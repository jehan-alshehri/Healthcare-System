-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 13 أغسطس 2026 الساعة 03:58
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthcare_mgmt`
--

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_people`
--

CREATE TABLE `tbl_people` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `tbl_people`
--

INSERT INTO `tbl_people` (`id`, `name`, `email`, `address`, `phone`) VALUES
(1, 'admin', 'admin@gmail.com', 'Jeddah', '055001421'),
(2, 'Jehan naif Alshehri', 'jehan@gmail.com', 'Tabuk', '05220120022'),
(3, 'جديد', 'new@gmail.com', 'Riyadh', '0557367289'),
(4, 'm', 'm@icloud.com', 'Tabuk', '05555555');

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_project`
--

CREATE TABLE `tbl_project` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(150) NOT NULL,
  `plan_start_date` date DEFAULT NULL,
  `actual_start_date` date DEFAULT NULL,
  `plan_budget` decimal(12,2) DEFAULT NULL,
  `actual_budget` decimal(12,2) DEFAULT NULL,
  `project_manager_id` int(11) DEFAULT NULL,
  `project_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `tbl_project`
--

INSERT INTO `tbl_project` (`project_id`, `project_name`, `plan_start_date`, `actual_start_date`, `plan_budget`, `actual_budget`, `project_manager_id`, `project_description`) VALUES
(4, '1', '2025-04-29', '2025-05-01', 10000.00, 10000.00, 4, ''),
(5, 'Hospital System Upgrade', '2026-08-13', '2026-08-13', 50000.00, 45000.00, 2, 'A System for managing healthcare and medical records.');

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_resources`
--

CREATE TABLE `tbl_resources` (
  `resource_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `resource_title` varchar(100) NOT NULL,
  `qty` int(11) DEFAULT 1,
  `resource_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `tbl_resources`
--

INSERT INTO `tbl_resources` (`resource_id`, `task_id`, `resource_title`, `qty`, `resource_type`) VALUES
(4, 3, 'C', 1, 'Service'),
(5, 3, 'Medical Tablets', 1, 'Equipment');

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_task`
--

CREATE TABLE `tbl_task` (
  `task_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `task_name` varchar(150) NOT NULL,
  `task_plan_start_date` date DEFAULT NULL,
  `task_actual_start_date` date DEFAULT NULL,
  `task_plan_budget` decimal(12,2) DEFAULT NULL,
  `task_actual_budget` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `tbl_task`
--

INSERT INTO `tbl_task` (`task_id`, `project_id`, `task_name`, `task_plan_start_date`, `task_actual_start_date`, `task_plan_budget`, `task_actual_budget`) VALUES
(3, 4, 'A', NULL, NULL, 3000.00, NULL),
(4, 5, 'Database Setup', '2026-08-13', '2026-08-13', 5000.00, 4500.00);

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_task_people`
--

CREATE TABLE `tbl_task_people` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `tbl_task_people`
--

INSERT INTO `tbl_task_people` (`id`, `task_id`, `person_id`) VALUES
(6, 3, 4),
(7, 4, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_people`
--
ALTER TABLE `tbl_people`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_project`
--
ALTER TABLE `tbl_project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `project_manager_id` (`project_manager_id`);

--
-- Indexes for table `tbl_resources`
--
ALTER TABLE `tbl_resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `tbl_task`
--
ALTER TABLE `tbl_task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `tbl_task_people`
--
ALTER TABLE `tbl_task_people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `person_id` (`person_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_people`
--
ALTER TABLE `tbl_people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_project`
--
ALTER TABLE `tbl_project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_resources`
--
ALTER TABLE `tbl_resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_task`
--
ALTER TABLE `tbl_task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_task_people`
--
ALTER TABLE `tbl_task_people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `tbl_project`
--
ALTER TABLE `tbl_project`
  ADD CONSTRAINT `tbl_project_ibfk_1` FOREIGN KEY (`project_manager_id`) REFERENCES `tbl_people` (`id`);

--
-- قيود الجداول `tbl_resources`
--
ALTER TABLE `tbl_resources`
  ADD CONSTRAINT `tbl_resources_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tbl_task` (`task_id`) ON DELETE CASCADE;

--
-- قيود الجداول `tbl_task`
--
ALTER TABLE `tbl_task`
  ADD CONSTRAINT `tbl_task_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `tbl_project` (`project_id`) ON DELETE CASCADE;

--
-- قيود الجداول `tbl_task_people`
--
ALTER TABLE `tbl_task_people`
  ADD CONSTRAINT `tbl_task_people_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tbl_task` (`task_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_task_people_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `tbl_people` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
