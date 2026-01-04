-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 11:14 AM
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
-- Database: `green_and_earn`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `activity_id` int(11) UNSIGNED NOT NULL,
  `admin_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `points_awarded` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_submission`
--

CREATE TABLE `activity_submission` (
  `submission_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `moderator_id` int(11) UNSIGNED NOT NULL,
  `activity_id` int(11) UNSIGNED NOT NULL,
  `proof` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `reason_of_rejection` text DEFAULT NULL,
  `submission_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

CREATE TABLE `badge` (
  `badge_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `requirement` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badge_obtain`
--

CREATE TABLE `badge_obtain` (
  `obtain_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `badge_id` int(11) UNSIGNED NOT NULL,
  `obtain_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friendship`
--

CREATE TABLE `friendship` (
  `friendship_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `friend_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_point_transaction`
--

CREATE TABLE `partner_point_transaction` (
  `partner_transaction_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `partner_id` int(11) UNSIGNED NOT NULL,
  `award_time` datetime NOT NULL DEFAULT current_timestamp(),
  `amount_spent` decimal(10,2) NOT NULL,
  `points_awarded` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `point_transaction`
--

CREATE TABLE `point_transaction` (
  `transaction_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `submission_id` int(11) UNSIGNED DEFAULT NULL,
  `redemption_id` int(11) UNSIGNED DEFAULT NULL,
  `partner_transaction_id` int(11) UNSIGNED DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `transaction_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward`
--

CREATE TABLE `reward` (
  `reward_id` int(11) UNSIGNED NOT NULL,
  `partner_id` int(11) UNSIGNED NOT NULL,
  `admin_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `required_points` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `reward_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reward_redemption`
--

CREATE TABLE `reward_redemption` (
  `redemption_id` int(11) UNSIGNED NOT NULL,
  `reward_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `points_spent` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `redemption_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `total_point_accumulated` int(11) NOT NULL DEFAULT 0,
  `point_balance` int(11) NOT NULL DEFAULT 0,
  `active_status` tinyint(1) NOT NULL DEFAULT 1,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `activity_submission`
--
ALTER TABLE `activity_submission`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `moderator_id` (`moderator_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `badge`
--
ALTER TABLE `badge`
  ADD PRIMARY KEY (`badge_id`);

--
-- Indexes for table `badge_obtain`
--
ALTER TABLE `badge_obtain`
  ADD PRIMARY KEY (`obtain_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- Indexes for table `friendship`
--
ALTER TABLE `friendship`
  ADD PRIMARY KEY (`friendship_id`),
  ADD KEY `friend_id` (`friend_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `partner_point_transaction`
--
ALTER TABLE `partner_point_transaction`
  ADD PRIMARY KEY (`partner_transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `point_transaction`
--
ALTER TABLE `point_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `redemption_id` (`redemption_id`),
  ADD KEY `partner_transaction_id` (`partner_transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reward`
--
ALTER TABLE `reward`
  ADD PRIMARY KEY (`reward_id`),
  ADD KEY `partner_id` (`partner_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `reward_redemption`
--
ALTER TABLE `reward_redemption`
  ADD PRIMARY KEY (`redemption_id`),
  ADD KEY `reward_id` (`reward_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `activity_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_submission`
--
ALTER TABLE `activity_submission`
  MODIFY `submission_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badge`
--
ALTER TABLE `badge`
  MODIFY `badge_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badge_obtain`
--
ALTER TABLE `badge_obtain`
  MODIFY `obtain_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friendship`
--
ALTER TABLE `friendship`
  MODIFY `friendship_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partner_point_transaction`
--
ALTER TABLE `partner_point_transaction`
  MODIFY `partner_transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `point_transaction`
--
ALTER TABLE `point_transaction`
  MODIFY `transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reward`
--
ALTER TABLE `reward`
  MODIFY `reward_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reward_redemption`
--
ALTER TABLE `reward_redemption`
  MODIFY `redemption_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `activity_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `activity_submission`
--
ALTER TABLE `activity_submission`
  ADD CONSTRAINT `activity_submission_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `activity_submission_ibfk_2` FOREIGN KEY (`moderator_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `activity_submission_ibfk_3` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`);

--
-- Constraints for table `badge_obtain`
--
ALTER TABLE `badge_obtain`
  ADD CONSTRAINT `badge_obtain_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `badge_obtain_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badge` (`badge_id`);

--
-- Constraints for table `friendship`
--
ALTER TABLE `friendship`
  ADD CONSTRAINT `friendship_ibfk_1` FOREIGN KEY (`friend_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `friendship_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `partner_point_transaction`
--
ALTER TABLE `partner_point_transaction`
  ADD CONSTRAINT `partner_point_transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `partner_point_transaction_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `point_transaction`
--
ALTER TABLE `point_transaction`
  ADD CONSTRAINT `point_transaction_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `activity_submission` (`submission_id`),
  ADD CONSTRAINT `point_transaction_ibfk_2` FOREIGN KEY (`redemption_id`) REFERENCES `reward_redemption` (`redemption_id`),
  ADD CONSTRAINT `point_transaction_ibfk_3` FOREIGN KEY (`partner_transaction_id`) REFERENCES `partner_point_transaction` (`partner_transaction_id`),
  ADD CONSTRAINT `point_transaction_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `reward`
--
ALTER TABLE `reward`
  ADD CONSTRAINT `reward_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `reward_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `reward_redemption`
--
ALTER TABLE `reward_redemption`
  ADD CONSTRAINT `reward_redemption_ibfk_1` FOREIGN KEY (`reward_id`) REFERENCES `reward` (`reward_id`),
  ADD CONSTRAINT `reward_redemption_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
