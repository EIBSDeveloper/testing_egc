-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 03:33 PM
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
-- Database: `dev_egc`
--

-- --------------------------------------------------------

--
-- Table structure for table `egc_target_achievements`
--

CREATE TABLE `egc_target_achievements` (
  `id` bigint(20) NOT NULL,
  `entity_id` bigint(20) DEFAULT NULL,
  `branch_id` bigint(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` tinyint(4) DEFAULT NULL,
  `month` tinyint(4) DEFAULT NULL,
  `week` tinyint(4) DEFAULT NULL,
  `achieved_amount` decimal(12,2) DEFAULT 0.00,
  `pre_sale` decimal(12,2) DEFAULT 0.00,
  `post_sale` decimal(12,2) DEFAULT 0.00,
  `source` varchar(50) DEFAULT 'ERP',
  `reference_id` varchar(100) DEFAULT NULL,
  `achieved_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_target_achievements`
--
ALTER TABLE `egc_target_achievements`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_target_achievements`
--
ALTER TABLE `egc_target_achievements`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
