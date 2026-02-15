-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2026 at 10:23 AM
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
  `status` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `admin_id`, `title`, `description`, `points_awarded`, `status`, `start_date`, `end_date`) VALUES
(1, 3, 'Neighborhood Cleanup', 'Spend 30 minutes picking up litter in your local area (park, street, or beach).', 500, 'Active', '2026-01-01', '2026-03-31'),
(2, 3, 'Recycle 5 Containers', 'Recycle 5 plastic bottles or aluminum cans at your nearest recycling bin.', 100, 'Active', '2026-01-29', '2026-06-02'),
(3, 3, 'Plant Greenery', 'Plant a tree, flower, or herb in your garden or a pot at home.', 1000, 'Active', '2026-01-09', '2026-05-07'),
(4, 3, 'Reusable Bag Challenge', 'Upload a photo of your grocery haul using only reusable bags.', 200, 'Active', '2026-01-02', '2026-04-08'),
(5, 3, 'Plastic-Free Day', 'Go 24 hours without buying or using single-use plastics (straws, cups, etc).', 300, 'Active', '2026-01-09', '2026-03-18');

-- --------------------------------------------------------

--
-- Table structure for table `activity_submission`
--

CREATE TABLE `activity_submission` (
  `submission_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `moderator_id` int(11) UNSIGNED DEFAULT NULL,
  `activity_id` int(11) UNSIGNED NOT NULL,
  `proof` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL,
  `submission_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_submission`
--

INSERT INTO `activity_submission` (`submission_id`, `user_id`, `moderator_id`, `activity_id`, `proof`, `status`, `submission_time`) VALUES
(1, 11, NULL, 2, '../../uploads/proofs/proof_userID11_activityID2_1769741635.png', 'Pending', '2026-01-30 10:53:55'),
(2, 5, NULL, 3, '../../uploads/proofs/proof_userID5_activityID3_1769741715.jpg', 'Pending', '2026-01-30 10:55:15'),
(3, 12, NULL, 4, '../../uploads/proofs/proof_userID12_activityID4_1769741826.png', 'Pending', '2026-01-30 10:57:06'),
(4, 12, NULL, 1, '../../uploads/proofs/proof_userID12_activityID1_1769742006.png', 'Pending', '2026-01-30 11:00:06'),
(5, 1, 2, 2, '../../uploads/proofs/proof_userID1_activityID2_1769742069.png', 'Approved', '2026-01-30 11:01:09'),
(6, 1, 2, 1, '../../uploads/proofs/proof_userID1_activityID1_1769742122.png', 'Rejected', '2026-01-30 11:02:02'),
(7, 1, NULL, 3, '../../uploads/proofs/proof_userID1_activityID3_1769742342.jpg', 'Pending', '2026-01-30 11:05:42'),
(8, 1, 2, 4, '../../uploads/proofs/proof_userID1_activityID4_1769780825.png', 'Approved', '2026-01-30 21:47:05'),
(9, 1, NULL, 1, '../../uploads/proofs/proof_userID1_activityID1_1769789496.png', 'Pending', '2026-01-31 00:11:36');

-- --------------------------------------------------------

--
-- Table structure for table `badge`
--

CREATE TABLE `badge` (
  `badge_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `criteria_category` varchar(55) NOT NULL,
  `criteria_value` int(11) NOT NULL,
  `badge_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badge`
--

INSERT INTO `badge` (`badge_id`, `title`, `description`, `criteria_category`, `criteria_value`, `badge_img`) VALUES
(1, 'Eco Warrior Bronze', 'U are a small eco warrior', 'points_earned', 5000, 'bronze.png'),
(2, 'Eco Warrior Silver', 'You are now a little more stronger', 'points_earned', 10000, 'silver.png'),
(3, 'Eco Warrior Gold', 'Keep On the good work', 'points_earned', 20000, 'gold.png\r\n'),
(4, 'Eco Warrior Platinum', 'nihowdy', 'points_earned', 30000, 'plat.png\r\n'),
(5, 'Eco Warrior Diamond', 'Shine bright like a Diamond in the sky', 'points_earned', 40000, 'diamond.png'),
(6, 'Eco Warrior Asc', 'Food', 'points_earned', 50000, 'asend.png'),
(7, 'Eco Warrior Immo', 'Immortal', 'tasks_completed', 100, 'immortal.png'),
(8, 'Eco Warrior Radiant', 'Top1', 'tasks_completed', 500, 'radiant.png');

-- --------------------------------------------------------

--
-- Table structure for table `badge_obtain`
--

CREATE TABLE `badge_obtain` (
  `obtain_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `badge_id` int(11) UNSIGNED NOT NULL,
  `obtain_date` datetime NOT NULL DEFAULT current_timestamp(),
  `isNew` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badge_obtain`
--

INSERT INTO `badge_obtain` (`obtain_id`, `user_id`, `badge_id`, `obtain_date`, `isNew`) VALUES
(1, 1, 1, '2026-01-30 21:47:24', 0),
(2, 1, 2, '2026-01-30 21:47:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_id` int(11) NOT NULL,
  `admin_id` int(11) UNSIGNED DEFAULT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL,
  `mark_as_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`contact_id`, `admin_id`, `firstName`, `lastName`, `email`, `phoneNumber`, `message`, `mark_as_read`) VALUES
(1, 3, 'Tham', 'Jun', 'tham@gmail.com', '0123456789', 'Hello, I am interested to become your app\'s partner. ', 0),
(3, 3, 'Tan ', 'Jing Yu ', 'leon0912tan@gmail.com', '0125193373', 'Hi, I love your website!', 0);

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

--
-- Dumping data for table `friendship`
--

INSERT INTO `friendship` (`friendship_id`, `user_id`, `friend_id`, `status`, `creation_date`) VALUES
(2, 6, 1, 'Accepted', '2026-01-30 22:57:17'),
(3, 1, 8, 'Accepted', '2026-01-31 00:14:38'),
(4, 1, 5, 'Pending', '2026-02-14 14:46:06');

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
  `points_awarded` int(11) NOT NULL,
  `receipt_img` varchar(255) NOT NULL,
  `status` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partner_point_transaction`
--

INSERT INTO `partner_point_transaction` (`partner_transaction_id`, `user_id`, `partner_id`, `award_time`, `amount_spent`, `points_awarded`, `receipt_img`, `status`) VALUES
(1, 1, 4, '2026-01-30 11:11:02', 56.70, 567, '../../uploads/receipt/member_belugapartner_runaway1769742662.png', 'Approved'),
(2, 1, 4, '2026-01-30 11:18:23', 49.80, 498, '../../uploads/receipt/member_belugapartner_runaway(blablaMart)1769743103.png', 'Pending'),
(3, 11, 4, '2026-01-30 11:21:04', 67.70, 677, '../../uploads/receipt/member_megaNightpartner_runaway(blablaMart)1769743264.png', 'Pending');

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

--
-- Dumping data for table `point_transaction`
--

INSERT INTO `point_transaction` (`transaction_id`, `user_id`, `submission_id`, `redemption_id`, `partner_transaction_id`, `amount`, `type`, `transaction_time`) VALUES
(1, 1, NULL, NULL, 1, 567, 'Earned', '2026-01-30 11:11:40'),
(2, 1, 5, NULL, NULL, 100, 'Earned', '2026-01-30 11:33:50'),
(3, 1, NULL, NULL, NULL, 800, 'Earned', '2026-01-24 21:07:18'),
(4, 1, NULL, NULL, NULL, 1000, 'Earned', '2026-01-25 21:12:12'),
(5, 1, NULL, NULL, NULL, 1500, 'Redemption', '2026-01-25 21:13:02'),
(6, 1, NULL, NULL, NULL, 867, 'Earned', '2026-01-26 21:13:54'),
(7, 1, NULL, NULL, NULL, 200, 'Redemption', '2026-01-26 21:13:54'),
(10, 1, NULL, NULL, NULL, 367, 'Earned', '2026-01-27 21:20:00'),
(11, 1, NULL, NULL, NULL, 780, 'Earned', '2026-01-28 21:20:00'),
(12, 1, NULL, NULL, NULL, 670, 'Earned', '2026-01-29 21:21:02'),
(13, 1, NULL, NULL, NULL, 1200, 'Redemption', '2026-01-29 21:21:02'),
(14, 1, NULL, 1, NULL, 4000, 'Redemption', '2026-01-30 21:25:08'),
(15, 1, NULL, 1, NULL, 4000, 'Refund', '2026-01-30 21:33:14'),
(16, 1, 8, NULL, NULL, 200, 'Earned', '2026-01-30 21:47:24'),
(17, 1, NULL, 2, NULL, 2000, 'Redemption', '2026-01-31 00:12:26'),
(18, 1, NULL, 3, NULL, 5000, 'Redemption', '2026-02-08 22:30:07'),
(19, 1, NULL, 4, NULL, 1500, 'Redemption', '2026-02-09 01:08:55'),
(20, 1, NULL, 5, NULL, 4000, 'Redemption', '2026-02-09 12:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `reward`
--

CREATE TABLE `reward` (
  `reward_id` int(11) UNSIGNED NOT NULL,
  `partner_id` int(11) UNSIGNED NOT NULL,
  `admin_id` int(11) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `required_points` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `reward_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reward`
--

INSERT INTO `reward` (`reward_id`, `partner_id`, `admin_id`, `status`, `title`, `description`, `required_points`, `stock`, `reward_img`) VALUES
(1, 4, 3, 'Available', '5% Cashback Voucher At Blabla Mart', 'Enjoy 5% Cashback At Blabla Mart, T&C Applied', 1000, 98, '../../uploads/rewardImages/4.14022026_224327.jpg'),
(2, 4, 3, 'Available', 'Free Morning Coffee at Blabla Mart', 'Redeem a coffee before 12PM at Blabla Mart for free with this voucher', 1500, 97, '../../uploads/rewardImages/blablacoffee.jpg'),
(3, 4, 3, 'Available', 'Buy 1 Free 1 Cupcake at Blabla Mart', 'The more the merrier, share the free one with your love ones', 2000, 99, '../../uploads/rewardImages/blablacupcake.jpg'),
(4, 4, 3, 'Available', '50% Off On Second Purchase Of Onigiri at Blabla Mart', 'Enjoy our mouth watering onigiri in a wide varities of flavour', 1500, 99, '../../uploads/rewardImages/blablaOnigiri.jpg'),
(5, 4, 3, 'Available', 'Free Breakfast Sandwich at Blabla Mart', 'Enjoy a free breakfast sandwich', 2000, 98, '../../uploads/rewardImages/blablaBreakfast.jpg\r\n'),
(6, 14, 3, 'Available', 'Rito Games Limited Edition Tumbler', 'Rito Games Limited Edition Tumbler, Aluminium Material Limited 100 Unit!', 5000, 99, '../../uploads/rewardImages/ritoTumbler.jpg'),
(7, 15, 3, 'Available', 'Mans Black Hoodie From Asia Pacific University', 'APU Black Hoodie, Sleek and Fashion Design', 4000, 97, '../../uploads/rewardImages/Mans Black Hoodie From Asia Pacific University.png'),
(8, 15, 3, 'Available', 'Portable Drive (32GB) From Asia Pacific University', 'Portable Drive from APU, bring your data to anywhere in this small size drive', 7000, 99, '../../uploads/rewardImages/Portable Drive (32GB) From Asia Pacific University.png'),
(9, 15, 3, 'Available', 'Unisex Cap From Asia Pacific University', 'This Well Designed Cap Makes The Person Who Wears It Look More Confident (No Cap) ', 4000, 99, '../../uploads/rewardImages/Unisex Cap From Asia Pacific University.png'),
(10, 15, 3, 'Available', 'Commuter Backpack (Black) From Asia Pacific University ', 'This 23L Commuter Backpack Can Fit A 17inch Laptop And All You Daily Needs  ', 6000, 99, '../../uploads/rewardImages/Commuter Backpack (Black) From Asia Pacific University .png');

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

--
-- Dumping data for table `reward_redemption`
--

INSERT INTO `reward_redemption` (`redemption_id`, `reward_id`, `user_id`, `points_spent`, `status`, `redemption_time`) VALUES
(1, 7, 1, 4000, 'Refunded', '2026-01-30 21:25:08'),
(2, 5, 1, 2000, 'Pending', '2026-01-31 00:12:26'),
(3, 6, 1, 5000, 'Pending', '2026-02-08 22:30:07'),
(4, 2, 1, 1500, 'Pending', '2026-02-09 01:08:55'),
(5, 7, 1, 4000, 'Pending', '2026-02-09 12:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Member'),
(3, 'Moderator'),
(4, 'Partner&Sponsor');

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
  `bio` varchar(255) NOT NULL DEFAULT 'Ops, this person is too lazy to set their bio',
  `total_point_accumulated` int(11) NOT NULL DEFAULT 0,
  `point_balance` int(11) NOT NULL DEFAULT 0,
  `active_status` tinyint(1) NOT NULL DEFAULT 1,
  `register_date` datetime NOT NULL DEFAULT current_timestamp(),
  `profile_photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `role_id`, `username`, `password_hash`, `email`, `bio`, `total_point_accumulated`, `point_balance`, `active_status`, `register_date`, `profile_photo`) VALUES
(1, 2, 'beluga', '$2y$10$MdDDPyoXQreW7t7.452Miuy6N9Doqyp9./S9JkOQP.NMocg299RIi', 'beluga123@gmail.com', 'I love coding', 19867, 7367, 1, '2025-12-27 14:05:08', '../../uploads/profileImg/jingyu.jpeg'),
(2, 3, 'blabla', '$2y$10$BEa6OrgP5lkgtc6PkWa1d.whbpw46DSTaDYNqdYMUtdiEkLxvf4qW', 'blabla@mail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2025-12-27 14:08:51', '../../uploads/profileImg/nicholas.jpeg'),
(3, 1, 'violet', '$2y$10$fUOAGSPh1AiF9FFDyPW9Wev.4UQNwrVELFiL2PYKM/3Y9lb0Z2obW', 'violet@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2025-12-27 14:14:05', '../../uploads/profileImg/profile_3_14022026_075114.png'),
(4, 4, 'runaway(blablaMart)', '$2y$10$fUOAGSPh1AiF9FFDyPW9Wev.4UQNwrVELFiL2PYKM/3Y9lb0Z2obW', 'yessur@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2025-12-28 12:35:48', '../../uploads/profileImg/isak.png'),
(5, 2, 'Mr Hamster', '$2y$10$b2SccqvcJEzd1yIaO0u/Q.BGAC4USevLKd3gknQftAF/jq27GEEj.', 'nuts@gmail.com', 'Hallo, I am Mr Hamster. I am a hamster ^_^', 10780, 10780, 1, '2026-01-04 22:11:32', '../../uploads/profileImg/profile_5_1769695941.gif'),
(6, 2, 'Yeru', '$2y$10$QnwXzv79v4P7Y9aWx9ehbuV5WAEDcJTXQNfD00NRYpTt/DmSuSQHe', 'yeru@gmail.com', 'Ops, this person is too lazy to set their bio', 5001, 5001, 1, '2026-01-29 22:24:51', '../../uploads/profileImg/profile_6_1769696701.gif'),
(7, 2, 'Huh', '$2y$10$1NWRmTZ.manjFGED27qbhO7TV2XHYolXhWmYv4ybOuGvCNn72I.BW', 'huh@gmail.com', 'Ops, this person is too lazy to set their bio', 6767, 6767, 1, '2026-01-29 22:26:58', '../../uploads/profileImg/profile_7_1769696831.gif'),
(8, 2, 'bongo', '$2y$10$c6MA6B.4ots0wwU2rEkdv.CpzaZ5w7PmYCFbvsL5Fiyl3VtnSDKWS', 'bongobongo@gmail.com', 'Bongo Cat', 14950, 14950, 1, '2026-01-29 22:27:47', '../../uploads/profileImg/profile_8_1769696877.gif'),
(9, 2, 'elmore', '$2y$10$VduBToZbirmAM1cr/zb9rObHoh90/eI5VjmTgb2yNmwThraPMNV0C', 'elmore@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2026-01-29 22:29:38', '../../uploads/profileImg/profile_9_1769696997.gif'),
(10, 2, 'sagae', '$2y$10$Q6zJnPCwswIjhfJ8HwPAdO9UkxADWm.f7Ifhh6jd8YM056I.dREwq', 'sagae@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2026-01-29 22:31:42', '../../uploads/profileImg/profile_10_1769697112.gif'),
(11, 2, 'meganight', '$2y$10$tXj27AsMtxyICjbrVdmAbeZejVNiDkvaBvU//Zx2dEOCw7Km.8bny', 'megaaa@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2026-01-29 22:32:22', '../../uploads/profileImg/profile_11_1769697205.gif'),
(12, 2, 'lokchart', '$2y$10$KLlbYY8bulmlbnCmkMRwLePoVGPS65SPMR3.CnmXpoE9ouoySpNae', '67@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2026-01-29 22:35:15', '../../uploads/profileImg/profile_12_1769697325.gif'),
(13, 2, 'camber', '$2y$10$t1nMn/Ayaxan.h0T2pwJB.hy2lId6.GGeghqmi1CZaHYUbWCNml/W', 'cber@gmail.com', 'Does anyone have funds, mercci', 0, 0, 1, '2026-01-29 22:37:33', '../../uploads/profileImg/profile_13_1769697468.gif'),
(14, 4, 'RitoGames', '$2y$10$U.i1/DL/9PktcxZgjNWDtewslBHXL5AV.cmMFPURFQg.u7qBo0BIS', 'rito@gmail.com', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2026-01-29 22:46:39', '../../uploads/profileImg/ritoGame.jpg'),
(15, 4, 'APU', '$2y$10$EpL6mBj6eWz8YTvSRpOXG.MnQc7zAGO2zlD7gdPNToHi3QIEj5alK', 'apu@mail.apu.edu.my', 'Ops, this person is too lazy to set their bio', 0, 0, 1, '2026-01-29 23:14:13', '../../uploads/profileImg/apu.jpg');

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
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `admin_id` (`admin_id`);

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
  MODIFY `activity_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `activity_submission`
--
ALTER TABLE `activity_submission`
  MODIFY `submission_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `badge`
--
ALTER TABLE `badge`
  MODIFY `badge_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `badge_obtain`
--
ALTER TABLE `badge_obtain`
  MODIFY `obtain_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `friendship`
--
ALTER TABLE `friendship`
  MODIFY `friendship_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `partner_point_transaction`
--
ALTER TABLE `partner_point_transaction`
  MODIFY `partner_transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `point_transaction`
--
ALTER TABLE `point_transaction`
  MODIFY `transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reward`
--
ALTER TABLE `reward`
  MODIFY `reward_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reward_redemption`
--
ALTER TABLE `reward_redemption`
  MODIFY `redemption_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
-- Constraints for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD CONSTRAINT `contact_us_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`user_id`);

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
