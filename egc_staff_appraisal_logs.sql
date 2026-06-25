-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 03:09 PM
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
-- Table structure for table `egc_staff_appraisal_logs`
--

CREATE TABLE `egc_staff_appraisal_logs` (
  `sno` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `salary_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payroll_employee_structure_sno` bigint(20) UNSIGNED DEFAULT NULL,
  `appraisal_date` date NOT NULL,
  `appraisal_type` varchar(100) DEFAULT NULL,
  `appraisal_reason` text DEFAULT NULL,
  `appraisal_unit_type` enum('%','value') NOT NULL DEFAULT '%',
  `appraisal_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `old_gross_salary` decimal(18,2) NOT NULL DEFAULT 0.00,
  `new_gross_salary` decimal(18,2) NOT NULL DEFAULT 0.00,
  `salary_difference` decimal(18,2) NOT NULL DEFAULT 0.00,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_by` bigint(20) DEFAULT 0,
  `updated_by` bigint(20) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_staff_appraisal_logs`
--
ALTER TABLE `egc_staff_appraisal_logs`
  ADD PRIMARY KEY (`sno`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `salary_account_id` (`salary_account_id`),
  ADD KEY `appraisal_date` (`appraisal_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_staff_appraisal_logs`
--
ALTER TABLE `egc_staff_appraisal_logs`
  MODIFY `sno` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
