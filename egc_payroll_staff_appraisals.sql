-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 04:18 PM
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
-- Table structure for table `egc_payroll_staff_appraisals`
--

CREATE TABLE `egc_payroll_staff_appraisals` (
  `sno` bigint(20) UNSIGNED NOT NULL,
  `employee_sno` bigint(20) UNSIGNED NOT NULL,
  `salary_account_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_template_sno` bigint(20) UNSIGNED NOT NULL,
  `appraisal_type` enum('Joining','Yearly','Special','Project','10X Warrior') NOT NULL,
  `appraisal_unit` enum('%','Rs') DEFAULT NULL,
  `appraisal_value` decimal(12,2) DEFAULT NULL,
  `appraisal_reason` text DEFAULT NULL,
  `gross_salary_before` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_salary_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_earnings` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ctc_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `employer_contribution` decimal(12,2) NOT NULL DEFAULT 0.00,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 1,
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
-- Indexes for table `egc_payroll_staff_appraisals`
--
ALTER TABLE `egc_payroll_staff_appraisals`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_payroll_staff_appraisals`
--
ALTER TABLE `egc_payroll_staff_appraisals`
  MODIFY `sno` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
