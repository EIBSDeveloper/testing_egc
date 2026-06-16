-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2026 at 03:39 PM
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
-- Table structure for table `egc_applicant_history_logs`
--

CREATE TABLE `egc_applicant_history_logs` (
  `sno` bigint(20) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `interview_date` date DEFAULT NULL,
  `applicant_status` enum('Applied','Interview Scheduled','Interview Attended','Selected','Rejected','On Hold','Critical','Not Joined','Offer Released','Offer Accepted','Offer Rejected','Joined') DEFAULT 'Applied',
  `remarks` text DEFAULT NULL,
  `selected_date` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `job_role_id` int(11) DEFAULT NULL,
  `expected_salary` decimal(12,2) DEFAULT NULL,
  `offered_salary` decimal(12,2) DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `egc_applicant_history_logs`
--

INSERT INTO `egc_applicant_history_logs` (`sno`, `applicant_id`, `interview_date`, `applicant_status`, `remarks`, `selected_date`, `joining_date`, `company_id`, `entity_id`, `job_role_id`, `expected_salary`, `offered_salary`, `followup_date`, `created_by`, `created_at`, `updated_by`, `updated_at`, `status`) VALUES
(1, 19398, '2026-06-16', 'Rejected', 'Better luck', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14', 0, '2026-06-16 19:04:14', 0, '2026-06-16 19:04:14', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_applicant_history_logs`
--
ALTER TABLE `egc_applicant_history_logs`
  ADD PRIMARY KEY (`sno`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `applicant_status` (`applicant_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_applicant_history_logs`
--
ALTER TABLE `egc_applicant_history_logs`
  MODIFY `sno` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
