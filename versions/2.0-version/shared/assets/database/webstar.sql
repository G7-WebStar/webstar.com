-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 06:37 AM
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
-- Database: `webstar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `followID` int(11) NOT NULL,
  `followingID` int(11) NOT NULL,
  `followedID` int(11) NOT NULL,
  `dateFollowed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gameresult`
--

CREATE TABLE `gameresult` (
  `gameResultID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `gameID` int(11) NOT NULL,
  `score` int(100) NOT NULL,
  `pointsEarned` int(100) NOT NULL,
  `dateTaken` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `minigames`
--

CREATE TABLE `minigames` (
  `gameID` int(11) NOT NULL,
  `gameName` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `lessonID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE `points` (
  `pointsID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `sourceType` varchar(50) NOT NULL,
  `pointsEarned` int(100) NOT NULL,
  `dateEarned` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userinfo`
--

CREATE TABLE `userinfo` (
  `userInfoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `profilePicture` varchar(300) NOT NULL,
  `userBio` varchar(300) NOT NULL,
  `totalPoints` varchar(3000) NOT NULL,
  `createdAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` varchar(8) NOT NULL,
  `userName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`followID`);

--
-- Indexes for table `gameresult`
--
ALTER TABLE `gameresult`
  ADD PRIMARY KEY (`gameResultID`);

--
-- Indexes for table `minigames`
--
ALTER TABLE `minigames`
  ADD PRIMARY KEY (`gameID`);

--
-- Indexes for table `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`pointsID`);

--
-- Indexes for table `userinfo`
--
ALTER TABLE `userinfo`
  ADD PRIMARY KEY (`userInfoID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `followID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gameresult`
--
ALTER TABLE `gameresult`
  MODIFY `gameResultID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `minigames`
--
ALTER TABLE `minigames`
  MODIFY `gameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
  MODIFY `pointsID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userinfo`
--
ALTER TABLE `userinfo`
  MODIFY `userInfoID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
