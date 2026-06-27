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
-- Table structure for table `egc_staff_salary_appraisal_history`
--

CREATE TABLE `egc_staff_salary_appraisal_history` (
  `sno` bigint(20) NOT NULL,
  `employee_sno` bigint(20) NOT NULL,
  `salary_account_id` bigint(20) NOT NULL,
  `payroll_template_sno` bigint(20) NOT NULL,
  `effective_from` date NOT NULL,
  `gross_salary` decimal(12,2) NOT NULL,
  `appraisal_type` tinyint(4) NOT NULL,
  `appraisal_unit` enum('%','Rs') DEFAULT NULL,
  `appraisal_value` decimal(12,2) DEFAULT NULL,
  `appraisal_reason` text DEFAULT NULL,
  `variable_component` bigint(20) DEFAULT NULL,
  `variable_amount` decimal(12,2) DEFAULT 0.00,
  `variable_months` int(11) DEFAULT 3,
  `processed` tinyint(4) DEFAULT 0,
  `processed_at` datetime DEFAULT NULL,
  `processed_by` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_staff_salary_appraisal_history`
--
ALTER TABLE `egc_staff_salary_appraisal_history`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_staff_salary_appraisal_history`
--
ALTER TABLE `egc_staff_salary_appraisal_history`
  MODIFY `sno` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
