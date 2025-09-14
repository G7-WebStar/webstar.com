-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 14, 2025 at 02:58 PM
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

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `email`, `password`) VALUES
(1, 'john.doe@gmail.com', 'Pass@123');

-- --------------------------------------------------------

--
-- Table structure for table `announcementnotes`
--

CREATE TABLE `announcementnotes` (
  `noteID` int(11) NOT NULL,
  `announcementID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `notedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcementnotes`
--

INSERT INTO `announcementnotes` (`noteID`, `announcementID`, `userID`, `notedAt`) VALUES
(1, 1, 2, '2025-09-04 21:31:28');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcementID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `userID` int(5) NOT NULL,
  `announcementTitle` varchar(255) NOT NULL,
  `announcementContent` text NOT NULL,
  `announcementDate` date NOT NULL,
  `announcementTime` time NOT NULL,
  `isRequired` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcementID`, `courseID`, `userID`, `announcementTitle`, `announcementContent`, `announcementDate`, `announcementTime`, `isRequired`) VALUES
(1, 1, 2, 'Project Deadline Reminder', 'Final project is due next week. Submit via LMS.', '2025-08-30', '09:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `assessmentID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type` enum('Task','Exam') DEFAULT 'Task',
  `deadline` date NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessmentID`, `courseID`, `title`, `type`, `deadline`, `createdAt`) VALUES
(1, 1, 'Activity #1', 'Task', '2025-09-09', '2025-09-04 22:00:15');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `courseID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `courseCode` varchar(50) NOT NULL,
  `courseTitle` varchar(255) NOT NULL,
  `courseImage` varchar(255) NOT NULL,
  `yearSection` varchar(50) NOT NULL,
  `schedule` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`courseID`, `userID`, `courseCode`, `courseTitle`, `courseImage`, `yearSection`, `schedule`, `code`) VALUES
(1, 1, 'WEBDEV101', 'Web Development', 'webdev101.jpg', '2', 'MWF 10:00–11:30AM', 'WD-2A-2025');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollmentID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `courseID` int(5) NOT NULL,
  `yearSection` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollmentID`, `userID`, `courseID`, `yearSection`) VALUES
(1, 2, 1, 2023);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `fileID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `filePath` text NOT NULL,
  `uploadedBy` int(11) NOT NULL,
  `uploadedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileID`, `courseID`, `fileName`, `filePath`, `uploadedBy`, `uploadedAt`) VALUES
(1, 1, 'css-grid-cheatsheet.pdf', 'uploads/course110/css-grid-cheatsheet.pdf', 1, '2025-08-30 10:30:00');

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
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `messageID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `messageText` text NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `isRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inbox`
--

INSERT INTO `inbox` (`messageID`, `userID`, `courseID`, `messageText`, `createdAt`, `isRead`) VALUES
(1, 2, 1, 'Prof. Christian James has posted a new assignment.', '2024-01-31 08:04:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `leaderboardID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `timeRange` varchar(10) NOT NULL,
  `periodStart` date NOT NULL,
  `xpPoints` int(11) NOT NULL,
  `rank` int(11) NOT NULL,
  `previousRank` int(11) NOT NULL,
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`leaderboardID`, `courseID`, `userID`, `timeRange`, `periodStart`, `xpPoints`, `rank`, `previousRank`, `updatedAt`) VALUES
(1, 1, 2, 'Weekly', '2025-08-25', 450, 3, 5, '2025-08-30 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lessonID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `lessonTitle` varchar(255) NOT NULL,
  `lessonDescription` text NOT NULL,
  `lessonContent` text NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `lessonType` varchar(255) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lessonID`, `courseID`, `userID`, `lessonTitle`, `lessonDescription`, `lessonContent`, `attachment`, `lessonType`, `createdAt`, `updatedAt`) VALUES
(1, 1, 2, 'Introduction to CSS Grid', 'Basics of layout with CSS Grid', 'Learn how to create grid layouts', 'css-grid.pdf', 'Lecture', '2025-08-30 09:00:00', '2025-08-30 09:00:00');

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

--
-- Dumping data for table `points`
--

INSERT INTO `points` (`pointsID`, `userID`, `sourceType`, `pointsEarned`, `dateEarned`) VALUES
(1, 2, 'Exam', 50, '2025-08-30 08:28:27');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `programID` int(11) NOT NULL,
  `programName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`programID`, `programName`) VALUES
(1, 'BSIT');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `reportID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `totalXP` int(11) NOT NULL,
  `allTimeRank` int(11) NOT NULL,
  `assignmentScorePercent` decimal(10,0) NOT NULL,
  `assesmentScorePercent` decimal(10,0) NOT NULL,
  `generatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`reportID`, `courseID`, `userID`, `totalXP`, `allTimeRank`, `assignmentScorePercent`, `assesmentScorePercent`, `generatedAt`) VALUES
(1, 1, 2, 4500, 10, 89, 92, '2025-08-30 14:26:32');

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `todoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `dueDate` date NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todo`
--

INSERT INTO `todo` (`todoID`, `userID`, `courseID`, `title`, `type`, `status`, `dueDate`, `createdAt`, `updatedAt`) VALUES
(1, 4, 1, 'Review CSS Grid and Flexbox', 'Study', 'Pending', '2025-09-01', '2025-08-29 09:00:00', '2025-08-29 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `userinfo`
--

CREATE TABLE `userinfo` (
  `userInfoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `profilePicture` varchar(100) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `middleName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `studentID` varchar(50) NOT NULL,
  `programID` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `yearLevel` varchar(20) NOT NULL,
  `yearSection` int(11) NOT NULL,
  `schoolEmail` varchar(50) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `facebookLink` text NOT NULL,
  `linkedInLink` text NOT NULL,
  `instagramLink` text NOT NULL,
  `createdAt` datetime NOT NULL,
  `isNewUser` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userinfo`
--

INSERT INTO `userinfo` (`userInfoID`, `userID`, `profilePicture`, `firstName`, `middleName`, `lastName`, `studentID`, `programID`, `gender`, `yearLevel`, `yearSection`, `schoolEmail`, `contactNumber`, `facebookLink`, `linkedInLink`, `instagramLink`, `createdAt`, `isNewUser`) VALUES
(1, 2, 'prof.png', 'Jane', 'Mendoza', 'Smith', '202310001', '1', 'Female', '2', 2023, 'jane.smith@university.edu', '+639123456789', 'https://facebook.com/jane.smith', 'https://linkedin.com/in/jane-smith', 'https://instagram.com/jane.smith', '2025-08-30 08:18:53', 0),
(2, 1, 'prof.png', 'Chistian James', 'Dom', 'Torillo', '202310002', '1', 'Male', '2', 2023, 'james.dom@university.edu', '+639123456789', 'https://facebook.com/james.dom', 'https://linkedin.com/in/james-dom', 'https://instagram.com/james.dom', '2025-08-30 08:18:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` varchar(8) NOT NULL DEFAULT 'user',
  `userName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `password`, `email`, `role`, `userName`) VALUES
(1, 'Pass@123', 'john.doe@gmail.com', 'admin', 'johndoe'),
(2, 'Hello@world', 'jane.smith@example.com', 'student', 'janesmith');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `announcementnotes`
--
ALTER TABLE `announcementnotes`
  ADD PRIMARY KEY (`noteID`),
  ADD KEY `announcementId` (`announcementID`),
  ADD KEY `fk_announcementNotes_user` (`userID`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcementID`);

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`assessmentID`),
  ADD KEY `courseID` (`courseID`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courseID`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollmentID`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`fileID`);

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
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`messageID`),
  ADD KEY `courseID` (`courseID`),
  ADD KEY `userID` (`userID`) USING BTREE;

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`leaderboardID`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lessonID`);

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
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`programID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`reportID`);

--
-- Indexes for table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`todoID`);

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
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcementnotes`
--
ALTER TABLE `announcementnotes`
  MODIFY `noteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `courseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `fileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `leaderboardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lessonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `minigames`
--
ALTER TABLE `minigames`
  MODIFY `gameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
  MODIFY `pointsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `programID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `userinfo`
--
ALTER TABLE `userinfo`
  MODIFY `userInfoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
