-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 11:08 AM
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
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activityID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `description` text NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activityID`, `userID`, `description`, `createdAt`) VALUES
(1, 2, 'Christian James submitted “Activity #1” Quest.', '2025-10-22 10:37:06'),
(2, 2, 'Christian James submitted “Activity #1” Quest.', '2025-10-28 22:37:06'),
(3, 2, 'Christian James submitted “Activity #1” Quest.', '2025-10-28 22:37:06');

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
(1, 1, 1, 'Project Deadline Reminder', 'Final project is due next week. Submit via LMS.', '2025-08-30', '09:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `assessmentID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `assessmentTitle` varchar(100) NOT NULL,
  `type` enum('Task','Test') DEFAULT 'Task',
  `deadline` date NOT NULL,
  `deadlineEnabled` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessmentID`, `courseID`, `assessmentTitle`, `type`, `deadline`, `deadlineEnabled`, `createdAt`) VALUES
(1, 1, 'Activity #1', 'Task', '2025-10-15', 0, '2025-09-09 23:00:15'),
(2, 1, 'Test #1', 'Test', '2025-11-06', 0, '2025-09-04 22:00:15'),
(3, 2, 'Activity #2', 'Task', '2025-10-23', 0, '2025-10-22 22:00:15'),
(4, 2, 'Activity #1', 'Task', '2025-09-11', 0, '2025-09-04 22:00:15'),
(5, 1, 'asdasd', 'Task', '2025-11-08', 0, '2025-11-03 10:36:42'),
(6, 1, 'fsfdsfds', 'Test', '2025-11-15', 0, '2025-11-03 03:53:07'),
(7, 1, 'sadsaf', 'Task', '2025-11-21', 0, '2025-11-03 10:56:27');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignmentID` int(5) NOT NULL,
  `assessmentID` int(11) NOT NULL,
  `assignmentDescription` varchar(500) NOT NULL,
  `assignmentPoints` int(5) NOT NULL,
  `rubricID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignmentID`, `assessmentID`, `assignmentDescription`, `assignmentPoints`, `rubricID`) VALUES
(1, 1, 'Attached is a Google Doc that you can edit.\n\nIn Figma, design a “404 Not Found” page.\n\nCreate two versions, one for the mobile and one for the desktop. Turn in when done.\n\nTurn in when done.\n\n', 100, NULL),
(2, 4, 'Attached is a Google Doc that you can edit.\r\n\r\nIn Figma, design a “404 Not Found” page.\r\n\r\nCreate two versions, one for the mobile and one for the desktop. Turn in when done.\r\n\r\nTurn in when done.\r\n\r\n', 100, NULL),
(3, 2, 'Attached is a Google Doc that you can edit.\r\n\r\nIn Figma, design a “404 Not Found” page.\r\n\r\nCreate two versions, one for the mobile and one for the desktop. Turn in when done.\r\n\r\nTurn in when done.\r\n\r\n', 100, NULL),
(4, 5, '', 0, NULL),
(5, 7, '', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `badgeID` int(11) NOT NULL,
  `badgeName` varchar(100) NOT NULL,
  `badgeDescription` varchar(255) DEFAULT NULL,
  `badgeIcon` varchar(255) DEFAULT NULL,
  `badgeCategory` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`badgeID`, `badgeName`, `badgeDescription`, `badgeIcon`, `badgeCategory`) VALUES
(1, 'Perfect Scorer', 'Awarded to learners who achieved a flawless, 100% score on any test or task — true mastery!', 'perfect_scorer.png', 'Achievement'),
(2, 'Top Scorer', 'Granted to the learner who outperformed everyone with the highest score in a test or activity.', 'top_scorer.png', 'Achievement'),
(3, 'Early Finisher', 'Given to those who beat the clock by submitting their task or test at least one day ahead of the deadline.', 'early_finisher.png', 'Achievement'),
(4, 'First Finisher', 'Presented to the fastest learner who submitted their task or test before anyone else.', 'first_finisher.png', 'Achievement'),
(5, 'High Achiever', 'Earned by learners who consistently score 90% or higher on any test or task — excellence at its best!', 'high_achiever.png', 'Achievement'),
(6, 'Quick Thinker', 'Awarded to learners who completed a quiz or exam with impressive speed and accuracy.', 'quick_thinker.png', 'Achievement'),
(7, 'Leaderboard Legend', 'Crowned to the ultimate top performer who reigned #1 on the leaderboard for the week or course.', 'leaderboard_legend.png', 'Leaderboard'),
(8, 'Top 3 Titan', 'Bestowed upon learners who claimed a spot in the Top 3 leaderboard — the elite ranks!', 'top_3_titan.png', 'Leaderboard'),
(9, 'Top 10 Challenger', 'Given to determined learners who earned a place in the Top 10 leaderboard.', 'top_10_challenger.png', 'Leaderboard'),
(10, 'Elite Performer', 'Celebrating learners who maintained a Top 3 ranking across multiple modules or courses — unstoppable!', 'elite_performer.png', 'Leaderboard'),
(11, 'Star Performer', 'Awarded to learners who delivered an exceptional performance in a task, activity, or project — a true standout!', 'star_performer.png', 'Recognition'),
(12, 'Most Improved', 'Given to learners who have shown remarkable growth and steady progress throughout their learning journey.', 'most_improved.png', 'Recognition'),
(13, 'Exemplary Effort', 'Recognizes learners who consistently demonstrate hard work, perseverance, and dedication — effort that truly shines!', 'exemplary_effort.png', 'Recognition'),
(14, 'Creative Thinker', 'Granted to learners who showcase originality and creativity in solving problems or completing their work.', 'creative_thinker.png', 'Recognition'),
(15, 'Insightful Thinker', 'For learners whose answers, reflections, or analyses show impressive depth and understanding.', 'insightful_thinker.png', 'Recognition'),
(16, 'Well-Documented Work', 'Awarded for submissions that are exceptionally clear, organized, and well-presented — a mark of professionalism!', 'well_documented_work.png', 'Recognition'),
(17, 'Pixel Perfectionist', 'Awarded to learners who craft layouts with flawless alignment, clean visuals, and pixel-perfect precision.', 'pixel_perfectionist.png', 'Development'),
(18, 'Function Wizard', 'Bestowed upon learners who write efficient, elegant, and well-structured JavaScript or PHP functions — pure coding magic!', 'function_wizard.png', 'Development'),
(19, 'Component Crafter', 'Given to learners who build reusable, modular, and well-organized components that make development shine.', 'component_crafter.png', 'Development'),
(20, 'CSS Sorcerer', 'Recognizes mastery in CSS — for creating stunning, responsive, and creative designs that captivate users.', 'css_sorcerer.png', 'Development'),
(21, 'UI Enchanter', 'Celebrates learners who bring interfaces to life with smooth interactions, seamless animations, and user-friendly flow.', 'ui_enchanter.png', 'Development');

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
  `isActive` int(3) NOT NULL DEFAULT 1,
  `code` varchar(20) NOT NULL,
  `section` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`courseID`, `userID`, `courseCode`, `courseTitle`, `courseImage`, `isActive`, `code`, `section`) VALUES
(1, 1, 'COMP-006', 'Web Development', 'webdev.jpg', 1, '123456', 'BSIT 4-1'),
(2, 1, 'GEED-007', 'Web Development 2', 'webdev.jpg', 1, '789ABC', 'BSIT 4-1'),
(3, 1, 'MM-102', 'Multimedia', '72793b45-c2cc-4ad2-ad29-5b395ca0c24d.jpg', 1, '1Z8AQ8', 'BSIT 4-1');

-- --------------------------------------------------------

--
-- Table structure for table `courseschedule`
--

CREATE TABLE `courseschedule` (
  `courseScheduleID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courseschedule`
--

INSERT INTO `courseschedule` (`courseScheduleID`, `courseID`, `day`, `startTime`, `endTime`, `createdAt`) VALUES
(1, 3, 'Monday', '18:06:00', '06:06:00', '2025-11-03 18:07:13'),
(2, 3, 'Monday', '18:07:00', '19:07:00', '2025-11-03 18:07:13'),
(3, 3, 'Monday', '18:08:00', '20:07:00', '2025-11-03 18:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `criterionID` int(11) NOT NULL,
  `rubricID` int(11) NOT NULL,
  `criteriaTitle` varchar(100) NOT NULL,
  `criteriaDescription` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 2, 1, 2023),
(2, 2, 2, 2023),
(3, 3, 1, 2023),
(4, 4, 1, 2023),
(5, 5, 1, 2023),
(6, 6, 1, 2023),
(7, 7, 1, 2023),
(8, 8, 1, 2023),
(9, 9, 1, 2023),
(10, 10, 1, 2023),
(11, 11, 1, 2023),
(12, 12, 1, 2023),
(13, 2, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackID` int(11) NOT NULL,
  `senderID` int(11) NOT NULL,
  `receiverID` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `fileID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `userID` int(5) NOT NULL,
  `announcementID` int(5) DEFAULT NULL,
  `lessonID` int(5) DEFAULT NULL,
  `assignmentID` int(5) DEFAULT NULL,
  `fileAttachment` varchar(255) NOT NULL,
  `fileTitle` varchar(50) NOT NULL,
  `fileLink` varchar(100) NOT NULL,
  `uploadedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileID`, `courseID`, `userID`, `announcementID`, `lessonID`, `assignmentID`, `fileAttachment`, `fileTitle`, `fileLink`, `uploadedAt`) VALUES
(1, 1, 1, 1, 1, NULL, 'Web Development Course Material', 'Web Development', 'https://gmail.com/lesson1,https://gmail.com/lesson1.1', '2025-08-30 10:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `inboxID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `messageText` text NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `isRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inbox`
--

INSERT INTO `inbox` (`inboxID`, `enrollmentID`, `messageText`, `createdAt`, `isRead`) VALUES
(1, 1, 'Prof. Christian James has posted a new assignment.', '2024-01-31 08:04:00', 1),
(2, 2, 'Prof. Christian James has posted a new assignment.', '2024-01-31 08:04:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `leaderboardID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `timeRange` varchar(10) NOT NULL,
  `periodStart` date NOT NULL,
  `xpPoints` int(11) NOT NULL,
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`leaderboardID`, `enrollmentID`, `timeRange`, `periodStart`, `xpPoints`, `updatedAt`) VALUES
(1, 1, 'Weekly', '2025-08-25', 450, '2025-08-30 12:00:00'),
(2, 2, 'Weekly', '2025-08-25', 450, '2025-08-30 12:00:00'),
(3, 1, 'Weekly', '2025-09-28', 100, '2025-09-28 16:50:22'),
(4, 3, 'Weekly', '2025-09-28', 400, '2025-09-28 16:58:09'),
(5, 4, 'Weekly', '2025-09-01', 152, '2025-09-28 21:03:44'),
(6, 5, 'Weekly', '2025-09-01', 189, '2025-09-28 21:03:44'),
(7, 6, 'Weekly', '2025-09-01', 215, '2025-09-28 21:03:44'),
(8, 7, 'Weekly', '2025-09-01', 176, '2025-09-28 21:03:44'),
(9, 8, 'Weekly', '2025-09-01', 221, '2025-09-28 21:03:44'),
(10, 9, 'Weekly', '2025-09-01', 167, '2025-09-28 21:03:44'),
(11, 10, 'Weekly', '2025-09-01', 195, '2025-09-28 21:03:44'),
(12, 11, 'Weekly', '2025-09-01', 214, '2025-09-28 21:03:44'),
(13, 12, 'Monthly', '2025-09-01', 95, '2025-09-28 21:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lessonID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `lessonTitle` varchar(255) NOT NULL,
  `lessonDescription` text NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lessonID`, `courseID`, `lessonTitle`, `lessonDescription`, `createdAt`) VALUES
(1, 1, 'Lesson 1: Introduction to CSS Grid', '1. Explain what HTML is and its role in web development.\n2. Identify the basic structure of an HTML document.\n3. Use common HTML tags such as headings, paragraphs, and links. \n4. Create a simple webpage using basic HTML elements.', '2025-08-30 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `levelID` int(11) NOT NULL,
  `criterionID` int(11) NOT NULL,
  `levelTitle` varchar(100) NOT NULL,
  `levelDescription` varchar(500) NOT NULL,
  `points` decimal(5,2) NOT NULL
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
(1, 2, 'Test', 50, '2025-08-30 08:28:27');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `profileID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `bio` varchar(255) NOT NULL DEFAULT 'Welcome to my Webstar profile!',
  `webstars` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profileID`, `userID`, `bio`, `webstars`) VALUES
(1, 2, 'Welcome to my Webstar profile!', 0);

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `programID` int(11) NOT NULL,
  `programName` varchar(100) NOT NULL,
  `programInitial` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`programID`, `programName`, `programInitial`) VALUES
(1, 'Bachelor of Technology and Livelihood Education major in Information and Communication Technology', 'BTLED-ICT'),
(2, 'Bachelor of Science in Electrical Engineering', 'BSEE'),
(3, 'Bachelor of Science in Electronics Engineering', 'BSECE'),
(4, 'Bachelor of Science in Entrepreneurship', 'BSENT'),
(5, 'Bachelor of Science in Industrial Engineering', 'BSIE'),
(6, 'Bachelor of Science in Information Technology', 'BSIT'),
(7, 'Bachelor of Public Administration with specialization in Fiscal Administration', 'BPAFA'),
(8, 'Bachelor of Science in Psychology', 'BSPSY'),
(9, 'Diploma in Information Technology', 'DIT'),
(10, 'Diploma in Office Management Technology - Legal Office Management', 'DOMTLOM');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `reportID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `totalXP` int(11) NOT NULL,
  `allTimeRank` int(11) NOT NULL,
  `generatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`reportID`, `enrollmentID`, `totalXP`, `allTimeRank`, `generatedAt`) VALUES
(1, 1, 550, 1, '2025-11-03 11:24:33'),
(2, 3, 400, 2, '2025-11-03 11:24:33'),
(3, 8, 221, 3, '2025-11-03 11:24:33'),
(4, 6, 215, 4, '2025-11-03 11:24:33'),
(5, 11, 214, 5, '2025-11-03 11:24:33'),
(6, 10, 195, 6, '2025-11-03 11:24:33'),
(7, 5, 189, 7, '2025-11-03 11:24:33'),
(8, 7, 176, 8, '2025-11-03 11:24:33'),
(9, 9, 167, 9, '2025-11-03 11:24:33'),
(10, 4, 152, 10, '2025-11-03 11:24:33'),
(11, 12, 95, 11, '2025-11-03 11:24:33'),
(12, 2, 450, 1, '2025-11-01 12:05:23');

-- --------------------------------------------------------

--
-- Table structure for table `rubric`
--

CREATE TABLE `rubric` (
  `rubricID` int(11) NOT NULL,
  `rubricTitle` varchar(100) NOT NULL,
  `rubricType` varchar(20) NOT NULL,
  `userID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `scoreID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `assignmentID` int(5) DEFAULT NULL,
  `testID` int(5) DEFAULT NULL,
  `score` int(5) NOT NULL,
  `feedback` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`scoreID`, `userID`, `assignmentID`, `testID`, `score`, `feedback`) VALUES
(1, 2, 1, NULL, 100, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `settingsID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `courseUpdateEnabled` tinyint(4) NOT NULL DEFAULT 0,
  `questDeadlineEnabled` tinyint(4) NOT NULL DEFAULT 0,
  `announcementEnabled` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`settingsID`, `userID`, `courseUpdateEnabled`, `questDeadlineEnabled`, `announcementEnabled`) VALUES
(1, 2, 1, 1, 1),
(3, 3, 0, 0, 0),
(4, 4, 0, 0, 0),
(5, 5, 0, 0, 0),
(6, 6, 0, 0, 0),
(7, 7, 0, 0, 0),
(8, 8, 0, 0, 0),
(9, 9, 0, 0, 0),
(10, 10, 0, 0, 0),
(11, 11, 0, 0, 0),
(12, 12, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `studentbadges`
--

CREATE TABLE `studentbadges` (
  `studentBadgeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `badgeID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `earnedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentbadges`
--

INSERT INTO `studentbadges` (`studentBadgeID`, `userID`, `badgeID`, `courseID`, `earnedAt`) VALUES
(1, 2, 1, 1, '2025-10-28 20:16:06'),
(2, 2, 2, 1, '2025-10-28 20:16:06'),
(3, 2, 2, 1, '2025-10-28 20:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submissionID` int(11) NOT NULL,
  `assessmentID` int(11) NOT NULL,
  `scoreID` int(11) NOT NULL,
  `submittedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testquestionchoices`
--

CREATE TABLE `testquestionchoices` (
  `choiceID` int(5) NOT NULL,
  `testQuestionID` int(5) NOT NULL,
  `choiceText` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testquestionchoices`
--

INSERT INTO `testquestionchoices` (`choiceID`, `testQuestionID`, `choiceText`) VALUES
(1, 1, '&lt;h3&gt;'),
(2, 1, '&lt;h6&gt;'),
(3, 1, '&lt;h1&gt;'),
(4, 1, '&lt;head&gt;'),
(5, 2, '&lt;link&gt;'),
(6, 2, '&lt;a&gt;'),
(7, 2, '&lt;href&gt;'),
(8, 2, '&lt;hyper&gt;'),
(9, 3, 'src'),
(10, 3, 'href'),
(11, 3, 'alt'),
(12, 3, 'path'),
(13, 4, '&lt;break&gt;'),
(14, 4, '&lt;br&gt;'),
(15, 4, '&lt;lb&gt;'),
(16, 4, '&lt;line&gt;'),
(17, 5, '&lt;ol&gt;'),
(18, 5, '&lt;ul&gt;'),
(19, 5, '&lt;li&gt;'),
(20, 5, '&lt;list&gt;'),
(21, 6, 'The title shown inside the page'),
(22, 6, 'The title shown on the browser tab'),
(23, 6, 'A tooltip on hover'),
(24, 6, 'A paragraph heading'),
(25, 7, '&lt;ol&gt;'),
(26, 7, '&lt;ul&gt;'),
(27, 7, '&lt;li&gt;'),
(28, 7, '&lt;dl&gt;'),
(29, 8, 'style'),
(30, 8, 'class'),
(31, 8, 'font'),
(32, 8, 'css'),
(33, 9, '&lt;img&gt;'),
(34, 9, '&lt;image&gt;'),
(35, 9, '&lt;pic&gt;'),
(36, 9, '&lt;src&gt;'),
(37, 10, '&lt;tr&gt;'),
(38, 10, '&lt;td&gt;'),
(39, 10, '&lt;th&gt;'),
(40, 10, '&lt;table&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `testquestions`
--

CREATE TABLE `testquestions` (
  `testQuestionID` int(5) NOT NULL,
  `testID` int(5) NOT NULL,
  `testQuestion` varchar(100) NOT NULL,
  `questionType` enum('Multiple Choice','Identification') NOT NULL DEFAULT 'Multiple Choice',
  `testQuestionImage` varchar(50) DEFAULT NULL,
  `correctAnswer` varchar(50) NOT NULL,
  `testQuestionPoints` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testquestions`
--

INSERT INTO `testquestions` (`testQuestionID`, `testID`, `testQuestion`, `questionType`, `testQuestionImage`, `correctAnswer`, `testQuestionPoints`) VALUES
(1, 1, 'Which HTML tag is used to define the largest heading?', 'Multiple Choice', NULL, '&lt;h1&gt;', 100),
(2, 1, 'Which tag is used to define a hyperlink in HTML?', 'Multiple Choice', NULL, '&lt;a&gt;', 100),
(3, 1, 'Which HTML attribute specifies an image source?', 'Multiple Choice', NULL, 'src', 100),
(4, 1, 'Which element is used to insert a line break?', 'Multiple Choice', NULL, '&lt;br&gt;', 100),
(5, 1, 'Which tag is used to create an unordered list?', 'Multiple Choice', NULL, '&lt;ul&gt;', 100),
(6, 1, 'What does the &lt;title&gt; tag define?', 'Multiple Choice', NULL, 'The title shown on the browser tab', 100),
(7, 1, 'Which HTML element is used to display a numbered list?', 'Multiple Choice', NULL, '&lt;ol&gt;', 100),
(8, 1, 'Which HTML attribute is used to define inline CSS styles?', 'Multiple Choice', NULL, 'style', 100),
(9, 1, 'What is the correct HTML tag for inserting an image?', 'Multiple Choice', NULL, '&lt;img&gt;', 100),
(10, 1, 'Which tag is used to create a table row?', 'Multiple Choice', NULL, '&lt;tr&gt;', 100);

-- --------------------------------------------------------

--
-- Table structure for table `testresponses`
--

CREATE TABLE `testresponses` (
  `testResponseID` int(5) NOT NULL,
  `testID` int(5) NOT NULL,
  `testQuestionID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `userAnswer` varchar(50) NOT NULL,
  `isCorrect` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testresponses`
--

INSERT INTO `testresponses` (`testResponseID`, `testID`, `testQuestionID`, `userID`, `userAnswer`, `isCorrect`) VALUES
(1, 0, 0, 2, 'choiceText.userAnswer[0]', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `testID` int(5) NOT NULL,
  `assessmentID` int(5) NOT NULL,
  `generalGuidance` varchar(500) NOT NULL,
  `testTimelimit` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`testID`, `assessmentID`, `generalGuidance`, `testTimelimit`) VALUES
(1, 2, 'Read each question carefully and choose the best answer from the given options. Only one option is correct for each question. Once you move to the next question, you will not be able to return to the previous one, so review your answer before proceeding. The exam will automatically submit when the timer ends. Avoid refreshing or closing the browser during the exam to prevent submission issues.', 600),
(2, 6, '', 12);

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `todoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `assessmentID` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `isRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todo`
--

INSERT INTO `todo` (`todoID`, `userID`, `assessmentID`, `status`, `updatedAt`, `isRead`) VALUES
(1, 2, 1, 'Graded', '2025-10-12 19:00:00', 1),
(2, 2, 4, 'Pending', '2025-10-29 09:00:00', 1),
(3, 2, 2, 'Pending', '2025-10-29 09:00:00', 1);

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
  `studentID` varchar(50) DEFAULT NULL,
  `programID` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `yearLevel` varchar(20) NOT NULL,
  `yearSection` int(11) NOT NULL,
  `schoolEmail` varchar(50) DEFAULT NULL,
  `facebookLink` text DEFAULT NULL,
  `linkedInLink` text DEFAULT NULL,
  `githubLink` text DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `isNewUser` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userinfo`
--

INSERT INTO `userinfo` (`userInfoID`, `userID`, `profilePicture`, `firstName`, `middleName`, `lastName`, `studentID`, `programID`, `gender`, `yearLevel`, `yearSection`, `schoolEmail`, `facebookLink`, `linkedInLink`, `githubLink`, `createdAt`, `isNewUser`) VALUES
(1, 2, 'prof.png', 'Jane', 'Mendoza', 'Smith', '202310001', '1', 'Female', '2', 1, 'jane.smith@university.edu', NULL, NULL, 'https://instagram.com/jane.smith', '2025-08-30 08:18:53', 0),
(2, 1, 'prof.png', 'Christopher Jay', '', 'De Claro', '202310002', '1', 'Male', '2', 1, 'james.dom@university.edu', 'https://facebook.com/james.dom', 'https://linkedin.com/in/james-dom', 'https://instagram.com/james.dom', '2025-08-30 08:18:53', 1),
(3, 3, 'prof.png', 'John', 'Cruz', 'Doe', '202310003', '1', 'Male', '2', 1, 'john.doe@university.edu', '', '', '', '2025-09-28 11:58:33', 0),
(4, 4, 'prof.png', 'Michael', 'A.', 'Lee', '202310003', '1', 'Male', '2', 1, 'michael.lee@school.edu', NULL, NULL, NULL, '2025-09-28 20:59:48', 0),
(5, 5, 'prof.png', 'Sophia', 'B.', 'Garcia', '202310004', '1', 'Female', '2', 1, 'sophia.garcia@school.edu', 'facebook.com/sophia.garcia', 'linkedin.com/in/sophiagarcia', 'instagram.com/sophia.garcia', '2025-09-28 20:59:48', 1),
(6, 6, 'prof.png', 'Daniel', 'C.', 'Kim', '202310005', '1', 'Male', '3', 2, 'daniel.kim@school.edu', 'facebook.com/daniel.kim', 'linkedin.com/in/danielkim', 'instagram.com/daniel.kim', '2025-09-28 20:59:48', 0),
(7, 7, 'prof.png', 'Olivia', 'D.', 'Brown', '202310006', '1', 'Female', '2', 2, 'olivia.brown@school.edu', 'facebook.com/olivia.brown', 'linkedin.com/in/oliviabrown', 'instagram.com/olivia.brown', '2025-09-28 20:59:48', 0),
(8, 8, 'prof.png', 'Ethan', 'E.', 'Wilson', '202310007', '1', 'Male', '2', 1, 'ethan.wilson@school.edu', 'facebook.com/ethan.wilson', 'linkedin.com/in/ethanwilson', 'instagram.com/ethan.wilson', '2025-09-28 20:59:48', 1),
(9, 9, 'prof.png', 'Isabella', 'F.', 'Martin', '202310008', '1', 'Female', '2', 1, 'isabella.martin@school.edu', 'facebook.com/isabella.martin', 'linkedin.com/in/isabellamartin', 'instagram.com/isabella.martin', '2025-09-28 20:59:48', 1),
(10, 10, 'prof.png', 'Liam', 'G.', 'Torres', '202310009', '1', 'Male', '1', 2, 'liam.torres@school.edu', 'facebook.com/liam.torres', 'linkedin.com/in/liamtorres', 'instagram.com/liam.torres', '2025-09-28 20:59:48', 1),
(11, 11, 'prof.png', 'Emma', 'H.', 'Davis', '202310010', '1', 'Female', '2', 1, 'emma.davis@school.edu', 'facebook.com/emma.davis', 'linkedin.com/in/emmadavis', 'instagram.com/emma.davis', '2025-09-28 20:59:48', 1),
(12, 12, 'prof.png', 'Chloe', 'I.', 'Nguyen', '202310011', '1', 'Female', '2', 1, 'chloe.nguyen@school.edu', 'facebook.com/chloe.nguyen', 'linkedin.com/in/chloenguyen', 'instagram.com/chloe.nguyen', '2025-09-28 21:34:51', 1),
(13, 18, 'default.png', 'Admin', '', 'User', NULL, '6', '', '4', 4, 'admin@example.com', NULL, NULL, NULL, '2025-11-03 17:57:47', 1),
(14, 19, 'default.png', 'Christian James', '', 'Torrillo', NULL, '6', '', '4', 1, 'christian@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(15, 20, 'default.png', 'Ayisha Sofhia', '', 'Estoque', NULL, '6', '', '4', 1, 'ayisha@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(16, 21, 'default.png', 'Marielle Alyssa', '', 'Cato', NULL, '6', '', '4', 1, 'marielle@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(17, 22, 'default.png', 'Neil Jeferson', '', 'Vergara', NULL, '6', '', '4', 1, 'neil@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(18, 23, 'default.png', 'Shane Rhyder', '', 'Silverio', NULL, '6', '', '4', 1, 'shane@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 11),
(19, 24, 'default.png', 'Kimberly Joan', '', 'Palla', NULL, '6', '', '4', 1, 'kimberly@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `role` varchar(12) NOT NULL DEFAULT 'student',
  `userName` varchar(50) NOT NULL,
  `status` varchar(15) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `password`, `email`, `role`, `userName`, `status`) VALUES
(1, 'Password123', 'john.doe@gmail.com', 'professor', 'johndoe', 'Active'),
(2, 'Hello@world', 'jane.smith@gmail.com', 'student', 'janesmith', 'Active'),
(3, 'HelloWorld', 'john.doe2@gmail.com', 'student', 'JohnDoe', 'Active'),
(4, 'password123', 'michael.lee@gmail.com', 'student', 'michael_lee', 'Active'),
(5, 'securePass!1', 'sophia.garcia@gmail.com', 'student', 'sophia_garcia', 'Active'),
(6, 'helloWorld9', 'daniel.kim@gmail.com', 'student', 'daniel_kim', 'Active'),
(7, 'qwerty2025', 'olivia.brown@gmail.com', 'student', 'olivia_brown', 'Active'),
(8, 'pass4321', 'ethan.wilson@gmail.com', 'student', 'ethan_wilson', 'Active'),
(9, 'abcXYZ987', 'isabella.martin@gmail.com', 'student', 'isabella_martin', 'Active'),
(10, 'myPass!77', 'liam.torres@gmail.com', 'student', 'liam_torres', 'Active'),
(11, 'safeKey555', 'emma.davis@gmail.com', 'student', 'emma_davis', 'Active'),
(12, 'newPass!11', 'chloe.nguyen@gmail.com', 'student', 'chloe_nguyen', 'Active'),
(18, 'adminpassword', 'admin@example.com', 'admin', 'Administrator', 'active'),
(19, 'devpassword1', 'christian@example.com', 'developer', 'christianjamss', 'active'),
(20, 'devpassword2', 'ayisha@example.com', 'developer', 'ayishaestoque', 'active'),
(21, 'devpassword3', 'marielle@example.com', 'developer', 'mariellecato', 'active'),
(22, 'devpassword4', 'neil@example.com', 'developer', 'neilvergara', 'active'),
(23, 'devpassword5', 'shane@example.com', 'developer', 'shanesilverio', 'active'),
(24, 'devpassword6', 'kimberly@example.com', 'developer', 'kimberlypalla', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activityID`),
  ADD KEY `userID` (`userID`);

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
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignmentID`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`badgeID`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courseID`);

--
-- Indexes for table `courseschedule`
--
ALTER TABLE `courseschedule`
  ADD PRIMARY KEY (`courseScheduleID`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`criterionID`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollmentID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackID`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`fileID`);

--
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`inboxID`);

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
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`levelID`);

--
-- Indexes for table `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`pointsID`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`profileID`),
  ADD KEY `profile_ibfk_1` (`userID`);

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
-- Indexes for table `rubric`
--
ALTER TABLE `rubric`
  ADD PRIMARY KEY (`rubricID`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`scoreID`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`settingsID`);

--
-- Indexes for table `studentbadges`
--
ALTER TABLE `studentbadges`
  ADD PRIMARY KEY (`studentBadgeID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `badgeID` (`badgeID`),
  ADD KEY `courseID` (`courseID`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submissionID`);

--
-- Indexes for table `testquestionchoices`
--
ALTER TABLE `testquestionchoices`
  ADD PRIMARY KEY (`choiceID`);

--
-- Indexes for table `testquestions`
--
ALTER TABLE `testquestions`
  ADD PRIMARY KEY (`testQuestionID`);

--
-- Indexes for table `testresponses`
--
ALTER TABLE `testresponses`
  ADD PRIMARY KEY (`testResponseID`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`testID`);

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
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignmentID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `badgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `courseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courseschedule`
--
ALTER TABLE `courseschedule`
  MODIFY `courseScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `criterionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `fileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inbox`
--
ALTER TABLE `inbox`
  MODIFY `inboxID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `leaderboardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lessonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `levelID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
  MODIFY `pointsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `programID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `rubric`
--
ALTER TABLE `rubric`
  MODIFY `rubricID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `scoreID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `settingsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `studentbadges`
--
ALTER TABLE `studentbadges`
  MODIFY `studentBadgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submissionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testquestionchoices`
--
ALTER TABLE `testquestionchoices`
  MODIFY `choiceID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `testquestions`
--
ALTER TABLE `testquestions`
  MODIFY `testQuestionID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `testresponses`
--
ALTER TABLE `testresponses`
  MODIFY `testResponseID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `testID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `userinfo`
--
ALTER TABLE `userinfo`
  MODIFY `userInfoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `studentbadges`
--
ALTER TABLE `studentbadges`
  ADD CONSTRAINT `studentbadges_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `studentbadges_ibfk_2` FOREIGN KEY (`badgeID`) REFERENCES `badges` (`badgeID`),
  ADD CONSTRAINT `studentbadges_ibfk_3` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
