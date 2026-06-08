<?php

namespace App\Http\Controllers\metrics;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BusinessGoalSetModel;
use App\Models\ManageTargetModel;
use App\Models\TransactionModel;
use App\Models\StaffModel;
use App\Models\CustomerModel;
use App\Models\IncentiveModel;
use App\Models\OldCustomerModel;
use App\Models\BranchModel;
use App\Models\GoalSetTeamModel;
use App\Models\AttendanceModel;
use Carbon\Carbon;
use App\Models\ManageCoursesModel;
use Illuminate\Support\Collection;

class ManageBhIncentive extends Controller
{
  public function index(Request $request)
  {
    $branch_id = $request->user()->branch_id;



    return view('content.metrics.manage_bh_incentive');
  }

  public function fetchIncentive(Request $request)
  {
    // Use input() to safely fetch values (or use $request->get() / query() for GET)
    $role_id   = $request->input('role_id');
    $month     = $request->input('month'); // you named it monthRaw, but using as month
    $year      = $request->input('year');
    $branchId  = $request->input('branch_id') ?? $request->user()->branch_id;

    // Fetch staff list
    $staff = StaffModel::select('za_staff.staff_name')
      ->where('za_staff.branch_id', $branchId)
      ->where('za_staff.role_id', $role_id)
      ->where('za_staff.status', 0)
      ->get();

    // Check if staff exists
    if ($staff->isEmpty()) {
      return response()->json([
        'status' => false,
        'message' => 'Staff not found'
      ]);
    }

    // Get unique staff names
    $staffNames = $staff->pluck('staff_name')->unique()->values();

    // Define incentive types
    $types = ['ECI', 'PI', 'BI', '10X', 'SI', 'LI'];

    // Return response
    return response()->json([
      'status' => true,
      'staff_names' => $staffNames,
      'types' => $types
    ]);
  }


  public function getIncentiveMetrics(Request $request)
  {
    $branchId = $request->branch_id ?? $request->user()->branch_id;
    $departmentId = $request->department_id;
    $role_id = $request->role_id;

    $year = $request->year;

    if (!empty($request->month)) {
      $request->month = is_numeric($request->month)
        ? (int) $request->month
        : (int) date('m', strtotime($request->month . ' 1'));
    }

    $month = $request->month;

    // Get the first and last date of the requested month
    $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

    $query = StaffModel::join('za_sub_department', 'za_sub_department.sno', '=', 'za_staff.sub_department_id')
      ->join('za_department', 'za_department.sno', '=', 'za_staff.department_id')
      ->join('za_branch', 'za_branch.sno', '=', 'za_staff.branch_id')
      ->where('za_staff.branch_id', $branchId);

    $query->where('za_staff.role_id', $role_id);

    $staffList = $query
      ->where('za_staff.status', 0)
      ->select(
        'za_staff.sno as staff_id',
        'za_staff.staff_name',
        'za_staff.role_id',
        'za_staff.department_id',
        'za_staff.staff_image',
        'za_staff.nick_name',
        'za_staff.branch_id',
        'za_branch.branch_name',
        'za_branch.franchise_name',
        'za_branch.cert_auth_sign',
        'za_staff.avaiable_points',
        'za_staff.per_hour_cost',
        'za_staff.basic_salary',
        'za_sub_department.sub_department_id',
        'za_sub_department.sub_department_name',
        'za_department.department_name'
      )
      ->get();

    $targets = ManageTargetModel::where('branch_target.branch_id', $branchId)
      ->whereMonth('branch_target.date', $request->month)
      ->whereYear('branch_target.date', $year)
      ->first();

    $goalset = GoalSetTeamModel::where('za_goal_set_team.branch_id', $branchId)
      ->where('za_goal_set_team.team_goal_month', $request->month)
      ->where('za_goal_set_team.team_goal_year', $year)
      ->where('za_goal_set_team.department_id', 1)
      ->first();

    $result = [];

    foreach ($staffList as $staff) {
      $incentive = IncentiveModel::where('role_id', $staff->role_id)
        ->where('branch_id', $branchId)
        ->whereMonth('incentive_month', $month)
        ->whereYear('incentive_month', $year)
        ->where('status', 0)
        ->first();

      $points = [];
      $calculations = [];
      $originalValues = [];

      // 1. 10X Award
      $result10xAward = $this->calculate10xAward($goalset, $targets, $startDate, $endDate, $branchId, $incentive, $year, $month);
      $points['10xaward'] = $result10xAward['value'];
      $calculations['10xaward'] = $result10xAward['calculation'];
      $originalValues['10xaward'] = $incentive->tenx_award ?? 0;

      // 2. Ceiling
      $resultCeiling = $this->calculateCeiling($targets, $startDate, $endDate, $branchId, $incentive, $year, $month);
      $points['ceiling'] = $resultCeiling['value'];
      $calculations['ceiling'] = $resultCeiling['calculation'];
      $originalValues['ceiling'] = $incentive->ceiling ?? 0;

      // 3. Quarterly
      $resultQuarterly = $this->calculateQuarterly($targets, $branchId, $incentive, $year, $month);
      $points['quarterly'] = $resultQuarterly['value'];
      $calculations['quarterly'] = $resultQuarterly['calculation'];
      $originalValues['quarterly'] = $incentive->quarterly ?? 0;

      // 4. 25%
      $result25 = $this->calculatePercentage($targets, $startDate, $endDate, $branchId, $incentive, $year, $month, 25);
      $points['twenty_five_percentage'] = $result25['value'];
      $calculations['twenty_five_percentage'] = $result25['calculation'];
      $originalValues['twenty_five_percentage'] = $incentive->twenty_five_percentage ?? 0;

      // 5. 50%
      $result50 = $this->calculatePercentage($targets, $startDate, $endDate, $branchId, $incentive, $year, $month, 50);
      $points['fifty_percentage'] = $result50['value'];
      $calculations['fifty_percentage'] = $result50['calculation'];
      $originalValues['fifty_percentage'] = $incentive->fifty_percentage ?? 0;

      // 6. 6 Month
      $result6Month = $this->calculateMonthBased($targets, $branchId, $incentive, $year, 6);
      $points['six_month'] = $result6Month['value'];
      $calculations['six_month'] = $result6Month['calculation'];
      $originalValues['six_month'] = $incentive->six_month ?? 0;

      // 7. 12 Month
      $result12Month = $this->calculateMonthBased($targets, $branchId, $incentive, $year, 12);
      $points['twelve_month'] = $result12Month['value'];
      $calculations['twelve_month'] = $result12Month['calculation'];
      $originalValues['twelve_month'] = $incentive->twelve_month ?? 0;

      // 8. 9 Month Add
      $result9MonthAdd = $this->calculateMonthAdd($targets, $branchId, $incentive, $year, 9);
      $points['nine_monthadd'] = $result9MonthAdd['value'];
      $calculations['nine_monthadd'] = $result9MonthAdd['calculation'];
      $originalValues['nine_monthadd'] = $incentive->nine_monthadd ?? 0;

      // 9. 12 Month Add
      $result12MonthAdd = $this->calculateMonthAdd($targets, $branchId, $incentive, $year, 12);
      $points['twelve_monthadd'] = $result12MonthAdd['value'];
      $calculations['twelve_monthadd'] = $result12MonthAdd['calculation'];
      $originalValues['twelve_monthadd'] = $incentive->twelve_monthadd ?? 0;

      // 10. Overall Year
      $resultOverallYear = $this->calculateOverallYear($targets, $branchId, $incentive, $year);
      $points['overall_year'] = $resultOverallYear['value'];
      $calculations['overall_year'] = $resultOverallYear['calculation'];
      $originalValues['overall_year'] = $incentive->overall_year ?? 0;

      // 11. Presale
      $resultPresale = $this->calculatePresale($targets, $branchId, $incentive, $year);
      $points['presale'] = $resultPresale['value'];
      $calculations['presale'] = $resultPresale['calculation'];
      $originalValues['presale'] = $incentive->presale ?? 0;

      // 12. Mega Bonus
      $resultMegaBonus = $this->calculateMegaBonus($targets, $branchId, $incentive, $year);
      $points['megabonus'] = $resultMegaBonus['value'];
      $calculations['megabonus'] = $resultMegaBonus['calculation'];
      $originalValues['megabonus'] = $incentive->megabonus ?? 0;

      // Calculate total earned and total original
      $conditionMet = $calculations['10xaward']['condition_met'] ?? false;

      if (!$conditionMet) {
        foreach ($points as $key => $val) {
          $points[$key] = 0;
        }

        foreach ($originalValues as $key => $val) {
          $originalValues[$key] = 0;
        }
      }

      $totalEarned = array_sum($points);
      $totalOriginal = array_sum($originalValues);
      $totalOriginal = array_sum($originalValues);

      $result[] = [
        'staff_id' => $staff->staff_id,
        'staff_name' => $staff->staff_name,
        'branch_name' => $staff->branch_name,
        'bh_signature' => $staff->cert_auth_sign,
        'staff_image' => $staff->staff_image,
        'role_id' => $staff->role_id,
        'nick_name' => $staff->nick_name,
        'sub_department_name' => $staff->sub_department_name,
        'department_name' => $staff->department_name,
        'incentive_value' => $points,
        'incentive_value_original' => $originalValues, // Default values from IncentiveModel
        'incentive_cal' => $calculations,
        'total_earned' => $totalEarned,
        'total_original' => $totalOriginal,
        'incentive_type' => [
          'presale' => 'gold',
        ]
      ];
    }

    return response()->json([
      'status' => 200,
      'data' => $result
    ]);
  }

  private function calculate10xAward($goalset, $targets, $startDate, $endDate, $branchId, $incentive, $year, $month)
  {
    $calculation = [
      'target' => 0,
      'actual' => 0,
      'unit' => '₹',
      'target_label' => 'Monthly Target & Registrations'
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    // overall
    $totalCredit = TransactionModel::where('branch_id', $branchId)
      ->whereBetween('transaction_date', [$startDate, $endDate])
      ->sum('credit');

    $presaleCount = CustomerModel::whereIn('status', [0, 6])
      ->where('post_sale_check', '!=', 1)
      ->where('branch_id', $branchId)
      ->whereYear('created_at', $year)
      ->whereMonth('created_at', $month)
      ->count();

    $postSaleCount = CustomerModel::join('za_training', 'za_customer.sno', '=', 'za_training.customer_id')
      ->whereIn('za_customer.status', [0, 6])
      ->where('za_customer.post_sale_check', 1)
      ->where('za_training.post_sale_check', 1)
      ->where('za_training.branch_id', $branchId)
      ->whereYear('za_training.created_at', $year)
      ->whereMonth('za_training.created_at', $month)
      ->distinct('za_training.sno')
      ->count('za_training.sno');

    // Get presale customers
    $presaleCustomers = CustomerModel::whereIn('status', [0, 6])
      ->where('post_sale_check', '!=', 1)
      ->where('branch_id', $branchId)
      ->whereYear('created_at', $year)
      ->whereMonth('created_at', $month)
      ->pluck('customer_id')
      ->toArray();

    // Get postsale customers
    $postsaleCustomers = CustomerModel::join('za_training', 'za_customer.sno', '=', 'za_training.customer_id')
      ->whereIn('za_customer.status', [0, 6])
      ->where('za_customer.post_sale_check', 1)
      ->where('za_training.post_sale_check', 1)
      ->where('za_training.branch_id', $branchId)
      ->whereYear('za_training.created_at', $year)
      ->whereMonth('za_training.created_at', $month)
      ->distinct('za_training.sno')
      ->pluck('za_training.sno')
      ->toArray();

    // Calculate for presale customers
    $presaleTotalOverallFees = 0;
    $presaleTotalCredit = 0;

    if (!empty($presaleCustomers)) {
      $presaleTotalOverallFees = CustomerModel::join('za_training', 'za_customer.sno', '=', 'za_training.customer_id')
        ->whereIn('za_customer.customer_id', $presaleCustomers)
        ->sum('za_training.overall_fees');

      $presaleTotalCredit = TransactionModel::where('branch_id', $branchId)
        ->whereIn('trans_source_id', $presaleCustomers)
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('credit');
    }

    $presaleABV = ($presaleCount > 0) ? $presaleTotalOverallFees / $presaleCount : 0;
    $presaleACV = ($presaleCount > 0) ? $presaleTotalCredit / $presaleCount : 0;

    // Calculate for postsale customers
    $postsaleTotalOverallFees = 0;
    $postsaleTotalCredit = 0;

    if (!empty($postsaleCustomers)) {
      $postsaleTotalOverallFees = CustomerModel::join('za_training', 'za_customer.sno', '=', 'za_training.customer_id')
        ->whereIn('za_training.sno', $postsaleCustomers)
        ->sum('za_training.overall_fees');

      $postsaleTotalCredit = TransactionModel::where('branch_id', $branchId)
        ->whereIn('training_id', $postsaleCustomers)
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('credit');
    }

    $postsaleABV = ($postSaleCount > 0) ? $postsaleTotalOverallFees / $postSaleCount : 0;
    $postsaleACV = ($postSaleCount > 0) ? $postsaleTotalCredit / $postSaleCount : 0;

    // Calculate for overall (combine presale + postsale)
    $totalCustomers = $presaleCount + $postSaleCount;
    $totalOverallFees = $presaleTotalOverallFees + $postsaleTotalOverallFees;
    $totalCreditAll = $presaleTotalCredit + $postsaleTotalCredit;

    $overallABV = ($totalCustomers > 0) ? $totalOverallFees / $totalCustomers : 0;
    $overallACV = ($totalCustomers > 0) ? $totalCreditAll / $totalCustomers : 0;

    $calculation = [
      'target' => [
        'monthly_target' => $targets->monthly_target ?? 0,
        'registrations' => $targets->no_of_registrations ?? 0,
        'post_sale' => $targets->post_sale ?? 0,
        'abv' => $goalset->abv_over_all ?? 0,
        'acv' => $goalset->acv_over_all ?? 0
      ],
      'actual' => [
        'total_credit' => round($totalCredit),
        'presale_count' => $presaleCount,
        'postsale_count' => $postSaleCount,
        'presale' => [
          'count' => $presaleCount,
          'abv_actual' => round($presaleABV, 2),
          'acv_actual' => round($presaleACV, 2),
          'total_overall_fees' => round($presaleTotalOverallFees, 2),
          'total_credit' => round($presaleTotalCredit, 2),
        ],
        'postsale' => [
          'count' => $postSaleCount,
          'abv_actual' => round($postsaleABV, 2),
          'acv_actual' => round($postsaleACV, 2),
          'total_overall_fees' => round($postsaleTotalOverallFees, 2),
          'total_credit' => round($postsaleTotalCredit, 2),
        ],
        'overall' => [
          'count' => $totalCustomers,
          'abv_actual' => round($overallABV, 2),
          'acv_actual' => round($overallACV, 2),
          'total_overall_fees' => round($totalOverallFees, 2),
          'total_credit' => round($totalCreditAll, 2),
        ],
      ],
      'unit' => '₹',
      'target_label' => 'Monthly Target & Registrations',
      'condition_met' => (
        $presaleCount >= ($targets->no_of_registrations ?? 0) &&
        $postSaleCount >= ($targets->post_sale ?? 0) &&
        $totalCredit >= ($targets->monthly_target ?? 0) &&
        $overallABV >= ($goalset->abv_over_all ?? 0) &&
        $overallACV >= ($goalset->acv_over_all ?? 0)
      )
    ];

    // Main condition
    if (
      $presaleCount >= ($targets->no_of_registrations ?? 0) &&
      $totalCredit >= ($targets->monthly_target ?? 0)
    ) {
      return ['value' => $incentive->tenx_award ?? 0, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }

  private function calculateCeiling($targets, $startDate, $endDate, $branchId, $incentive, $year, $month)
  {
    $calculation = [
      'target' => 0,
      'actual' => 0,
      'unit' => '₹',
      'target_label' => 'Monthly Target',
      'profit' => 0,
      'percentage' => 0
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $totalCredit = TransactionModel::where('branch_id', $branchId)
      ->whereBetween('transaction_date', [$startDate, $endDate])
      ->sum('credit');

    $target = $targets->monthly_target ?? 0;

    $calculation = [
      'target' => $target,
      'actual' => round($totalCredit),
      'unit' => '₹',
      'target_label' => 'Monthly Target',
      'profit' => max(0, $totalCredit - $target),
      'percentage' => $incentive->ceiling ?? 0
    ];

    // Only if target achieved
    if ($totalCredit > $target) {
      $profit = $totalCredit - $target;
      $percentage = $incentive->ceiling ?? 0;
      $percentageValue = ($profit * $percentage) / 100;
      $calculation['calculated_value'] = $percentageValue;
      return ['value' => $percentageValue, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }

  private function calculateQuarterly($targets, $branchId, $incentive, $year, $currentMonth)
  {
    $calculation = [
      'eligible_quarter' => null,
      'months' => [],
      'message' => 'Not eligible'
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    //  Define quarters
    $quarters = [
      1 => [4, 5, 6],   // Q1
      2 => [7, 8, 9],   // Q2
      3 => [10, 11, 12], // Q3
      4 => [1, 2, 3]    // Q4
    ];

    // Map reward month → previous quarter
    $rewardMap = [
      1  => 3,
      2  => 4,
      3  => 4,
      4  => 4,
      5  => 1,
      6  => 1,
      7  => 1,
      8  => 2,
      9  => 2,
      10 => 2,
      11 => 3,
      12 => 3,
    ];

    //  If current month is not reward month → no calculation
    if (!isset($rewardMap[$currentMonth])) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $quarterKey = $rewardMap[$currentMonth];
    $months = $quarters[$quarterKey];

    $allAchieved = true;
    $monthData = [];

    foreach ($months as $m) {

      // Handle year change for Q4 (Jan–Mar belongs to NEXT year logic)
      $loopYear = ($quarterKey == 4) ? $year : $year;

      $startDate = Carbon::create($loopYear, $m, 1)->startOfMonth();
      $endDate   = Carbon::create($loopYear, $m, 1)->endOfMonth();

      $totalCredit = TransactionModel::where('branch_id', $branchId)
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('credit');

      $targetsMonth = ManageTargetModel::where('branch_id', $branchId)
        ->whereMonth('date', $m)
        ->whereYear('date', $loopYear)
        ->first();

      $target = $targetsMonth->monthly_target ?? $targets->monthly_target ?? 0;

      $achieved = ($target > 0 && $totalCredit >= $target);

      if (!$achieved) {
        $allAchieved = false;
      }

      $monthData[] = [
        'month' => $m,
        'month_name' => Carbon::create($loopYear, $m, 1)->format('F'),
        'target' => $target,
        'actual' => round($totalCredit),
        'achieved' => $achieved
      ];
    }

    $calculation['eligible_quarter'] = "Q{$quarterKey}";
    $calculation['months'] = $monthData;

    // FINAL CHECK (strict 3/3)
    if ($allAchieved) {
      return [
        'value' => $incentive->quarterly ?? 0,
        'calculation' => $calculation
      ];
    }

    return [
      'value' => 0,
      'calculation' => $calculation
    ];
  }

  private function calculatePercentage($targets, $startDate, $endDate, $branchId, $incentive, $year, $month, $percent)
  {

    $calculation = [
      'target' => 0,
      'actual' => 0,
      'unit' => '₹',
      'target_label' => "{$percent}% Achievement",
      'achievement_percent' => 0,
      'applied_slab' => 'no_data'
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $totalCredit = TransactionModel::where('branch_id', $branchId)
      ->whereBetween('transaction_date', [$startDate, $endDate])
      ->sum('credit');

    $target = $targets->monthly_target ?? 0;

    if ($target <= 0) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $achievementPercent = ($totalCredit / $target) * 100;

    $profit = max(0, $totalCredit - $target);

    $calculation = [
      'target' => $target,
      'actual' => round($totalCredit),
      'unit' => '₹',
      'target_label' => "{$percent}% Achievement",
      'achievement_percent' => round($achievementPercent, 2),
      'profit' => $profit,
      'applied_slab' => 'target_not_achieved'
    ];

    //  Below 50% → nothing
    if ($achievementPercent < 50) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    //  BLOCK 25% if 50% slab achieved
    if ($percent == 25 && $achievementPercent >= 100) {
      $calculation['applied_slab'] = 'blocked_by_50_slab';
      return ['value' => 0, 'calculation' => $calculation];
    }

    //  25% slab (50% to <100%)
    if ($percent == 25 && $achievementPercent >= 50 && $achievementPercent < 100) {

      $percentage = $incentive->twenty_five_percentage ?? 0;

      $calculation['applied_slab'] = '25%_slab';
      $calculation['slab_percentage'] = $percentage;

      $value = ($profit * $percentage) / 100;

      $calculation['calculated_value'] = $value;

      return ['value' => $value, 'calculation' => $calculation];
    }

    //  50% slab (>=100%)
    if ($percent == 50 && $achievementPercent >= 100) {

      $percentage = $incentive->fifty_percentage ?? 0;

      $calculation['applied_slab'] = '50%_slab';
      $calculation['slab_percentage'] = $percentage;

      $value = ($profit * $percentage) / 100;

      $calculation['calculated_value'] = $value;

      return ['value' => $value, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }


  private function calculateMonthBased($targets, $branchId, $incentive, $year, $months)
  {
    $calculation = [
      'target_label' => "{$months} Month Achievement (FY Apr–Mar)", // here need FY Apr 2026 – Mar 2026
      'months_achieved' => 0,
      'required_months' => $months,
      'monthly_breakdown' => []
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $startMonth = 4; // April

    $achievedMonths = 0;
    $monthlyBreakdown = [];

    for ($i = 0; $i < 12; $i++) {

      $m = (($startMonth + $i - 1) % 12) + 1;
      $fyYear = ($m >= 4) ? $year : $year + 1;

      $startDate = Carbon::create($fyYear, $m, 1)->startOfMonth();
      $endDate   = Carbon::create($fyYear, $m, 1)->endOfMonth();

      $totalCredit = TransactionModel::where('branch_id', $branchId)
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('credit');

      $target = ManageTargetModel::where('branch_id', $branchId)
        ->whereMonth('date', $m)
        ->whereYear('date', $fyYear)
        ->first()
        ->monthly_target ?? 0;

      $achieved = ($target > 0 && $totalCredit >= $target);

      if ($achieved) {
        $achievedMonths++;
      }

      $monthlyBreakdown[] = [
        'month' => $m,
        'year' => $fyYear,
        'month_name' => Carbon::create($fyYear, $m, 1)->format('F'),
        'target' => $target,
        'actual' => round($totalCredit),
        'achieved' => $achieved
      ];
    }

    $calculation['months_achieved'] = $achievedMonths;
    $calculation['monthly_breakdown'] = $monthlyBreakdown;

    if ($achievedMonths >= 12) {
      return [
        'value' => $incentive->twelve_month ?? 0,
        'calculation' => $calculation
      ];
    }

    if ($achievedMonths >= 6) {
      return [
        'value' => $incentive->six_month ?? 0,
        'calculation' => $calculation
      ];
    }

    return [
      'value' => 0,
      'calculation' => $calculation
    ];
  }

  private function calculateMonthAdd($targets, $branchId, $incentive, $year, $months)
  {
    $calculation = [
      'target_label' => "{$months} Month Add-on (FY Apr–Mar)", // here need FY Apr 2026 – Mar 2026
      'months_achieved' => 0,
      'monthly_breakdown' => []
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $startMonth = 4; // April
    $achievedMonths = 0;
    $monthlyBreakdown = [];

    for ($i = 0; $i < 12; $i++) {

      $m = (($startMonth + $i - 1) % 12) + 1;

      // financial year mapping
      $fyYear = ($m >= 4) ? $year : $year + 1;

      $startDate = Carbon::create($fyYear, $m, 1)->startOfMonth();
      $endDate   = Carbon::create($fyYear, $m, 1)->endOfMonth();

      $totalCredit = TransactionModel::where('branch_id', $branchId)
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->sum('credit');

      $targetsMonth = ManageTargetModel::where('branch_id', $branchId)
        ->whereMonth('date', $m)
        ->whereYear('date', $fyYear)
        ->first();

      $target = $targetsMonth->monthly_target ?? 0;

      $achieved = ($target > 0 && $totalCredit >= $target);

      if ($achieved) {
        $achievedMonths++;
      }

      $monthlyBreakdown[] = [
        'month' => $m,
        'year' => $fyYear,
        'month_name' => Carbon::create($fyYear, $m, 1)->format('F'),
        'target' => $target,
        'actual' => round($totalCredit),
        'achieved' => $achieved
      ];
    }

    $calculation['months_achieved'] = $achievedMonths;
    $calculation['monthly_breakdown'] = $monthlyBreakdown;

    if ($achievedMonths >= 12) {
      return ['value' => $incentive->twelve_monthadd ?? 0, 'calculation' => $calculation];
    }

    if ($achievedMonths >= 9) {
      return ['value' => $incentive->nine_monthadd ?? 0, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }
  private function calculateOverallYear($targets, $branchId, $incentive, $year)
  {
    $calculation = [
      'target_label' => 'Overall Year (150%)',
      'target' => 0,
      'actual' => 0,
      'unit' => '₹',
      'achievement_percent' => 0
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    // Financial Year: April to March (Apr 1, YYYY to Mar 31, YYYY+1)
    $startMonth = 4; // April
    $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
    $endDate = Carbon::create($year + 1, 3, 31)->endOfMonth(); // March of next year

    // Total yearly credit for financial year
    $yearTotal = TransactionModel::where('branch_id', $branchId)
      ->whereBetween('transaction_date', [$startDate, $endDate])
      ->sum('credit');

    // Year target (monthly × 12)
    $yearTarget = ($targets->monthly_target ?? 0) * 12;

    $calculation = [
      'target_label' => 'Overall Year (150%)',
      'target' => $yearTarget,
      'actual' => round($yearTotal),
      'unit' => '₹',
      'achievement_percent' => $yearTarget > 0 ? round(($yearTotal / $yearTarget) * 100, 2) : 0
    ];

    if ($yearTarget <= 0) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $achievementPercent = ($yearTotal / $yearTarget) * 100;

    // Condition: 150% or more
    if ($achievementPercent >= 150) {
      return ['value' => $incentive->overall_year ?? 0, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }

  private function calculatePresale($targets, $branchId, $incentive, $year)
  {
    $calculation = [
      'target_label' => 'Presale (150% of Yearly Registrations)',
      'target' => 0,
      'actual' => 0,
      'unit' => 'registrations',
      'achievement_percent' => 0,
      'fy_range' => "Apr {$year} - Mar " . ($year + 1)
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    // Financial Year: April to March
    $startMonth = 4; // April
    $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
    $endDate = Carbon::create($year + 1, 3, 31)->endOfMonth();

    // Total yearly presale count for financial year
    $yearCount = CustomerModel::whereIn('status', [0, 6])
      ->where('post_sale_check', '!=', 1)
      ->where('branch_id', $branchId)
      ->whereBetween('created_at', [$startDate, $endDate])
      ->count();

    // Year target (monthly registrations × 12)
    $yearTarget = ($targets->no_of_registrations ?? 0) * 12;

    $calculation = [
      'target_label' => 'Presale (150% of Yearly Registrations)',
      'target' => $yearTarget,
      'actual' => $yearCount,
      'unit' => 'registrations',
      'achievement_percent' => $yearTarget > 0 ? round(($yearCount / $yearTarget) * 100, 2) : 0,
      'fy_range' => "Apr {$year} - Mar " . ($year + 1)
    ];

    if ($yearTarget <= 0) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $achievementPercent = ($yearCount / $yearTarget) * 100;

    // Condition: 150% or more
    if ($achievementPercent >= 150) {
      return ['value' => $incentive->presale ?? 0, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }

  private function calculateMegaBonus($targets, $branchId, $incentive, $year)
  {
    $calculation = [
      'target_label' => 'Mega Bonus (200% of Yearly Target)',
      'target' => 0,
      'actual' => 0,
      'unit' => '₹',
      'achievement_percent' => 0,
      'fy_range' => "Apr {$year} - Mar " . ($year + 1)
    ];

    if (!$targets || !$incentive) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    // Financial Year: April to March
    $startMonth = 4; // April
    $startDate = Carbon::create($year, $startMonth, 1)->startOfMonth();
    $endDate = Carbon::create($year + 1, 3, 31)->endOfMonth();

    // Total yearly credit for financial year
    $yearTotal = TransactionModel::where('branch_id', $branchId)
      ->whereBetween('transaction_date', [$startDate, $endDate])
      ->sum('credit');

    // Year target
    $yearTarget = ($targets->monthly_target ?? 0) * 12;

    $calculation = [
      'target_label' => 'Mega Bonus (200% of Yearly Target)',
      'target' => $yearTarget,
      'actual' => round($yearTotal),
      'unit' => '₹',
      'achievement_percent' => $yearTarget > 0 ? round(($yearTotal / $yearTarget) * 100, 2) : 0,
      'fy_range' => "Apr {$year} - Mar " . ($year + 1)
    ];

    if ($yearTarget <= 0) {
      return ['value' => 0, 'calculation' => $calculation];
    }

    $achievementPercent = ($yearTotal / $yearTarget) * 100;

    // 200% condition (DOUBLE target)
    if ($achievementPercent >= 200) {
      return ['value' => $incentive->megabonus ?? 0, 'calculation' => $calculation];
    }

    return ['value' => 0, 'calculation' => $calculation];
  }
}
