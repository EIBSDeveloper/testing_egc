-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2026 at 03:31 PM
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
-- Table structure for table `egc_staff_salary_accounts`
--

CREATE TABLE `egc_staff_salary_accounts` (
  `sno` int(11) NOT NULL,
  `account_code` varchar(50) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `salary_company_id` int(11) DEFAULT NULL,
  `salary_bank_id` int(11) DEFAULT NULL,
  `gross_salary` decimal(12,2) DEFAULT NULL,
  `salary_type` int(3) DEFAULT 1,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `is_payslip` int(4) DEFAULT 0,
  `created_by` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_staff_salary_accounts`
--
ALTER TABLE `egc_staff_salary_accounts`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_staff_salary_accounts`
--
ALTER TABLE `egc_staff_salary_accounts`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
