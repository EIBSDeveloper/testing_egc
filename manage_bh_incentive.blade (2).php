@extends('layouts.contentNavbarLayout')

@section('title', 'Manage BH Incentive')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/forms_date_time_pickers.js'])
@endsection

@section('content')

<style>
    .incentive-row {
        transition: 0.2s;
    }
    .incentive-row:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }
    .incentive-row.locked {
        background: #f1f3f5;
        cursor: pointer;
        opacity: 0.7;
    }
    .accordion-button {
        background: linear-gradient(90deg, #f8f9fa, #ffffff);
    }
    /* Gamified UI Enhancements */
    .staff-avatar {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .total-reward-badge {
        background: linear-gradient(135deg, #FFD700, #FFC107, #FF8F00);
        padding: 8px 20px;
        border-radius: 40px;
        font-weight: bold;
        color: #2C1810;
        font-size: 1.2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 215, 0, 0.5);
        display: inline-block;
        min-width: 120px;
        text-align: center;
    }
    .incentive-table-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s;
    }
    .incentive-table-row.locked {
        background: #f8f9fa;
        opacity: 0.75;
        cursor: pointer;
        border-left: 4px solid #e92c12;
    }
    .incentive-table-row.unlocked {
        background: linear-gradient(90deg, #ffffff, #f0fff4);
        border-left: 4px solid #28a745;
    }
    .calculation-part {
        background: #f1f3f5;
        border-radius: 30px;
        padding: 4px 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-icon {
        width: 36px;
        text-align: center;
        font-size: 1.4rem;
    }
    .award-value {
        font-weight: 800;
        font-size: 1.1rem;
        min-width: 90px;
        text-align: right;
    }
    .progress-threshold {
        width: 80px;
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-threshold-fill {
        height: 100%;
        background: #28a745;
        width: 0%;
    }
    @media (max-width: 768px) {
        .incentive-table-row {
            flex-wrap: wrap;
            gap: 10px;
        }
        .calculation-part {
            order: 3;
            width: 100%;
        }
    }

    /* Gold coin styling */
    .mdi-gold {
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));
        animation: subtle-glow 2s ease-in-out infinite;
    }

    @keyframes subtle-glow {
        0%, 100% {
            filter: drop-shadow(0 0 2px rgba(255, 215, 0, 0.3));
        }
        50% {
            filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.6));
        }
    }

    .bg-warning.bg-opacity-15 {
        background-color: rgba(255, 193, 7, 0.15) !important;
    }
</style>

<div class="card">
    <div class="card-header border-bottom pb-1">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h5 class="mb-1"> Manage BH Incentive</h5>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{url('/dashboards')}}" class="d-flex align-items-center"><i class="mdi mdi-home text-body fs-4"></i></a>
                        </li>
                        <span class="text-dark opacity-75 me-1 ms-1">
                            <i class="mdi mdi-chevron-double-right fs-4"></i>
                        </span>
                        <li class="breadcrumb-item">
                            <a href="javascript:;" class="d-flex align-items-center">Metrics Management</a>
                        </li>
                    </ol>
                </nav>
            </div>
           <div class="col-lg-6 text-end">
                <label class="mt-1 fs-3 bg-label-info px-2 py-2 rounded" id="branch_name_lab">{{ $branch_common->branch_name??'' }}</label>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3 mb-3">
                <label class="text-dark mb-1 fs-6 fw-semibold">Branch<span class="text-danger">*</span></label>
                @php
                $helper = new \App\Helpers\Helpers();
                $selectedBranch = request()->query('branch_id') ?? auth()->user()->branch_id;
                @endphp

                @if(isset($role_permission->manage_branch))
                    @if($role_permission->manage_branch == 2)
                        @php
                            $user_id = auth()->user()->user_id;
                            $branch_data = $helper->get_branch_control_list($user_id);
                            $branches = $branch_data['branches'];
                            $franchises = $branch_data['franchises'];
                        @endphp
                        <select name="branch_id" class="select3 form-select" id="branchSelectsgoal" onchange="branch_change()">
                            @if($user_id == 0)
                            <option value="0" selected>All Branches</option>
                            @endif
                            @if ($branches->isNotEmpty())
                                <optgroup label="Branches">
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->sno }}" {{ $selectedBranch == $branch->sno ? 'selected' : '' }}>
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if ($franchises->isNotEmpty())
                                <optgroup label="Franchises">
                                    @foreach ($franchises as $franchise)
                                        <option value="{{ $franchise->sno }}" {{ $selectedBranch == $franchise->sno ? 'selected' : '' }}>
                                            {{ $franchise->franchise_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    @elseif($role_permission->manage_branch == 1)
                        @if($branch_common)
                          <div class="d-flex align-items-center mt-2">
                              <i class="mdi mdi-source-branch text-primary fs-3 me-2"></i>
                              <div>
                                  <h5 class="text-primary mb-0">{{ $branch_common->city_name }}</h5>
                                  <small class="text-truncate max-w-10px text-black" title="{{ $branch_common->branch_name }}">{{ $branch_common->branch_name }}</small>
                                  <input type="hidden" name="branch_id" id="branchSelectsgoal" value="{{$branch_common->sno}}">
                              </div>
                          </div>
                        @endif
                    @endif
                @endif
            </div>

            <div class="col-lg-3 mb-3">
                <label class="text-dark mb-1 fs-5 fw-semibold">Role<span class="text-danger">*</span></label>
                <select id="role_filter" name="role_id" class="select3 form-select" onchange="deptChange()">
                    <option value="18" selected>BH - Business Head</option>
                </select>
            </div>
            <div class="col-lg-4 mb-3">
              <div class="nav-align-top my-4">
                  <ul class="nav nav-pills justify-content-start" role="tablist">
                      <li class="nav-item me-1">
                          <a class="nav-link px-3" href="#" id="prevMonthBtn">
                              <div class="text-center">
                                  <span class="mdi mdi-arrow-left-circle-outline fs-2"></span>
                              </div>
                          </a>
                      </li>
                      <li class="nav-item me-1">
                          <a class="nav-link active px-3 border border-dashed border-info" href="#">
                              <div class="text-center">
                                  <span class="fs-6 fw-semibold text-capitalize" id="selectedMonth">
                                      {{ \Carbon\Carbon::now()->format('F') }}
                                  </span>
                                  <div class="d-block">
                                      <span class="fw-semibold fs-8" id="selectedYear">
                                          ({{ \Carbon\Carbon::now()->year }})
                                      </span>
                                  </div>
                              </div>
                          </a>
                      </li>
                      <li class="nav-item me-1">
                          <a class="nav-link px-3" href="#" id="nextMonthBtn">
                              <div class="text-center">
                                  <span class="mdi mdi-arrow-right-circle-outline fs-2"></span>
                              </div>
                          </a>
                      </li>
                  </ul>
              </div>
            </div>
        </div>
        <div class="row" id="incentiveaccordion"></div>
        <div id="incentiveLoader" class="text-center py-4" style="display: none;">
            <div class="progress" style="height: 20px; width: 50%; margin: auto;">
                <div id="incentiveProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info rounded-pill"
                    role="progressbar"
                    style="width: 0%"
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    Loading...
                </div>
            </div>
            <div id="incentiveLoadingText" class="mt-2 fw-semibold text-primary">Loading Incentives...</div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
<script>
    var currentMonth = new Date().getMonth() + 1;
    var currentYear = new Date().getFullYear();

    // Mapping for labels and their calculation KPI
    var incentiveKPIMap = {
        '10xaward': { label: '10X Award', targetLabel: 'Revenue Target', unit: '₹' },
        'ceiling': { label: 'Ceiling', targetLabel: 'Achievement %', unit: '%' },
        'quarterly': { label: 'Quarterly', targetLabel: 'Quarterly Goal', unit: '₹' },
        'twenty_five_percentage': { label: '25%', targetLabel: 'Milestone 25%', unit: '%' },
        'fifty_percentage': { label: '50%', targetLabel: 'Milestone 50%', unit: '%' },
        'six_month': { label: '6 Month', targetLabel: '6 Month Retention', unit: 'days' },
        'twelve_month': { label: '12 Month', targetLabel: '12 Month Retention', unit: 'days' },
        'nine_monthadd': { label: '9 Month Add', targetLabel: 'Additional 9M', unit: 'months' },
        'twelve_monthadd': { label: '12 Month Add', targetLabel: 'Additional 12M', unit: 'months' },
        'overall_year': { label: 'Overall Year', targetLabel: 'Annual Target (Apr-Mar)', unit: '%' },
        'presale': { label: 'Presale', targetLabel: 'Presale Deals (Apr-Mar)', unit: 'units' },
        'megabonus': { label: 'Mega Bonus', targetLabel: 'Mega Factor (Apr-Mar)', unit: 'x' }
    };

    function startLoader() {
        let progress = 0;
        $("#incentiveProgressBar").css("width", "0%").attr("aria-valuenow", 0);
        $("#incentiveLoadingText").text("Loading Incentives...");

        window.loaderInterval = setInterval(() => {
            if (progress < 90) { // stop at 90% until complete
                progress += Math.random() * 10;
                progress = Math.min(progress, 90);

                $("#incentiveProgressBar")
                    .css("width", progress + "%")
                    .attr("aria-valuenow", progress);
            }
        }, 300);
    }

    function stopLoader() {
        clearInterval(window.loaderInterval);

        $("#incentiveProgressBar")
            .css("width", "100%")
            .attr("aria-valuenow", 100);

        $("#incentiveLoadingText").text("Completed ✅");

        setTimeout(() => {
            $("#incentiveLoader").hide();
        }, 500);
    }

    // Gold-based incentives
    var goldBasedIncentives = ['presale'];

    function updateMonthDisplay() {
        let monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        document.getElementById("selectedMonth").textContent = monthNames[currentMonth - 1];
        document.getElementById("selectedYear").textContent = "(" + currentYear + ")";
        fetchIncentivesList();
    }

    document.getElementById("prevMonthBtn").addEventListener("click", function (e) {
        e.preventDefault();
        if (currentMonth === 1) {
            currentMonth = 12;
            currentYear--;
        } else {
            currentMonth--;
        }
        updateMonthDisplay();
    });
    document.getElementById("nextMonthBtn").addEventListener("click", function (e) {
        e.preventDefault();
        if (currentMonth === 12) {
            currentMonth = 1;
            currentYear++;
        } else {
            currentMonth++;
        }
        updateMonthDisplay();
    });

    // Direct PDF download function using html2pdf or simple blob with proper HTML
    function downloadPDF(content, filename) {
        // Create a hidden iframe for better PDF generation
        const blob = new Blob([content], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename.replace('.pdf', '.html');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Show info message
        setTimeout(() => {
            URL.revokeObjectURL(url);
            alert('File downloaded as HTML. Please open in browser and use Ctrl+P to save as PDF for better formatting.');
        }, 100);
    }

    // Clean text helper function
    function cleanText(text) {
        if (!text) return '-';
        return String(text).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Format award value for PDF
    function formatAwardValueForPDF(originalValue, isGold) {
        if (typeof originalValue === 'string' && isNaN(originalValue)) {
            return originalValue;
        } else if (isGold) {
            return `${originalValue} gm Gold`;
        } else {
            return `₹ ${Number(originalValue).toLocaleString('en-IN')}`;
        }
    }


    // Download single staff PDF using jsPDF
    function downloadSinglePDF(staffIndex) {
        if (!window.incentiveData || !window.incentiveData[staffIndex]) return;

        const staff = window.incentiveData[staffIndex];
        const points = staff.incentive_value || {};
        const originalValues = staff.incentive_value_original || {};
        const calDetails = staff.incentive_cal || {};
        const incentiveType = staff.incentive_type || {};
        const monthYear = $('#selectedMonth').text() + ' ' + $('#selectedYear').text();
        const branchName = $('#branch_name_lab').text() || 'Branch Report';

        // Calculate total earned
        let totalEarned = Object.values(points).reduce((a, b) => a + Number(b), 0);

        // Prepare table rows
        let rows = [];
        let orderedKeys = ['10xaward', 'ceiling', 'quarterly', 'twenty_five_percentage', 'fifty_percentage', 'six_month', 'twelve_month', 'nine_monthadd', 'twelve_monthadd', 'overall_year', 'presale', 'megabonus'];

        orderedKeys.forEach(key => {
            let earnedValue = Number(points[key] || 0);
            let originalValue = originalValues[key];
            let isUnlocked = earnedValue > 0;
            let isGold = goldBasedIncentives.includes(key) || incentiveType[key] === 'gold';
            let labelInfo = incentiveKPIMap[key] || { label: key.replace(/_/g, ' '), targetLabel: 'KPI', unit: '' };
            let calcData = calDetails[key] || null;

            let targetDisplay = getTargetValueForPDF(key, calcData);
            let actualDisplay = getActualValueForPDF(key, calcData);
            let awardDisplay = formatAwardValueForPDF(originalValue, isGold);
            let statusDisplay = isUnlocked ? 'Achieved ✓' : 'Not Achieved';

            rows.push([
                labelInfo.label,
                targetDisplay,
                actualDisplay,
                statusDisplay,
                awardDisplay
            ]);
        });

        // Add total row
        rows.push(["", "", "", "TOTAL EARNED", `₹ ${totalEarned.toLocaleString('en-IN')}`]);

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Page dimensions
        let pageWidth = doc.internal.pageSize.getWidth();
        let pageHeight = doc.internal.pageSize.getHeight();

        // Background image (optional - remove if causing issues)
        try {
            let bgUrl = "https://erp.elysium.academy/assets/eapl_images/bg_image/guide_bg.jpg";
            doc.setGState(new doc.GState({ opacity: 0.1 }));
            doc.addImage(bgUrl, "PNG", 0, 0, pageWidth, pageHeight);
            doc.setGState(new doc.GState({ opacity: 1 }));
        } catch(e) {
            // Skip background if error
        }

        // Company Logo
        try {
            let logoUrl = "https://erp.elysium.academy/assets/eapl_images/ea_full_logo.png";
            doc.addImage(logoUrl, "PNG", 160, 10, 45, 20);
        } catch(e) {
            // Skip logo if error
        }

        // Title
        doc.setFontSize(18);
        doc.setFont("helvetica", "bold");
        doc.text(`BH Incentive Report - ${monthYear}`, 14, 25);

        // Subtitle
        doc.setFontSize(10);
        doc.setFont("helvetica", "normal");
        doc.text(`Branch: ${branchName}`, 14, 35);

        // Staff Profile Section
        let profileImg = staff.staff_image ? `https://erp.elysium.academy/staff_images/${staff.staff_image}` : '';
        let imgX = 14;
        let imgY = 50;
        let imgSize = 30;

        // Add profile image if exists
        if (profileImg) {
            try {
                doc.addImage(profileImg, "JPEG", imgX, imgY, imgSize, imgSize, "", "FAST");
            } catch(e) {
                // Draw circle placeholder
                doc.setDrawColor(200);
                doc.circle(imgX + imgSize/2, imgY + imgSize/2, imgSize/2, "S");
            }
        } else {
            // Draw circle placeholder
            doc.setDrawColor(200);
            doc.circle(imgX + imgSize/2, imgY + imgSize/2, imgSize/2, "S");
        }

        // Staff details
        let textXLabel = imgX + imgSize + 5;
        let textXValue = imgX + imgSize + 45;
        let lineHeight = 7;
        let textY = imgY + 8;

        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.text("Staff Details", textXLabel, textY - 3);
        doc.setFont("helvetica", "normal");

        doc.text("Name:", textXLabel, textY);
        doc.text(staff.staff_name || '-', textXValue, textY);

        doc.text("Department:", textXLabel, textY + lineHeight);
        doc.text(staff.department_name || '-', textXValue, textY + lineHeight);

        doc.text("Role:", textXLabel, textY + (lineHeight * 2));
        doc.text(staff.sub_department_name || 'BH', textXValue, textY + (lineHeight * 2));

        doc.text("Total Earned:", textXLabel, textY + (lineHeight * 3));
        doc.text(`₹ ${totalEarned.toLocaleString('en-IN')}`, textXValue, textY + (lineHeight * 3));

        // Generate table
        doc.autoTable({
            head: [["Incentive", "Target", "Actual", "Status", "Award"]],
            body: rows,
            startY: 100,
            theme: "grid",
            headStyles: {
                fillColor: [52, 152, 219],
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center'
            },
            styles: {
                fontSize: 9,
                valign: "middle",
                overflow: "linebreak",
                cellPadding: 3,
                font: "helvetica"
            },
            columnStyles: {
                0: { cellWidth: 35 },
                1: { cellWidth: 40 },
                2: { cellWidth: 55 },
                3: { cellWidth: 25, halign: "center" },
                4: { cellWidth: 35, halign: "right" }
            },
            didParseCell: function (data) {
                // Color status cells
                if (data.section === 'body' && data.column.index === 3) {
                    if (data.cell.raw === "Achieved ✓") {
                        data.cell.styles.textColor = [40, 167, 69];
                    } else if (data.cell.raw === "Not Achieved") {
                        data.cell.styles.textColor = [220, 53, 69];
                    }
                }
                // Highlight total row
                if (data.section === 'body' && data.row.index === rows.length - 1) {
                    data.cell.styles.fillColor = [240, 240, 240];
                    data.cell.styles.fontStyle = 'bold';
                }
            }
        });

        // Footer
        let finalY = doc.lastAutoTable.finalY + 10;
        if (finalY > pageHeight - 30) {
            doc.addPage();
            finalY = 20;
        }

        doc.setFontSize(8);
        doc.text("This is a system-generated report. For any discrepancies, please contact HR/Admin.", 14, finalY);

        // Signature
        try {
            let signatureUrl = staff.bh_signature || "https://erp.elysium.academy/assets/eapl_images/signature_placeholder.png";
            doc.addImage(signatureUrl, "PNG", 140, finalY + 5, 50, 15);
            doc.text("Business Head", 155, finalY + 25);
        } catch(e) {
            doc.text("Business Head Signature", 150, finalY + 15);
        }

        // Save PDF
        doc.save(`BH_Incentive_${staff.staff_name.replace(/\s/g, '_')}_${monthYear.replace(/\s/g, '_')}.pdf`);
    }

    // Download all staff PDF
    function downloadAllPDF() {
        if (!window.incentiveData || window.incentiveData.length === 0) return;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let pageWidth = doc.internal.pageSize.getWidth();
        let pageHeight = doc.internal.pageSize.getHeight();
        const monthYear = $('#selectedMonth').text() + ' ' + $('#selectedYear').text();
        const branchName = $('#branch_name_lab').text() || 'Branch Report';

        window.incentiveData.forEach((staff, idx) => {
            if (idx > 0) {
                doc.addPage();
            }

            const points = staff.incentive_value || {};
            const originalValues = staff.incentive_value_original || {};
            const calDetails = staff.incentive_cal || {};
            const incentiveType = staff.incentive_type || {};

            let totalEarned = Object.values(points).reduce((a, b) => a + Number(b), 0);

            // Background
            try {
                let bgUrl = "https://erp.elysium.academy/assets/eapl_images/bg_image/guide_bg.jpg";
                doc.setGState(new doc.GState({ opacity: 0.1 }));
                doc.addImage(bgUrl, "PNG", 0, 0, pageWidth, pageHeight);
                doc.setGState(new doc.GState({ opacity: 1 }));
            } catch(e) {}

            // Logo
            try {
                let logoUrl = "https://erp.elysium.academy/assets/eapl_images/ea_full_logo.png";
                doc.addImage(logoUrl, "PNG", 160, 10, 45, 20);
            } catch(e) {}

            // Title
            doc.setFontSize(18);
            doc.setFont("helvetica", "bold");
            doc.text(`BH Incentive Report - ${monthYear}`, 14, 25);
            doc.setFontSize(10);
            doc.text(`Branch: ${branchName}`, 14, 35);

            // Staff header
            doc.setFontSize(14);
            doc.setFont("helvetica", "bold");
            doc.text(staff.staff_name || 'Staff', 14, 50);
            doc.setFontSize(10);
            doc.setFont("helvetica", "normal");
            doc.text(`${staff.department_name || ''} | ${staff.sub_department_name || 'BH'}`, 14, 58);
            doc.text(`Total Earned: ₹ ${totalEarned.toLocaleString('en-IN')}`, 14, 66);

            // Table rows
            let rows = [];
            let orderedKeys = ['10xaward', 'ceiling', 'quarterly', 'twenty_five_percentage', 'fifty_percentage', 'six_month', 'twelve_month', 'nine_monthadd', 'twelve_monthadd', 'overall_year', 'presale', 'megabonus'];

            orderedKeys.forEach(key => {
                let earnedValue = Number(points[key] || 0);
                let originalValue = originalValues[key];
                let isUnlocked = earnedValue > 0;
                let isGold = goldBasedIncentives.includes(key) || incentiveType[key] === 'gold';
                let labelInfo = incentiveKPIMap[key] || { label: key.replace(/_/g, ' '), targetLabel: 'KPI', unit: '' };
                let calcData = calDetails[key] || null;

                rows.push([
                    labelInfo.label,
                    getTargetValueForPDF(key, calcData),
                    getActualValueForPDF(key, calcData),
                    isUnlocked ? 'Achieved ✓' : 'Not Achieved',
                    formatAwardValueForPDF(originalValue, isGold)
                ]);
            });

            rows.push(["", "", "", "TOTAL EARNED", `₹ ${totalEarned.toLocaleString('en-IN')}`]);

            doc.autoTable({
                head: [["Incentive", "Target", "Actual", "Status", "Award"]],
                body: rows,
                startY: 75,
                theme: "grid",
                headStyles: { fillColor: [52, 152, 219], textColor: 255, fontStyle: 'bold', halign: 'center' },
                styles: { fontSize: 9, valign: "middle", overflow: "linebreak", cellPadding: 3 },
                columnStyles: {
                    0: { cellWidth: 35 },
                    1: { cellWidth: 40 },
                    2: { cellWidth: 55 },
                    3: { cellWidth: 25, halign: "center" },
                    4: { cellWidth: 35, halign: "right" }
                },
                didParseCell: function (data) {
                    if (data.section === 'body' && data.column.index === 3) {
                        if (data.cell.raw === "Achieved ✓") data.cell.styles.textColor = [40, 167, 69];
                        else if (data.cell.raw === "Not Achieved") data.cell.styles.textColor = [220, 53, 69];
                    }
                    if (data.section === 'body' && data.row.index === rows.length - 1) {
                        data.cell.styles.fillColor = [240, 240, 240];
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
            });
        });

        doc.save(`BH_Incentive_Report_${monthYear.replace(/\s/g, '_')}.pdf`);
    }

    function getTargetValueForPDF(key, calcData) {
        if (!calcData) return '-';
        if (key === '10xaward' && calcData.target) {
            return `₹${(calcData.target.monthly_target || 0).toLocaleString()} | ${(calcData.target.registrations || 0)} reg`;
        }
        if (key === 'quarterly' && calcData.eligible_quarter) return calcData.eligible_quarter;
        if ((key === 'six_month' || key === 'twelve_month' || key === 'nine_monthadd' || key === 'twelve_monthadd') && calcData.required_months) {
            return `${calcData.required_months} Months (Apr-Mar)`;
        }
        if (calcData.target !== undefined) {
            let unit = calcData.unit || (key === 'presale' ? 'registrations' : '₹');
            return `${typeof calcData.target === 'number' ? calcData.target.toLocaleString() : calcData.target} ${unit}`;
        }
        return '-';
    }

    function getActualValueForPDF(key, calcData) {
        if (!calcData) return '-';
        if (key === '10xaward' && calcData.actual) {
            let credit = calcData.actual.total_credit || 0;
            let preCount = calcData.actual.presale_count || 0;
            let postCount = calcData.actual.postsale_count || 0;
            let abv = calcData.actual.overall?.abv_actual || 0;
            let acv = calcData.actual.overall?.acv_actual || 0;
            return `₹${credit.toLocaleString()} | Pre:${preCount} | Post:${postCount} | ABV:₹${abv} | ACV:₹${acv}`;
        }
        if (key === 'ceiling' && calcData.actual !== undefined) {
            return `${calcData.actual.toLocaleString()} (Profit: ${(calcData.profit || 0).toLocaleString()})`;
        }
        if (key === 'quarterly' && calcData.months) {
            let months = calcData.months || [];
            let achieved = months.filter(m => m.achieved).length;
            return `${achieved}/${months.length} months`;
        }
        if ((key === 'twenty_five_percentage' || key === 'fifty_percentage') && calcData.achievement_percent !== undefined) {
            return `${(calcData.actual || 0).toLocaleString()} (${calcData.achievement_percent}%)`;
        }
        if ((key === 'six_month' || key === 'twelve_month' || key === 'nine_monthadd' || key === 'twelve_monthadd') && calcData.months_achieved !== undefined) {
            return `${calcData.months_achieved}/${calcData.required_months} months`;
        }
        if ((key === 'overall_year' || key === 'presale' || key === 'megabonus') && calcData.achievement_percent !== undefined) {
            return `${(calcData.actual || 0).toLocaleString()} (${calcData.achievement_percent}%)`;
        }
        if (calcData.actual !== undefined) return `${calcData.actual.toLocaleString()}`;
        return '-';
    }

    function fetchIncentivesList() {
        let departmentId = $("#department_filter").val();
        let role_id = $("#role_filter").val();
        let month = $("#selectedMonth").text().trim();
        let year = $("#selectedYear").text().trim().replace(/[()]/g, '');
        let branch_id = $("#branchSelectsgoal").val();

        $("#incentiveLoader").show();
        $("#incentiveaccordion").hide();
        startLoader();

        $.ajax({
            url: "/get-bh-incentive",
            type: "GET",
            data: { branch_id: branch_id, month: month, year: year, department_id: departmentId, role_id: role_id },
            success: function (response) {
                if (response.status === 200 && response.data.length > 0) {
                    window.incentiveData = response.data;
                    renderIncentiveUI(response.data);
                } else {
                    $("#incentiveaccordion").html('<div class="text-center text-black py-4"><i class="mdi mdi-emoticon-sad-outline fs-1"></i><p>No incentive data available for selected criteria.</p></div>');
                }
            },
            error: function (xhr) {
                console.error("Error fetching incentives:", xhr);
                $("#incentiveaccordion").html('<div class="text-center text-danger py-4">Failed to load data. Please try again.</div>');
            },
            complete: function () {
                stopLoader();
                $("#incentiveLoader").hide();
                $("#incentiveaccordion").show();
            }
        });
    }

    function renderIncentiveUI(data) {
        let html = '';
        data.forEach((staff, index) => {
            let total = Number(staff.total_earned || 0);
            let avatarUrl = (staff.staff_image && staff.staff_image !== '') ? 'https://erp.elysium.academy/staff_images/' + staff.staff_image : 'assets/eapl_images/user_1.png';
            let borderClass = total > 0 ? 'border border-success border-opacity-25' : 'border border-danger border-opacity-25';

            html += `
            <div class="col-12 mb-3">
                <div class="accordion" id="acc_${index}">
                    <div class="accordion-item shadow-sm border-0 rounded-4 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed d-flex justify-content-between align-items-center ${borderClass}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_${index}">
                                <div class="d-flex align-items-center gap-3 w-100 flex-wrap flex-md-nowrap">
                                    <img src="${avatarUrl}" class="staff-avatar" alt="staff">
                                    <div><div class="fw-bold">${escapeHtml(staff.staff_name)}</div><small class="text-black">${staff.department_name ?? ''} | ${staff.sub_department_name ?? ''}</small></div>
                                    <div class="ms-auto"><span class="total-reward-badge">₹ ${total.toLocaleString()}</span></div>

                                </div>
                            </button>
                        </h2>
                        <div id="collapse_${index}" class="accordion-collapse collapse">
                            <div class="accordion-body p-0">${renderIncentiveRows(staff)}</div>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        html = `<div class="text-end mb-3"></div>${html}`;
        $("#incentiveaccordion").html(html);
    }

  function renderIncentiveRows(staff) {
      let points = staff.incentive_value || {};
      let originalValues = staff.incentive_value_original || {};
      let calDetails = staff.incentive_cal || {};
      let incentiveType = staff.incentive_type || {};
      console.log(incentiveType);

      let html = `
      <style>
          .custom-incentive-table { border: 2px solid #000 !important; border-collapse: collapse !important; width: 100%; }
          .custom-incentive-table th, .custom-incentive-table td { border: 2px solid #000 !important; padding: 12px; vertical-align: middle; }
          .bg-success-cell { background-color: #d4edda !important; }
          .bg-danger-cell { background-color: #f8d7da !important; }
          .bg-warning-cell { background-color: #fff3cd !important; }
          .progress-bar-custom { height: 12px; background: #e9ecef; border-radius: 6px; overflow: hidden; width: 100%; margin: 5px 0; }
          .progress-fill-custom { height: 100%; transition: width 0.5s ease; border-radius: 6px; }
          .month-icon { font-size: 22px; margin: 0 2px; display: inline-block; }
          .month-icon.future { color: #17a2b8; }
          .month-icon.achieved { color: #28a745; }
          .month-icon.failed { color: #dc3545; }
          .award-text { font-weight: bold; }
          .award-text.trip { color: #9b59b6; }
          .award-text.iphone { color: #e67e22; }
      </style>
      <table class="custom-incentive-table">
          <thead class="bg-primary text-white"><tr><th>Incentive</th><th class="text-center">Target</th><th class="text-center">Actual</th><th class="text-center">Status</th><th class="text-end">Award</th></tr></thead>
          <tbody>`;

      let orderedKeys = ['10xaward', 'ceiling', 'quarterly', 'twenty_five_percentage', 'fifty_percentage', 'six_month', 'twelve_month', 'nine_monthadd', 'twelve_monthadd', 'overall_year', 'presale', 'megabonus'];
      let percentageKeys = ['fifty_percentage', 'twenty_five_percentage', 'ceiling'];
      orderedKeys.forEach(key => {
          let earnedValue = Number(points[key] || 0);
          let originalValue = originalValues[key];
          let isUnlocked;

          //  Special condition for 10xaward
          // if (key === '10xaward') {
              isUnlocked = calDetails[key]?.condition_met === true;
          // } else {
          //     isUnlocked = earnedValue > 0;
          // }
          let isGold = goldBasedIncentives.includes(key) || incentiveType[key] === 'gold';
          let labelInfo = incentiveKPIMap[key] || { label: key.replace(/_/g, ' '), targetLabel: 'KPI', unit: '' };
          let calcData = calDetails[key] || null;
          let targetValue = getTargetValue(key, calcData);
          let actualResult = getActualWithDetails(key, calcData);
          let actualHtml = actualResult.html;
          let actualBgClass = actualResult.bgClass;

          // let statusIcon = isUnlocked ? '<i class="mdi mdi-check-circle text-success fs-3" title="Achieved"></i>' : '<i class="mdi mdi-lock text-danger fs-3" title="Locked"></i>';
          let statusIcon = isUnlocked ?
              '<a href="javascript:;" class="btn btn-icon btn-sm">' +
                  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" id="hand-draw-white-check-mark-on-green-circle" style="width:40px; height:40px;">' +
                      '<circle cx="236.5" cy="250" r="200" fill="#39ba77"></circle>' +
                      '<path fill="#fff" d="M220.1,274.6c0,0,56.1-127.3,172.9-197.6c0,0,26-16.6,44.6,4.5c0,0,26.2,25.4-30.9,64.3c0,0-69,45.8-122.4,123.2c-12.8,18.5-23.6,38.3-33.3,58.6c-5.8,12.2-17.5,32.9-29,32.4c0,0-14.8,0.4-32.1-29.7c0,0-32.1-58.9-52.6-101.3c0,0-6-19,6.8-26.8c15.5-9.4,30.6,9.4,30.6,9.4L220.1,274.6z"></path>' +
                  '</svg>' +
              '</a>' :
              '<a href="javascript:;" class="btn btn-icon btn-sm">' +
                  '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500" id="hand-draw-white-cross-mark-on-red-circle" style="width:40px; height:40px;">' +
                      '<circle cx="250" cy="250" r="200" fill="#f32f45"></circle>' +
                      '<path fill="#fff" d="M417.1,96.9c0,0-22.8-25.6-64.3,5c0,0-55,40-110.7,102.2c-34-33.7-54.7-59.7-54.7-59.7s-19.5-24.3-35.8-10.5c-14.1,11.9,4.1,44.5,4.1,44.5c14.4,25.4,31.9,49.1,49.9,70.2c-13.1,17.5-25.5,36.2-36.5,55.6c0,0-20.8,38.6-4.6,52.9c14,12.3,35.5-14.7,35.5-14.7s16-21.9,43.6-52.8c36.9,36.3,67.6,58.4,67.6,58.4c38.8,28.6,59.9,2.2,59.9,2.2c23.2-32.7-24.2-59-24.2-59c-21.8-14.6-42.2-30.5-60.6-46.2c28.6-28,62.9-57.9,101-83.8C387.4,161.1,441.7,126.1,417.1,96.9z"></path>' +
                  '</svg>' +
              '</a>';
          // Award display: Show default original value (can be number or string)
          let awardBgClass = isUnlocked ? 'bg-success-cell' : 'bg-danger-cell';

          //  detect type
          let isPercentageType = percentageKeys.includes(key);

          //  choose display value
          let displayValue = isUnlocked ? earnedValue : originalValue;

          let awardDisplay = '';

          // ==========================
          //  PERCENTAGE TYPE
          // ==========================
          if (isPercentageType) {

              if (isUnlocked) {
                  //  Achieved → show MONEY
                  let moneyValue = Number(earnedValue) || 0;

                  awardDisplay = `<i class="mdi mdi-currency-inr fs-3"></i>
                      ${moneyValue.toLocaleString()}`;
              } else {
                  //  Not achieved → show %
                  let percentValue = Number(originalValue) || 0;

                  awardDisplay = `<span class="fw-bold">
                      ${percentValue}%
                  </span>`;
              }
          }

          // ==========================
          //  STRING AWARDS (Trip/iPhone)
          // ==========================
          else if (typeof displayValue === 'string' && isNaN(displayValue)) {

              let awardClass = '';
              if (displayValue.includes('Trip')) awardClass = 'trip';
              if (displayValue.toLowerCase().includes('iphone')) awardClass = 'iphone';

              awardDisplay = `<span class="award-text ${awardClass}">
                  <i class="mdi mdi-trophy-variant fs-5"></i> ${displayValue}
              </span>`;
          }

          // ==========================
          //  GOLD
          // ==========================
          else if (isGold) {

              let goldValue = Number(displayValue) || 0;

              awardDisplay = `<i class="mdi mdi-gold fs-4" style="color:#FFD700;"></i>
                  ${goldValue} gm Gold`;
          }

          // ==========================
          //  MONEY NORMAL
          // ==========================
          else {

              let moneyValue = Number(displayValue) || 0;

              awardDisplay = `<i class="mdi mdi-currency-inr fs-3"></i>
                  ${moneyValue.toLocaleString()}`;
          }

          html += `<tr onclick="showDetailsModal('${labelInfo.label}', '${key}', \`${JSON.stringify(calcData).replace(/`/g, '\\`').replace(/"/g, '&quot;')}\`, ${earnedValue}, '${String(originalValue).replace(/'/g, "\\'")}', ${isGold})" style="cursor:pointer;">
              <td class="fw-bold text-black fs-3" style="background: #ccc; border: 2px solid #000; font-size:18px;">${labelInfo.label} </td>
              <td class="text-center text-black fw-bold fs-3">${targetValue}</td>
              <td class="text-center  text-black fw-bold fs-3 ${actualBgClass}">${actualHtml}</td>
              <td class="text-center text-black">${statusIcon}</td>
              <td class="text-end text-black fw-bold fs-3 ${awardBgClass}">${awardDisplay}</td>
          </tr>`;
      });

      html += `</tbody></table>`;
      return html;
  }

  // Update showDetailsModal to handle string values
  function showDetailsModal(title, key, calcDataStr, earnedValue, originalValue, isGold) {
      let calcData;
      try {
          calcData = typeof calcDataStr === 'string' ? JSON.parse(calcDataStr.replace(/&quot;/g, '"')) : calcDataStr;
      } catch(e) {
          calcData = calcDataStr;
      }

      // Format award display for modal
      let awardDisplay = '';
      let isStringAward = typeof originalValue === 'string' && isNaN(originalValue);

      if (isStringAward) {
          awardDisplay = `${originalValue} (Earned: ${earnedValue > 0 ? 'Achieved ✓' : 'Not yet'})`;
      } else if (isGold) {
          awardDisplay = `${originalValue} gm Gold (Earned: ${earnedValue > 0 ? earnedValue + ' gm' : 'Not earned yet'})`;
      } else {
          awardDisplay = `₹ ${Number(originalValue).toLocaleString()} (Earned: ${earnedValue > 0 ? '₹ ' + earnedValue.toLocaleString() : 'Not earned yet'})`;
      }

      let modalContent = generateModalContent(key, calcData, awardDisplay);
      $('#detailModalLabel').text(`${title} - Detailed Breakdown`);
      $('#detailModalBody').html(modalContent);
      $('#detailModal').modal('show');
  }

  // Update PDF generation to handle string awards
  function generatePDFContent(staff, staffIndex, isSingle = true) {
      let total = Object.values(staff.incentive_value || {}).reduce((a, b) => a + Number(b), 0);
      let points = staff.incentive_value || {};
      let originalValues = staff.incentive_value_original || {};
      let calDetails = staff.incentive_cal || {};
      let incentiveType = staff.incentive_type || {};
      let branchName = $('#branch_name_lab').text() || 'Branch Report';
      let monthYear = $('#selectedMonth').text() + ' ' + $('#selectedYear').text();

      let orderedKeys = ['10xaward', 'ceiling', 'quarterly', 'twenty_five_percentage', 'fifty_percentage', 'six_month', 'twelve_month', 'nine_monthadd', 'twelve_monthadd', 'overall_year', 'presale', 'megabonus'];

      let rowsHtml = '';
      orderedKeys.forEach(key => {
          let earnedValue = Number(points[key] || 0);
          let originalValue = originalValues[key];
          let isUnlocked = earnedValue > 0;
          let isGold = goldBasedIncentives.includes(key) || incentiveType[key] === 'gold';
          let labelInfo = incentiveKPIMap[key] || { label: key.replace(/_/g, ' '), targetLabel: 'KPI', unit: '' };
          let targetDisplay = getTargetValueForPDF(key, calDetails[key]);
          let actualDisplay = getActualValueForPDF(key, calDetails[key]);

          // Format award text
          let awardText = '';
          if (typeof originalValue === 'string' && isNaN(originalValue)) {
              awardText = originalValue;
          } else if (isGold) {
              awardText = `${originalValue} gm Gold`;
          } else {
              awardText = `₹ ${Number(originalValue).toLocaleString()}`;
          }

          let statusText = isUnlocked ? 'Achieved ✓' : 'Not Achieved';

          rowsHtml += `
          <tr style="border: 1px solid #ddd;">
              <td style="border: 1px solid #ddd; padding: 8px;">${labelInfo.label}</td>
              <td style="border: 1px solid #ddd; padding: 8px;">${targetDisplay}</td>
              <td style="border: 1px solid #ddd; padding: 8px;">${actualDisplay}</td>
              <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${statusText}</td>
              <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${awardText}</td>
          </tr>`;
      });

      return `<!DOCTYPE html>
      <html>
      <head><meta charset="UTF-8"><title>BH Incentive Report</title>
      <style>
          body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
          .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
          .staff-info { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 5px; }
          table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
          th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
          th { background-color: #f2f2f2; font-weight: bold; }
          .total-award { background: #f9f9f9; font-size: 16px; font-weight: bold; padding: 10px; text-align: right; border-top: 2px solid #333; }
          .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #7f8c8d; }
          @media print {
              body { margin: 0; padding: 10px; }
              .no-print { display: none; }
          }
      </style>
      </head>
      <body>
          <div class="header"><h1>BH Incentive Report</h1><p>Branch: ${branchName} | Period: ${monthYear}</p></div>
          <div class="staff-info"><strong>Staff:</strong> ${escapeHtml(staff.staff_name)}<br>
          <strong>Department:</strong> ${escapeHtml(staff.department_name || '')} | ${escapeHtml(staff.sub_department_name || '')}</div>
          <table><thead><tr><th>Incentive</th><th>Target</th><th>Actual</th><th>Status</th><th>Award</th></tr></thead>
          <tbody>${rowsHtml}</tbody></table>
          <div class="total-award">Total Earned: ₹ ${total.toLocaleString()}</div>
          <div class="footer">Generated on: ${new Date().toLocaleString()}<br><button onclick="window.print()" style="margin-top:10px;">Save as PDF</button></div>
      </body>
      </html>`;
  }

  // Update generateModalContent for non-monetary awards
  function generateModalContent(key, calcData, awardDisplay) {
      if (!calcData) return '<div class="alert alert-info">No detailed data available</div>';

      if (key === '10xaward' && calcData.actual) {
          let target = calcData.target || {};
          let actual = calcData.actual || {};
          let conditionMet = calcData.condition_met || false;
          return `
              <div class="row">
                  <div class="col-12 mb-3"><h6>Target vs Actual</h6><table class="table table-sm"><tr><th>Metric</th><th>Target</th><th>Actual</th><th>Status</th></tr>
                  <tr class="${(actual.presale_count||0) >= (target.registrations||0) ? 'table-success' : 'table-danger'}"><td>Presale Registrations</td><td>${(target.registrations||0).toLocaleString()}</td><td>${(actual.presale_count||0).toLocaleString()}</td><td>${(actual.presale_count||0) >= (target.registrations||0) ? '✓' : '✗'}</td></tr>
                  <tr class="${(actual.postsale_count||0) >= (target.post_sale||0) ? 'table-success' : 'table-danger'}"><td>Postsale Count</td><td>${(target.post_sale||0).toLocaleString()}</td><td>${(actual.postsale_count||0).toLocaleString()}</td><td>${(actual.postsale_count||0) >= (target.post_sale||0) ? '✓' : '✗'}</td></tr>
                  <tr class="${(actual.total_credit||0) >= (target.monthly_target||0) ? 'table-success' : 'table-danger'}"><td>Total Credit</td><td>₹${(target.monthly_target||0).toLocaleString()}</td><td>₹${(actual.total_credit||0).toLocaleString()}</td><td>${(actual.total_credit||0) >= (target.monthly_target||0) ? '✓' : '✗'}</td></tr>
                  </table></div>
                  <div class="col-12"><h6>ABV/ACV Breakdown</h6><table class="table table-sm"><tr><th>Category</th><th>Count</th><th>ABV</th><th>ACV</th></tr>
                  <tr><td>Presale</td><td>${actual.presale?.count||0}</td><td>₹${actual.presale?.abv_actual||0}</td><td>₹${actual.presale?.acv_actual||0}</td></tr>
                  <tr><td>Postsale</td><td>${actual.postsale?.count||0}</td><td>₹${actual.postsale?.abv_actual||0}</td><td>₹${actual.postsale?.acv_actual||0}</td></tr>
                  <tr class="table-info"><td><strong>Overall</strong></td><td><strong>${actual.overall?.count||0}</strong></td><td><strong>₹${actual.overall?.abv_actual||0}</strong></td><td><strong>₹${actual.overall?.acv_actual||0}</strong></td></tr>
                  64.</div>
                  <div class="alert ${conditionMet ? 'alert-success' : 'alert-danger'} text-center mt-2"><strong>${conditionMet ? '✓ Condition Met! Award: ' + awardDisplay : '✗ Condition Not Met'}</strong></div>
              </div>`;
      }

      if (key === 'quarterly' && calcData.months) {
          let months = calcData.months || [];
          let eligibleQuarter = calcData.eligible_quarter || 'N/A';
          let quarterMonths = eligibleQuarter === 'Q1' ? [1,2,3] : eligibleQuarter === 'Q2' ? [4,5,6] : eligibleQuarter === 'Q3' ? [7,8,9] : [10,11,12];
          let relevantMonths = months.filter(m => quarterMonths.includes(m.month));
          let allAchieved = relevantMonths.length > 0 && relevantMonths.every(m => m.achieved);
          let monthsHtml = '<div class="row">';
          relevantMonths.forEach(month => {
              monthsHtml += `<div class="col-md-4 mb-2"><div class="card ${month.achieved ? 'border-success' : 'border-danger'}"><div class="card-body text-center"><h6>${month.month_name}</h6><div>Target: ₹${(month.target||0).toLocaleString()}</div><div>Actual: ₹${(month.actual||0).toLocaleString()}</div><div class="mt-2 fw-bold">${month.achieved ? '✓ Achieved' : '✗ Not Achieved'}</div></div></div></div>`;
          });
          monthsHtml += '</div>';
          return `<div class="alert alert-info">Eligible Quarter: ${eligibleQuarter}</div>${monthsHtml}<div class="alert ${allAchieved ? 'alert-success' : 'alert-danger'} text-center mt-3"><strong>${allAchieved ? '✓ Quarter Complete! Award: ' + awardDisplay : '✗ Not all months achieved'}</strong></div>`;
      }

      if (key.includes('month') || ['six_month','twelve_month','nine_monthadd','twelve_monthadd'].includes(key)) {
          let monthsBreakdown = calcData.monthly_breakdown || [];
          let monthsAchieved = calcData.months_achieved || 0;
          let requiredMonths = calcData.required_months || 6;
          let currentMonthNum = new Date().getMonth() + 1;
          let currentYearNum = new Date().getFullYear();
          let monthsHtml = '<div class="row mt-3">';
          monthsBreakdown.slice(0,12).forEach(month => {
              let isFuture = isFutureMonth(month.month, month.year, currentYearNum, currentMonthNum);
              let statusClass = isFuture ? 'border-info' : (month.achieved ? 'border-success' : 'border-danger');
              let statusIcon = isFuture ? '🕐 Future' : (month.achieved ? '✓ Achieved' : '✗ Not Achieved');
              monthsHtml += `<div class="col-md-3 col-sm-6 mb-2"><div class="card ${statusClass}"><div class="card-body text-center p-2"><strong>${month.month_name} ${month.year}</strong><div class="small">Target: ₹${(month.target||0).toLocaleString()}</div><div class="small">Actual: ₹${(month.actual||0).toLocaleString()}</div><div class="mt-1 fw-bold">${statusIcon}</div></div></div></div>`;
          });
          monthsHtml += '</div>';
          return `<div class="text-center mb-3"><div class="display-4 fw-bold">${monthsAchieved} / ${requiredMonths}</div><div class="progress-bar-custom mt-2"><div class="progress-fill-custom" style="width: ${(monthsAchieved/requiredMonths)*100}%; background: ${monthsAchieved>=requiredMonths?'#28a745':'#ffc107'}; height: 20px; border-radius: 6px;"></div></div></div><h6>Monthly Breakdown (Financial Year Apr-Mar):</h6>${monthsHtml}<div class="alert ${monthsAchieved>=requiredMonths?'alert-success':'alert-danger'} text-center mt-3"><strong>${monthsAchieved>=requiredMonths?'✓ ' + requiredMonths + ' Months Achieved! Award: ' + awardDisplay : 'Need ' + requiredMonths + ' months to unlock'}</strong></div>`;
      }

      if ((key === 'overall_year' || key === 'presale' || key === 'megabonus') && calcData.achievement_percent !== undefined) {
          let percent = calcData.achievement_percent;
          let required = key === 'megabonus' ? 200 : 150;
          let isAchieved = percent >= required;
          return `<div class="text-center mb-4"><div class="display-4 fw-bold ${isAchieved ? 'text-success' : 'text-danger'}">${percent}%</div><div class="progress-bar-custom mt-2" style="height: 30px;"><div class="progress-fill-custom" style="width: ${Math.min(100, (percent/required)*100)}%; background: ${isAchieved?'#28a745':'#ffc107'}; height: 30px; border-radius: 6px;"></div></div></div><div class="row text-center"><div class="col-6"><div class="card"><div class="card-body"><h5>Target</h5><h3>${key === 'presale' ? (calcData.target||0).toLocaleString() + ' registrations' : '₹ ' + (calcData.target||0).toLocaleString()}</h3></div></div></div><div class="col-6"><div class="card"><div class="card-body"><h5>Actual</h5><h3>${key === 'presale' ? (calcData.actual||0).toLocaleString() + ' registrations' : '₹ ' + (calcData.actual||0).toLocaleString()}</h3></div></div></div></div>${calcData.fy_range ? `<div class="alert alert-secondary mt-3 text-center">Financial Year: ${calcData.fy_range}</div>` : ''}<div class="alert ${isAchieved ? 'alert-success' : 'alert-danger'} text-center mt-3"><strong>${isAchieved ? '✓ Target Achieved! Award: ' + awardDisplay : 'Need ' + required + '% to unlock'}</strong></div>`;
      }

      return `<div class="alert alert-info text-center"><strong>Target: ${calcData.target || 0}</strong><br><strong>Actual: ${calcData.actual || 0}</strong><br><strong>Award: ${awardDisplay}</strong></div>`;
  }


    // Function to check if a month is in the future based on financial year (April-March)
    function isFutureMonth(monthNum, yearNum, currentYearVal, currentMonthVal) {
        // For financial year April-March
        // If we're viewing a past financial year, no month is future
        if (yearNum < currentYearVal) return false;
        if (yearNum > currentYearVal) return true;

        // Same year - compare months
        // For months Jan-Mar (1-3), they belong to previous financial year's end
        // For months Apr-Dec (4-12), they belong to current financial year
        if (monthNum >= 4) {
            return monthNum > currentMonthVal;
        } else {
            // Jan-Mar months - they are future only if current month is also Jan-Mar and month > current
            if (currentMonthVal >= 1 && currentMonthVal <= 3) {
                return monthNum > currentMonthVal;
            } else {
                // If we're in Apr-Dec, then Jan-Mar of next year are future
                return true;
            }
        }
    }

    function getActualWithDetails(key, calcData) {
        if (!calcData) return { html: '-', bgClass: 'bg-danger-cell' };

        // 10X Award with full ABV/ACV
        if (key === '10xaward' && calcData.actual) {
            let credit = calcData.actual.total_credit || 0;
            let preCount = calcData.actual.presale_count || 0;
            let postCount = calcData.actual.postsale_count || 0;
            let preABV = calcData.actual.presale?.abv_actual || 0;
            let preACV = calcData.actual.presale?.acv_actual || 0;
            let postABV = calcData.actual.postsale?.abv_actual || 0;
            let postACV = calcData.actual.postsale?.acv_actual || 0;
            let overallABV = calcData.actual.overall?.abv_actual || 0;
            let overallACV = calcData.actual.overall?.acv_actual || 0;
            let conditionMet = calcData.condition_met || false;

            let html = `
                <div>
                    <div><strong>₹ ${credit.toLocaleString()}</strong> | Pre:${preCount} | Post:${postCount}</div>
                    <div class="small mt-1">
                        <span class="badge bg-secondary">Pre ABV:₹${preABV}</span>
                        <span class="badge bg-secondary">Pre ACV:₹${preACV}</span>
                        <span class="badge bg-info">Post ABV:₹${postABV}</span>
                        <span class="badge bg-info">Post ACV:₹${postACV}</span>
                        <span class="badge bg-success">Overall ABV:₹${overallABV}</span>
                        <span class="badge bg-success">Overall ACV:₹${overallACV}</span>
                    </div>
                </div>`;
            return { html, bgClass: conditionMet ? 'bg-success-cell' : 'bg-danger-cell' };
        }

        // Ceiling
        if (key === 'ceiling' && calcData.actual !== undefined) {
            let isAchieved = calcData.actual >= calcData.target;
            let html = `<div><strong>${calcData.actual.toLocaleString()}</strong><br><small>Profit: ₹${(calcData.profit || 0).toLocaleString()} (${calcData.percentage || 0}%)</small></div>`;
            return { html, bgClass: isAchieved ? 'bg-success-cell' : 'bg-danger-cell' };
        }

        // Quarterly
        if (key === 'quarterly' && calcData.months) {
            let eligibleQuarter = calcData.eligible_quarter;
            let months = calcData.months || [];
            let quarterMonths = eligibleQuarter === 'Q1' ? [1,2,3] : eligibleQuarter === 'Q2' ? [4,5,6] : eligibleQuarter === 'Q3' ? [7,8,9] : [10,11,12];
            let relevantMonths = months.filter(m => quarterMonths.includes(m.month));
            let allAchieved = relevantMonths.length > 0 && relevantMonths.every(m => m.achieved === true);
            let totalActual = relevantMonths.reduce((s,m) => s + (m.actual || 0), 0);
            let totalTarget = relevantMonths.reduce((s,m) => s + (m.target || 0), 0);
            let percent = totalTarget > 0 ? Math.round((totalActual/totalTarget)*100) : 0;

            let monthIcons = '';
            relevantMonths.forEach(month => {
                if (month.achieved) monthIcons += `<i class="mdi mdi-check-circle text-success month-icon" title="${month.month_name}: ₹${(month.actual||0).toLocaleString()}/₹${(month.target||0).toLocaleString()}"></i>`;
                else monthIcons += `<i class="mdi mdi-close-circle text-danger month-icon" title="${month.month_name}: ₹${(month.actual||0).toLocaleString()}/₹${(month.target||0).toLocaleString()}"></i>`;
            });

            let html = `<div><strong>${totalActual.toLocaleString()}/${totalTarget.toLocaleString()}</strong> <span class="badge ${percent>=100?'bg-success':'bg-warning'}">${percent}%</span><br>${monthIcons}<div class="progress-bar-custom mt-1"><div class="progress-fill-custom" style="width: ${Math.min(100,percent)}%; background: ${percent>=100?'#28a745':'#ff9800'}; height: 12px; border-radius: 6px;"></div></div></div>`;
            return { html, bgClass: allAchieved ? 'bg-success-cell' : 'bg-danger-cell' };
        }

        // Month based (6,12,9 month) with future/past icons based on financial year
        if ((key === 'six_month' || key === 'twelve_month' || key === 'nine_monthadd' || key === 'twelve_monthadd') && calcData.months_achieved !== undefined) {
            let monthsAchieved = calcData.months_achieved;
            let requiredMonths = calcData.required_months || 6;
            let monthlyBreakdown = calcData.monthly_breakdown || [];
            let isAchieved = monthsAchieved >= requiredMonths;
            let currentMonthNum = new Date().getMonth() + 1;
            let currentYearNum = new Date().getFullYear();

            let monthIcons = '';
            monthlyBreakdown.slice(0, 12).forEach((month) => {
                let monthNum = month.month;
                let monthYear = month.year;
                let isFuture = isFutureMonth(monthNum, monthYear, currentYearNum, currentMonthNum);

                if (isFuture) {
                    monthIcons += `<i class="mdi mdi-clock-time-four-outline text-info month-icon" title="${month.month_name} ${month.year}: Future Month (Not yet occurred)"></i>`;
                } else if (month.achieved) {
                    monthIcons += `<i class="mdi mdi-check-circle text-success month-icon" title="${month.month_name} ${month.year}: Achieved ✓ - ₹${(month.actual||0).toLocaleString()}/₹${(month.target||0).toLocaleString()}"></i>`;
                } else {
                    monthIcons += `<i class="mdi mdi-close-circle text-danger month-icon" title="${month.month_name} ${month.year}: Not Achieved ✗ - ₹${(month.actual||0).toLocaleString()}/₹${(month.target||0).toLocaleString()}"></i>`;
                }
            });

            let html = `<div class="text-center"><strong>${monthsAchieved}/${requiredMonths} Months</strong><br><div class="d-flex flex-wrap justify-content-center gap-1 mt-1">${monthIcons}</div><div class="progress-bar-custom mt-2"><div class="progress-fill-custom" style="width: ${Math.min(100, (monthsAchieved/requiredMonths)*100)}%; background: ${isAchieved?'#28a745':'#ffc107'}; height: 12px; border-radius: 6px;"></div></div><small>Need ${requiredMonths} months (Apr-Mar Financial Year)</small></div>`;
            return { html, bgClass: isAchieved ? 'bg-success-cell' : 'bg-danger-cell' };
        }

        // Percentage based
        if ((key === 'twenty_five_percentage' || key === 'fifty_percentage') && calcData.achievement_percent !== undefined) {
            let percent = calcData.achievement_percent;
            let isAchieved = percent >= 100;
            let html = `<div><strong>₹${(calcData.actual || 0).toLocaleString()}</strong> <span class="badge ${isAchieved?'bg-success':'bg-warning'}">${percent}%</span><div class="progress-bar-custom mt-1"><div class="progress-fill-custom" style="width: ${Math.min(100,percent)}%; background: ${isAchieved?'#28a745':'#ffc107'}; height: 12px; border-radius: 6px;"></div></div>${calcData.profit > 0 ? `<small>Profit: ₹${calcData.profit.toLocaleString()}</small>` : ''}</div>`;
            return { html, bgClass: isAchieved ? 'bg-success-cell' : (percent >= 70 ? 'bg-warning-cell' : 'bg-danger-cell') };
        }

        // Overall Year, Presale, Mega Bonus
        if ((key === 'overall_year' || key === 'presale' || key === 'megabonus') && calcData.achievement_percent !== undefined) {
            let percent = calcData.achievement_percent;
            let required = key === 'megabonus' ? 200 : 150;
            let isAchieved = percent >= required;
            let actualVal = calcData.actual || 0;
            let unit = key === 'presale' ? 'registrations' : '₹';
            let html = `<div><strong>${typeof actualVal === 'number' ? actualVal.toLocaleString() : actualVal} ${unit}</strong> <span class="badge ${isAchieved?'bg-success':'bg-danger'}">${percent}%</span><div class="progress-bar-custom mt-1"><div class="progress-fill-custom" style="width: ${Math.min(100, (percent/required)*100)}%; background: ${isAchieved?'#28a745':'#dc3545'}; height: 12px; border-radius: 6px;"></div></div><small>Need ${required}%</small></div>`;
            return { html, bgClass: isAchieved ? 'bg-success-cell' : 'bg-danger-cell' };
        }

        // Fallback
        if (calcData.actual !== undefined) {
            let isAchieved = calcData.actual >= calcData.target;
            let percent = calcData.target > 0 ? Math.round((calcData.actual/calcData.target)*100) : 0;
            let html = `<div><strong>${calcData.actual.toLocaleString()}</strong><div class="progress-bar-custom mt-1"><div class="progress-fill-custom" style="width: ${percent}%; background: ${isAchieved?'#28a745':'#ffc107'}; height: 12px; border-radius: 6px;"></div></div><small>${percent}%</small></div>`;
            return { html, bgClass: isAchieved ? 'bg-success-cell' : (percent >= 70 ? 'bg-warning-cell' : 'bg-danger-cell') };
        }

        return { html: '-', bgClass: 'bg-danger-cell' };
    }

    function getTargetValue(key, calcData) {
        if (!calcData) return '-';
        if (key === '10xaward' && calcData.target) {
            return `<i class="mdi mdi-currency-inr fs-3"></i> ${(calcData.target.monthly_target || 0).toLocaleString()} | 📋 ${calcData.target.registrations || 0} pre | 🎯 ${calcData.target.post_sale || 0} post`;
        }
        if (key === 'quarterly' && calcData.eligible_quarter) return calcData.eligible_quarter;
        if ((key === 'six_month' || key === 'twelve_month' || key === 'nine_monthadd' || key === 'twelve_monthadd') && calcData.required_months) {
            return `${calcData.required_months} Months (Apr-Mar)`;
        }
        if ((key === 'nine_monthadd' || key === 'twelve_monthadd') && calcData.target_label) {
            let requiredMonthsadd = calcData.target_label;
            return requiredMonthsadd;
        }
        if (calcData.target !== undefined) {
            let unit = calcData.unit || (key === 'presale' ? 'registrations' : '₹');
            return `${typeof calcData.target === 'number' ? calcData.target.toLocaleString() : calcData.target} ${unit}`;
        }
        return '-';
    }




    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function branch_change() { fetchIncentivesList(); }
    function deptChange() { fetchIncentivesList(); }

    $(document).ready(function() {
        fetchIncentivesList();
        if (typeof $.fn.select2 !== 'undefined') $('.select3').select2({ theme: 'bootstrap-5' });
    });
</script>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title" id="detailModalLabel">Incentive Details</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body" id="detailModalBody"><div class="text-center py-4"><div class="spinner-border text-primary"></div></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

@endsection
