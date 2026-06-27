-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 04:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `egc_may`
--

-- --------------------------------------------------------

--
-- Table structure for table `egc_payroll_staff_appraisal_details`
--

CREATE TABLE `egc_payroll_staff_appraisal_details` (
  `sno` bigint(20) UNSIGNED NOT NULL,
  `payroll_staff_appraisal_sno` bigint(20) UNSIGNED NOT NULL,
  `payroll_component_sno` bigint(20) UNSIGNED NOT NULL,
  `payroll_rule_sno` bigint(20) UNSIGNED DEFAULT NULL,
  `component_name` varchar(255) NOT NULL,
  `component_code` varchar(255) DEFAULT NULL,
  `component_type` enum('earning','deduction','employer_contribution') NOT NULL,
  `calculation_type` enum('fixed','percentage','manual_input') NOT NULL,
  `percentage_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fixed_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `calculated_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `include_in_gross` tinyint(1) NOT NULL DEFAULT 0,
  `include_in_ctc` tinyint(1) NOT NULL DEFAULT 1,
  `include_in_payslip` tinyint(1) NOT NULL DEFAULT 1,
  `monthly_variable` tinyint(1) NOT NULL DEFAULT 0,
  `variable_months` tinyint(4) NOT NULL DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_payroll_staff_appraisal_details`
--
ALTER TABLE `egc_payroll_staff_appraisal_details`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_payroll_staff_appraisal_details`
--
ALTER TABLE `egc_payroll_staff_appraisal_details`
  MODIFY `sno` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
