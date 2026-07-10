-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2026 at 03:41 PM
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
-- Table structure for table `egc_locations`
--

CREATE TABLE `egc_locations` (
  `sno` int(11) NOT NULL,
  `location_code` varchar(50) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `area` text DEFAULT NULL,
  `pincode` varchar(50) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0- Waiting For start\r\n1- Resource Start\r\n2- delete\r\n3- on Hold\r\n4- ShortList\r\n5- Processing\r\n6- Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `egc_locations`
--

INSERT INTO `egc_locations` (`sno`, `location_code`, `country_id`, `state_id`, `city_id`, `area`, `pincode`, `region_id`, `latitude`, `longitude`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) VALUES
(1, 'LOC-IN-TN-MAI-0001', 101, 35, 3912, 'Anna Nagar', '623826', 1, '798749.54', '75485954.34', '2026-07-10 07:18:54', 0, '2026-07-10 07:55:51', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `egc_locations`
--
ALTER TABLE `egc_locations`
  ADD PRIMARY KEY (`sno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `egc_locations`
--
ALTER TABLE `egc_locations`
  MODIFY `sno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
