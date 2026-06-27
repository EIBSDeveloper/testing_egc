-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 04:21 PM
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
-- Table structure for table `egc_staff_salary_appraisals`
--

CREATE TABLE `egc_staff_salary_appraisals` (
  `sno` bigint(20) UNSIGNED NOT NULL,
  `employee_sno` bigint(20) UNSIGNED NOT NULL,
  `salary_account_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_structure_sno` bigint(20) UNSIGNED DEFAULT NULL,
  `payroll_template_sno` bigint(20) UNSIGNED NOT NULL,
  `previous_gross_salary` decimal(15,2) NOT NULL DEFAULT 0.00,
  `revised_gross_salary` decimal(15,2) NOT NULL,
  `appraisal_type` enum('Joining','Yearly Appraisal','Special Appraisal','Project','10X Warrior') NOT NULL,
  `appraisal_unit` enum('RS','PERCENT') DEFAULT NULL,
  `appraisal_value` decimal(12,2) DEFAULT NULL,
  `has_variable_amount` tinyint(1) NOT NULL DEFAULT 0,
  `variable_component_sno` bigint(20) UNSIGNED DEFAULT NULL,
  `variable_amount` decimal(12,2) DEFAULT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `revision_reason` varchar(500) DEFAULT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 Active,1 Imported,2 Deleted',
  `created_by` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `updated_by` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_staff_salary_appraisals`
--
ALTER TABLE `egc_staff_salary_appraisals`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_staff_salary_appraisals`
--
ALTER TABLE `egc_staff_salary_appraisals`
  MODIFY `sno` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
