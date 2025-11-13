-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 04:20 PM
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
(4, 1, 2, '2025-11-06 12:27:25');

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
(4, 2, 'Activity #1', 'Task', '2025-09-11', 0, '2025-09-04 22:00:15');

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
(1, 1, 'Attached is a Google Doc that you can edit.\n\nIn Figma, design a “404 Not Found” page.\n\nCreate two versions, one for the mobile and one for the desktop. Turn in when done.\n\nTurn in when done.\n\n', 100, 1),
(2, 4, 'Attached is a Google Doc that you can edit.\r\n\r\nIn Figma, design a “404 Not Found” page.\r\n\r\nCreate two versions, one for the mobile and one for the desktop. Turn in when done.\r\n\r\nTurn in when done.\r\n\r\n', 100, 1),
(3, 2, 'Attached is a Google Doc that you can edit.\r\n\r\nIn Figma, design a “404 Not Found” page.\r\n\r\nCreate two versions, one for the mobile and one for the desktop. Turn in when done.\r\n\r\nTurn in when done.\r\n\r\n', 100, NULL);

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
-- Table structure for table `colortheme`
--

CREATE TABLE `colortheme` (
  `colorThemeID` int(11) NOT NULL,
  `themeName` varchar(100) NOT NULL,
  `hexCode` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colortheme`
--

INSERT INTO `colortheme` (`colorThemeID`, `themeName`, `hexCode`, `description`, `price`) VALUES
(1, 'Sunny Glow', '#FDDC94', 'Warm primary accent.', 100),
(2, 'Peachy Cream', '#FFE1D6', 'Soft and gentle peach.', 150),
(3, 'Rosy Blush', '#FFC4C4', 'Soft pink, friendly tone.', 150),
(4, 'Lavender Mist', '#FFD1FF', 'Soft purple elegance.', 150),
(5, 'Mint Breeze', '#D1FFD6', 'Fresh subtle green.', 100),
(6, 'Sky Whisper', '#C6E2FF', 'Calm airy blue.', 100),
(7, 'Apricot Cream', '#FFE5B4', 'Warm muted orange.', 100),
(8, 'Soft Sage', '#B8CBB8', 'Gentle muted green.', 50),
(9, 'Muted Teal', '#8FBAC8', 'Soft muted teal.', 50),
(10, 'Dusty Lavender', '#C9B8D6', 'Muted purple tone.', 50),
(11, 'Warm Taupe', '#D6C1B3', 'Soft neutral brownish tone.', 50),
(12, 'Pale Coral', '#FFCCC4', 'Soft coral accent.', 50),
(13, 'Creamy Almond', '#FFE6CC', 'Soft creamy beige.', 50),
(14, 'Aqua Mist', '#B3FFEC', 'Relaxing soft cyan.', 100),
(15, 'Lilac Whisper', '#E5D4FF', 'Soft delicate lilac.', 150),
(16, 'Buttercream', '#FFF3CC', 'Gentle yellowish cream.', 150),
(17, 'Powder Blue', '#DCEEFF', 'Soft blue for calm vibes.', 100),
(18, 'Peach Fizz', '#FFD8B8', 'Light and cheerful peach.', 100),
(19, 'Seafoam', '#C4FFE1', 'Soft refreshing greenish-blue.', 100),
(20, 'Blush Pink', '#FFD9E1', 'Tender pink for subtle warmth.', 150),
(21, 'Soft Lemon', '#FFF9CC', 'Delicate pastel yellow.', 150),
(22, 'Lilac Glow', '#EED6FF', 'Mild lilac accent.', 100),
(23, 'Powder Peach', '#FFE6D9', 'Soft, neutral peach.', 100),
(24, 'Misty Aqua', '#CCF7FF', 'Gentle airy blue-green.', 100);

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
-- Table structure for table `coverimage`
--

CREATE TABLE `coverimage` (
  `coverImageID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `imagePath` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coverimage`
--

INSERT INTO `coverimage` (`coverImageID`, `title`, `imagePath`, `description`, `price`) VALUES
(1, 'Soft Yellow Plain', '1.png', 'A gentle yellow background that brings warmth and positivity — simple, bright, and easy on the eyes.', 0),
(2, 'Light Green Stripes', '2.png', 'Fresh green stripes that make everything feel calm and natural, like a spring morning.', 50),
(3, 'Yellow Grid', '3.png', 'Clean, geometric lines for a tidy and upbeat vibe.', 50),
(4, 'Green Checkered Pattern', '4.png', 'A cozy green checkerboard that feels like a picnic under the trees.', 50),
(5, 'Blue Lined Pattern', '5.png', 'Soft blue lines for a relaxed, classic look that keeps things organized.', 50),
(6, 'Red Diagonal Stripes', '6.png', 'Bold red stripes that bring energy and movement to your layout.', 50),
(7, 'Blue Pinstripe Pattern', '7.png', 'Sleek, professional, and timeless — perfect for anyone who likes order with style.', 50),
(8, 'Red Checkered Pattern', '8.png', 'Playful and lively, like an afternoon spent with friends and laughter.', 50),
(9, 'Light Purple Texture', '9.png', 'Gentle lavender tones that give your space a dreamy, creative feel.', 50),
(10, 'Blue Geometric Lines', '10.png', 'Structured and modern — for planners who like clean, minimal aesthetics.', 50),
(11, 'Yellow Gingham Pattern', '11.png', 'Sunny and cheerful, this pattern adds instant brightness to any page.', 50),
(12, 'Pink Plaid Pattern', '12.png', 'Soft, friendly, and charming — a gentle pink that feels welcoming and fun.', 50),
(13, 'Green and White Stripes', '13.png', 'Balanced and refreshing — a simple design that keeps things light.', 50),
(14, 'Red Picnic Pattern', '14.png', 'Classic red checks that remind you of summer afternoons and happy breaks.', 50),
(15, 'Brown Checkered Pattern', '15.png', 'Warm and earthy — perfect for those who prefer grounded tones.', 50),
(16, 'Light Blue Check Pattern', '16.png', 'Cool and breezy, like clear skies on a quiet day.', 50),
(17, 'Blue Duck Pattern', '17.png', 'Tiny ducks swimming across a bright blue background — cheerful, quirky, and full of charm.', 100),
(18, 'Dark Teal Spaceships', '18.png', 'Playful little UFOs drifting in deep space — for the dreamers and explorers.', 100),
(19, 'Green Mushroom Pattern', '19.png', 'Cute mushrooms sprouting across a green field — a cozy, game-like atmosphere.', 100),
(20, 'Gray Scratch Texture', '20.png', 'A rough silver texture that gives off an edgy, modern vibe.', 150),
(21, 'Blue City Silhouette', '21.png', 'A skyline at sunrise — simple, clean, and full of quiet ambition.', 200),
(22, 'Purple Fantasy Skyline', '22.png', 'A whimsical cityscape that feels straight out of a storybook.', 200),
(23, 'Orange Desert Scene', '23.png', 'Warm canyon tones that radiate adventure and wanderlust.', 200),
(24, 'Purple City at Dusk', '24.png', 'Cool purple skies over a glowing city — mysterious and modern.', 200),
(25, 'Colorful Confetti Pattern', '25.png', 'Bright and fun — perfect for celebrating progress and small wins.', 150),
(26, 'Green Star Pattern', '26.png', 'Neon stars twinkling on a dark background — energetic and playful.', 250),
(27, 'White Star Pattern', '27.png', 'A soft night sky scattered with gentle stars — peaceful and subtle.', 250),
(28, 'Comic Cat Scene', '28.png', 'A hand-drawn lineup of sleepy cats doing cute things — simple joy in every glance.', 300),
(29, 'Golden Starburst', '29.png', 'A glowing gold design that shines like confidence itself.', 400),
(30, 'Green Laser Grid', '30.png', 'Futuristic green beams crisscrossing — sharp, fast, and full of energy.', 400),
(31, 'KOLORCOASTER', '31.png', 'A vivid wave of color and motion, inspired by Maki’s KOLORCOASTER— a rush of rhythm, light, and feeling that never stops moving.', 450),
(32, 'Purple Energy Core', '32.png', 'Radiant light bursting from the center — calm strength meets inner focus.', 400),
(33, 'Pink Heart Glow', '33.png', 'Soft pink energy swirling with warmth — for expressive, kind-hearted players.', 400),
(34, 'Green Energy Stream', '34.png', 'Waves of vibrant light in motion — smooth, lively, and futuristic.', 400),
(35, 'Blue Triangle Tunnel', '35.png', 'A sharp geometric tunnel leading into the future — sleek and mesmerizing.', 400),
(36, 'Blue Light Tunnel', '36.png', 'Deep blues and glowing lines that pull you into a world of motion.', 400),
(37, 'Purple Energy Burst', '37.png', 'Powerful violet streaks exploding with energy — bold, fast, and dramatic.', 400),
(38, 'Purple Nebula', '38.png', 'A calm cosmic swirl — mysterious, vast, and quietly beautiful.', 400);

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

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`criterionID`, `rubricID`, `criteriaTitle`, `criteriaDescription`) VALUES
(1, 1, 'Voice, Style, and Imagery', 'Assesses the development of a distinct and compelling writer\'s voice, the effectiveness of word choice (diction), and the use of sensory details and figurative language (imagery) to create a vivid experience for the reader.'),
(2, 1, 'Narrative Structure and Pacing', 'Assesses the effective management of the plot (beginning, rising action, climax, resolution), character development (if applicable), and the manipulation of time and speed (pacing) to build tension and interest.'),
(3, 2, 'Participation and Initiative', 'Assesses the student\'s level of engagement, attendance at meetings, willingness to take on tasks, and proactive approach to contributing ideas and effort to the team\'s goals.');

-- --------------------------------------------------------

--
-- Table structure for table `emblem`
--

CREATE TABLE `emblem` (
  `emblemID` int(11) NOT NULL,
  `emblemName` varchar(100) NOT NULL,
  `emblemPath` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emblem`
--

INSERT INTO `emblem` (`emblemID`, `emblemName`, `emblemPath`, `description`, `price`) VALUES
(1, 'Webstar', 'default.png', 'Show your pride in Webstar University.', 0),
(2, 'Webstar University', 'webstar_u.png', 'Show your pride in Webstar University.', 50),
(3, 'Crammers Club', 'crammers_club.png', 'For those who hustle and cram like pros.', 450),
(4, 'Mula S\'ayo, Para sa Bayan', 'mula_sayo.png', 'Gagamitin ang karunungan mula sa\'yo, para sa bayan', 300),
(5, 'Computer Society', 'comsoc.png', 'Sumusulong, Lumalaban!', 250),
(6, 'BSIT-Log', 'bsitlog.png', 'BSITlogs, mag-ingay!', 250),
(7, 'Y2K BSIT', 'bsit.png', 'Y2K emblem for BSIT members!', 250),
(8, 'Iskolar ng Bayan', 'iskolar_ng_bayan.png', 'Ngayon ay lumalaban!', 400);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollmentID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `courseID` int(5) NOT NULL,
  `yearSection` int(11) NOT NULL,
  `xpPoints` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollmentID`, `userID`, `courseID`, `yearSection`, `xpPoints`) VALUES
(1, 2, 1, 2023, 0),
(2, 2, 2, 2023, 0),
(3, 3, 1, 2023, 0),
(4, 4, 1, 2023, 0),
(5, 5, 1, 2023, 0),
(6, 6, 1, 2023, 0),
(7, 7, 1, 2023, 0),
(8, 8, 1, 2023, 0),
(9, 9, 1, 2023, 0),
(10, 10, 1, 2023, 0),
(11, 11, 1, 2023, 0),
(12, 12, 1, 2023, 0),
(13, 2, 3, 1, 0);

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
  `submissionID` int(11) DEFAULT NULL,
  `fileAttachment` varchar(255) NOT NULL,
  `fileTitle` varchar(50) NOT NULL,
  `fileLink` varchar(100) NOT NULL,
  `uploadedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileID`, `courseID`, `userID`, `announcementID`, `lessonID`, `assignmentID`, `submissionID`, `fileAttachment`, `fileTitle`, `fileLink`, `uploadedAt`) VALUES
(1, 1, 1, 1, 1, NULL, NULL, 'Web Development Course Material', 'Web Development', 'https://gmail.com/lesson1,https://gmail.com/lesson1.1', '2025-08-30 10:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `inboxID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `messageText` text NOT NULL,
  `notifType` int(11) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `isRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inbox`
--

INSERT INTO `inbox` (`inboxID`, `enrollmentID`, `messageText`, `notifType`, `createdAt`, `isRead`) VALUES
(1, 1, 'Prof. Christian James has posted a new assignment.', 0, '2024-01-31 08:04:00', 1),
(2, 2, 'Prof. Christian James has posted a new assignment.', 0, '2024-01-31 08:04:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `leaderboardID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `timeRange` varchar(10) NOT NULL,
  `periodStart` date NOT NULL,
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`leaderboardID`, `enrollmentID`, `timeRange`, `periodStart`, `updatedAt`) VALUES
(1, 1, 'Weekly', '2025-08-25', '2025-08-30 12:00:00'),
(2, 2, 'Weekly', '2025-08-25', '2025-08-30 12:00:00'),
(3, 1, 'Weekly', '2025-09-28', '2025-09-28 16:50:22'),
(4, 3, 'Weekly', '2025-09-28', '2025-09-28 16:58:09'),
(5, 4, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(6, 5, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(7, 6, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(8, 7, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(9, 8, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(10, 9, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(11, 10, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(12, 11, 'Weekly', '2025-09-01', '2025-09-28 21:03:44'),
(13, 12, 'Monthly', '2025-09-01', '2025-09-28 21:34:51');

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

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`levelID`, `criterionID`, `levelTitle`, `levelDescription`, `points`) VALUES
(1, 1, 'Exemplary (A)', 'The writing demonstrates a unique, sophisticated, and memorable voice. Imagery is fresh, powerful, and deeply evocative, seamlessly integrating figurative language (metaphor, simile) that enriches the meaning. Diction is precise and masterful.', 30.00),
(2, 2, 'Exemplary (A)', 'The structure is innovative and perfectly paced, maintaining tension and emotional resonance throughout. Characters (if present) are complex and fully realized, undergoing believable development. The ending is highly satisfying and meaningful.', 50.00),
(3, 3, 'Exemplary (A)', 'Is a driving force in the group; consistently attends and is highly prepared for all meetings. Proactively seeks out complex tasks and offers creative solutions, motivating others to contribute effectively.', 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `myitems`
--

CREATE TABLE `myitems` (
  `myItemID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `emblemID` int(11) DEFAULT NULL,
  `coverImageID` int(11) DEFAULT NULL,
  `colorThemeID` int(11) DEFAULT NULL,
  `dateAcquired` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `myitems`
--

INSERT INTO `myitems` (`myItemID`, `userID`, `emblemID`, `coverImageID`, `colorThemeID`, `dateAcquired`) VALUES
(1, 2, NULL, 1, NULL, '2025-11-06 15:53:54'),
(131, 2, 1, NULL, NULL, '2025-11-06 15:53:54'),
(132, 2, NULL, NULL, 1, '2025-11-06 15:53:54'),
(152, 4, 1, NULL, NULL, '2025-11-06 17:43:05'),
(153, 4, NULL, NULL, 13, '2025-11-06 17:43:17'),
(154, 4, NULL, NULL, 2, '2025-11-06 17:43:26'),
(155, 4, NULL, 14, NULL, '2025-11-06 17:43:34'),
(156, 4, 2, NULL, NULL, '2025-11-06 17:43:39'),
(157, 2, NULL, NULL, 2, '2025-11-06 17:46:35'),
(158, 2, NULL, NULL, 3, '2025-11-06 17:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `profileID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `bio` varchar(255) NOT NULL DEFAULT 'Welcome to my Webstar profile!',
  `webstars` int(11) DEFAULT 0,
  `emblemID` int(11) NOT NULL DEFAULT 1,
  `coverImageID` int(11) NOT NULL DEFAULT 1,
  `colorThemeID` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profileID`, `userID`, `bio`, `webstars`, `emblemID`, `coverImageID`, `colorThemeID`) VALUES
(1, 2, 'Welcome to my Webstar profile!', 5599, 1, 1, 1),
(2, 4, 'Welcome to my Webstar profile!', 700, 1, 1, 1);

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
(1, 1, 550, 1, '2025-11-07 08:30:53'),
(2, 3, 400, 2, '2025-11-07 08:30:53'),
(3, 8, 221, 3, '2025-11-07 08:30:53'),
(4, 6, 215, 4, '2025-11-07 08:30:53'),
(5, 11, 214, 5, '2025-11-07 08:30:54'),
(6, 10, 195, 6, '2025-11-07 08:30:54'),
(7, 5, 189, 7, '2025-11-07 08:30:54'),
(8, 7, 176, 8, '2025-11-07 08:30:54'),
(9, 9, 167, 9, '2025-11-07 08:30:54'),
(10, 4, 152, 10, '2025-11-07 08:30:54'),
(11, 12, 95, 11, '2025-11-07 08:30:54'),
(12, 2, 450, 1, '2025-11-06 15:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `rubric`
--

CREATE TABLE `rubric` (
  `rubricID` int(11) NOT NULL,
  `rubricTitle` varchar(100) NOT NULL,
  `rubricType` varchar(20) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `totalPoints` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rubric`
--

INSERT INTO `rubric` (`rubricID`, `rubricTitle`, `rubricType`, `userID`, `totalPoints`) VALUES
(1, 'Creative Writing Assessment', 'Created', 1, 80),
(2, 'Group Collaboration and Contribution', 'Preset', 1, 50);

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `scoreID` int(5) NOT NULL,
  `userID` int(5) NOT NULL,
  `submissionID` int(5) DEFAULT NULL,
  `testID` int(5) DEFAULT NULL,
  `score` int(5) NOT NULL,
  `feedback` varchar(255) DEFAULT NULL,
  `gradedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `selectedlevels`
--

CREATE TABLE `selectedlevels` (
  `selectedLevelID` int(11) NOT NULL,
  `submissionID` int(11) NOT NULL,
  `levelID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `assignmentID` int(11) NOT NULL,
  `earnedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentbadges`
--

INSERT INTO `studentbadges` (`studentBadgeID`, `userID`, `badgeID`, `courseID`, `assignmentID`, `earnedAt`) VALUES
(1, 2, 1, 1, 0, '2025-10-28 20:16:06'),
(2, 2, 2, 1, 0, '2025-10-28 20:16:06'),
(3, 2, 2, 1, 0, '2025-10-28 20:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submissionID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `assessmentID` int(11) NOT NULL,
  `scoreID` int(11) DEFAULT NULL,
  `submittedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `isSubmitted` tinyint(4) NOT NULL,
  `modalShown` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

CREATE TABLE `supports` (
  `supportID` int(5) NOT NULL,
  `supportRole` enum('Student','Professor') NOT NULL,
  `supportQuestion` varchar(50) NOT NULL,
  `supportAnswer` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supports`
--

INSERT INTO `supports` (`supportID`, `supportRole`, `supportQuestion`, `supportAnswer`) VALUES
(1, 'Student', 'What is Webstar', 'Webstar is an interactive online learning platform designed to help users explore courses, track their progress, and earn points through engaging activities. It focuses on making learning fun, structured, and community driven.'),
(2, 'Student', 'What is required on the Login Setup Page?', 'If you are a new user, you will be directed to the Account Setup Page where you are required to enter a valid email address. A verification code will be sent to your email, and you must input that code to confirm your ownership of the account. After the verification, you will be directed to create a new password and confirm it. Once you complete these steps, you can proceed to the user dashboard.'),
(3, 'Student', 'How do I enroll in my first course?', 'You enroll by entering the access code given by your professor. Your new course will appear in the \"Your Courses\" list immediately. The rest of your dashboard will show an empty state until your course is active with announcements and tasks.'),
(4, 'Student', 'What is the Home Page and what does it show?', 'The Webstar Home page is your central, personalized dashboard providing a quick overview of your academic life, featuring your enrolled courses, recent announcements, a timeline of upcoming tasks, and your leaderboard rank.'),
(5, 'Student', 'What information is available on the Courses page?', 'The Courses page serves as your complete directory, listing every course you have enrolled in, with each card displaying key details like the Course Code and Name, the Professor Name, and the class Schedule.'),
(6, 'Student', 'How can I manage or find my courses?', 'The Courses page offers several organizational tools, including a Search Bar to find courses by code or name, and a Status Filter that allows you to sort the list to view either \"Active\" or \"Archived\" classes.'),
(7, 'Student', 'How do I add a new course?', 'To add an additional course, click the \"+ Join Course\" button in the top right corner, then enter the access code provided by your professor in the access form and click \"Enroll\" to add the class to your list.'),
(8, 'Student', 'What does the Course Info page contain?', 'The Course Info page aggregates all resources and activities for a single class, featuring top navigation tabs for Announcements, Lessons, To-do, Files, Leaderboard, Report, and Students, alongside a sidebar card that tracks your overall Class Standing, level, and current Quests.'),
(9, 'Student', 'How do I enroll or unenroll from a course?', 'You can choose to unenroll from a course by clicking the three-dot menu at the top of the course card on the left side of the Course Info page.'),
(10, 'Student', 'How do I view professor announcements and access a', 'The Announcements tab contains messages and updates posted by your professor, which you can filter to view the newest or oldest messages. If an announcement includes extra resources, you can click the designated button to view any attached files.'),
(11, 'Student', 'Where can I find the learning materials and lectur', 'The Lessons tab contains all the organized lectures and learning materials for the course. Clicking on any lesson card will redirect you to the Lesson Info, where you can access and review the specific content provided by your professor.'),
(12, 'Student', 'How do I view and manage my activities for the cou', 'The To-do tab lists all required course activities, which you can manage by sorting them or filtering by Status (Pending, Missing, Done). Clicking an activity takes you to the respective page where you can submit your work or take your exams.'),
(13, 'Student', 'How do I submit my work for an assignment or activ', 'In the submission area, you can attach your completed work either by uploading files from your device or by providing a link to an external source (like Google Drive or Figma), and then click \"Turn In\" to submit.'),
(14, 'Student', 'What rewards do I receive after submitting an assi', 'After successfully submitting a task or taking an exam, you will be shown a reward screen indicating the XPs (Experience Points) and Webstars you have earned.'),
(15, 'Student', 'What is the purpose of the XP Multiplier and how d', 'The XP Multiplier is used to instantly increase the Experience Points (XPs) you earned from an exam, helping you level up faster and improve your standing.'),
(16, 'Student', 'Where can I view the results and answers for my co', 'You can view the full results, including your score and the correct/incorrect answers, by returning to the Exam Info. The results become available only after your instructor has released them; at that point, you will see a \"View Results\" button on the page.'),
(17, 'Student', 'Where can I find and download course materials sha', 'The Files tab serves as a repository for all documents and links shared by your professor. If the item is a document, clicking it allows you to view the file and offers a download button. If it is a link, clicking it will open the external resource.'),
(18, 'Student', 'What information is in the Leaderboard?', 'The Leaderboard tab shows a ranked list of students based on their performance, highlighting the top 3 in the class. You can also use the search function to quickly find any of your classmates within the list.'),
(19, 'Student', 'How can I view a detailed performance report for a', 'To view a detailed performance report for a classmate, you must go to the Leaderboard, click on their name, and you will be redirected to their Report tab, which shows their on-time submissions, overall performance, records, charts, and badges.'),
(20, 'Student', 'How can I view a list of all students enrolled in ', 'To see everyone enrolled, navigate to the student’s tab within the Course Info. You can view the full class roster, use the search bar to quickly find a specific student, and apply filters to sort the list by newest or oldest student.'),
(21, 'Student', 'What notifications will I find in my Inbox?', 'Inbox serves as your notification center, containing updates on every relevant course activity, including notifications for submitted tasks, lessons, exams, announcements, submissions, and feedback. You can sort your notifications and filter them by course or type. If the notification is about a new badge, you can click it to view the new badge, XPs, and Webstars you earned.'),
(22, 'Student', 'What will I see in the My To-do section, and how c', 'My To-do displays all your activities, exams, and tasks from every course you are enrolled in. You can organize this list by using filters to view activities by specific course or by Status (Pending, Missing, Done), and you can sort the items from newest to oldest. Clicking on any activity card will direct you to its respective page.'),
(23, 'Student', 'What is the purpose of the Explore page?', 'The Explore page serves as the main search directory for the platform, allowing you to easily search for and find information about students and professors enrolled or working within the Webstar system.'),
(24, 'Student', 'What kinds of items can I purchase in the Shop?', 'The Shop allows you to purchase various cosmetic items to personalize your profile, including emblems, cover photos, moving profile photos, and profile colors. You can view all the items you currently own in the \"My Items\" section.'),
(25, 'Student', 'What will I use to buy the items in the Shop?', 'You can use your earned Webstars to purchase items in the Shop. Each item has a different price indicated in Webstars.'),
(26, 'Student', 'What happens after I purchase an item from the Sho', 'Once you successfully purchase an item, it is immediately applied to your profile to customize your look and display your achievement.'),
(27, 'Student', 'How can I edit my profile?', 'You can edit your profile by clicking \"Edit Profile\" in the Settings sidebar. Here, you can update your personal information such as your name, email, and profile picture. Once you save the changes, your updated information will be reflected across the system.'),
(28, 'Student', 'How can I share my Star Card with others?', 'You can share your Star Card by going to your Settings sidebar and clicking \"Edit Profile\". On the profile editing page, locate the specific section or card for the Star Card, where you will find and click the Share button and you can download the Star Card in My Star Card tab.'),
(29, 'Student', 'How can I reset my password?', 'You can reset your password under the Edit Profile tab in the Settings sidebar. You are required to enter a valid email address, and a verification code will be sent to your email. You must input the code to confirm ownership of your account. After the verification, you will be directed to create a new password and confirm it. This ensures that your account remains secure.'),
(30, 'Student', 'How can I customize my profile?', 'You can customize your settings by clicking Settings Customization tab. Here, you can also design borders for your cover photo and profile photo to personalize your profile further.'),
(31, 'Student', 'How can I access support and send feedback?', 'You can access support by clicking Support tab. This will provide you with resources, guides, and options to contact the support team if you need help with the system. Under Support, there is an Email option where you can compose and send messages directly to the system’s support email. This allows you to report issues, ask questions, or request assistance from the support team.'),
(32, 'Student', 'How can I provide feedback?', 'You can provide feedback by clicking Feedback tab. This allows you to share suggestions, report issues, or give comments directly to the development or support team to help improve the system.'),
(33, 'Student', 'How can I manage and control my email notification', 'You can manage your email notifications by navigating to the Preferences tab within your settings. This page provides toggle buttons that allow you to select whether you wish to receive email updates regarding your course activities, quests, deadlines, and announcements.'),
(34, 'Professor', 'What is Webstar', 'Webstar is an interactive online learning platform designed to help users explore courses, track their progress, and earn points through engaging activities. It focuses on making learning fun, structured, and community driven.'),
(35, 'Professor', 'What is required on the Login Setup Page?', 'If you are a new user, you will be directed to the Account Setup Page where you are required to enter a valid email address. A verification code will be sent to your email, and you must input that code to confirm your ownership of the account. After the verification, you will be directed to create a new password and confirm it. Once you complete these steps, you can proceed to the professor dashboard.'),
(36, 'Professor', 'How can I create a course?', 'You can create a course by clicking the <strong>Create</strong> button on the dashboard and selecting <strong>Create Course</strong> or go to courses tab and you will see the <strong>create course</strong> button there. You will be directed to create-course page where you enter the course details, such as the course title, description, and other relevant information. Once saved, the course will appear on the student’s dashboard and be accessible to them.'),
(37, 'Professor', 'How can I post announcements?', 'You can post announcements by clicking the <strong>Create</strong> button and selecting <strong>Post Announcement</strong>. You will be directed to post-announcement page where you can write the announcement, attach relevant files, links, and choose which course/s should receive it. Once posted, the announcement will be visible to the student’s dashboard and course-info section.'),
(38, 'Professor', 'How can I add lessons?', 'You can add lessons by clicking the <strong>Create</strong> button and selecting <strong>Add Lesson</strong>. You will be directed to add-lesson page where you can provide the lesson title, description, and attach links or files. Once added, students can access the lessons in the course-info section.'),
(39, 'Professor', 'How can I create tests?', 'You can create tests by clicking the <strong>Create</strong> button and selecting <strong>Create Exam</strong>. You will be directed to the <strong>Create Exam</strong> page, where you can set the exam title, time limits, choose the type of test (multiple choice or identification questions), and select which course(s) should receive it. Once published, students can take the exam according to the deadline you set.'),
(40, 'Professor', 'How can I assign tasks?', 'You can assign tasks by clicking the <strong>Create</strong> button and selecting <strong>Assign Task</strong>. You will be directed to assign-task page where you can provide task instructions, set deadlines, attach necessary files, links, rubrics and select which course(s) should receive it. Once assigned, students will receive notifications and can submit their work through the dashboard.'),
(41, 'Professor', 'How can I view the courses I have created?', 'You can see all the courses you have created in the <strong>Courses</strong> sidebar on the dashboard. Clicking on a course will take you to the <strong>Course Info</strong> page, where you can access all the course contents.'),
(42, 'Professor', 'How can I view or manage announcements for a cours', 'On the <strong>Course Info</strong> page, you can access the <strong>Announcements</strong> tab section. You can view existing announcements or manage them by clicking the <strong>three dots (⋮)</strong> to <strong>edit or delete</strong> any announcement.'),
(43, 'Professor', 'How can I view or manage lessons in a course?', 'On the <strong>Course Info</strong> page, you can go to the <strong>Lessons</strong> tab section. Clicking on a lesson will take you to <strong>Lesson Info</strong>, where you can view its content (title, objectives, attachments). You can also <strong>edit or delete</strong> any lesson by clicking the <strong>three dots (⋮)</strong> next to it.'),
(44, 'Professor', 'How can I manage tests and assignments in a course', 'On the <strong>Course Info</strong> page, the <strong>To-Do</strong> tab section displays cards for each <strong>Test</strong> and <strong>Task</strong>. Each card has a <strong>three dots (⋮)</strong> menu. Clicking it allows you to <strong>edit or delete</strong> the item. Clicking the card itself will direct you to the <strong>Assess</strong> page, where you can view test details, student submissions, and analytics. Clicking <strong>Preview</strong> on the card will take you to the <strong>Te'),
(45, 'Professor', 'How can I view course records, links, files and le', 'You can access all assessment records in the <strong>Records</strong> section of the <strong>Course Info</strong> page. Here, you can view and track student’s grades. In the <strong>Files and Link</strong> tab section of the <strong>Course Info</strong> page, you can see all the files and links you have uploaded for the course. You can view student rankings in the <strong>Leaderboard</strong> tab section of the <strong>Course Info</strong> page. This shows the performance of students based on sc'),
(46, 'Professor', 'How can I view students enrolled in a course?', 'On the <strong>Students</strong> tab of the <strong>Course Info</strong> page, you can see all students enrolled in the course. You can manage students by searching for a specific student using the <strong>search bar</strong>. Each student also has a <strong>three dots (⋮)</strong> menu, where you can view their report page, access their profile page, or remove them from the course if necessary. This allows you to efficiently manage students and monitor their progress.'),
(47, 'Professor', 'What is the Inbox tab used for?', 'The <strong>Inbox</strong> tab serves as a reminder system for professors. You can view notifications about completed assessment deadlines and see if all students have submitted their work. This helps you keep track of important course activities without missing any updates.'),
(48, 'Professor', 'How can I find a student’s name?', 'You can use the <strong>search bar</strong> on the dashboard or within the <strong>student’s</strong> tab to quickly find a specific student. Simply type the student’s name, and the system will display matching results. You can then click on a student to visit their <strong>profile</strong> and access related course information or records.'),
(49, 'Professor', 'How can I view and manage the assessments I have c', 'In the <strong>Assess</strong> tab, you can see all the assessments you have created. Each assessment has buttons for <strong>Task Details, Grading Sheet, and Return All</strong>. If the assessment type is a task, clicking <strong>Task Details</strong> will redirect you to the <strong>Task Details</strong> page, where you can view the task information and student submissions. The <strong>Grading Sheet</strong> allows you to review and grade student work, while <strong>Return All</strong> lets yo'),
(50, 'Professor', 'How can I see the status of student submissions an', 'In the <strong>Submission</strong> section of the <strong>Assess</strong> tab, you can view the status of each student’s work to see who has submitted and who hasn’t. After the assessment deadline, once all submissions have been graded, you can click <strong>Return All</strong> to return the graded work to all students at once. This allows you to efficiently track student progress and complete the grading process.'),
(51, 'Professor', 'How can I grade student submissions for a task?', 'If the assessment type is a task, you can click <strong>Grading Sheet</strong> on the assessment card, which will direct you to the <strong>Grading Sheet</strong> page. Here, you can award badges, provide rubrics (if available), and give feedback to each student. This allows you to grade submissions thoroughly and provide meaningful evaluations for your students.'),
(52, 'Professor', 'How can I view student performance on a test?', 'On the <strong>Assess Exam Analytics</strong> page, you can view detailed student performance, including question-level performance, class averages, overall performance, and the average time students took to answer each question. You can also see which students passed, the average number of correct answers per question, and the names of students who answered each question correctly or incorrectly.'),
(53, 'Professor', 'How can I reuse an existing item in my courses?', 'You can reuse an existing item by clicking the <strong>Reuse</strong> option on its card. This works for announcements, lessons, exams, and tasks. Clicking Reuse will open a <strong>modal</strong> where you can select the course(s) where you want to post or use the item again.'),
(54, 'Professor', 'How can I edit my profile?', 'You can edit your profile by clicking <strong>Edit Profile</strong> in the Settings sidebar. Here, you can update your personal information such as your name, email, and profile picture. Once you save the changes, your updated information will be reflected across the system.'),
(55, 'Professor', 'How can I reset my password?', 'You can reset your password under the <strong>Edit Profile</strong> tab in the Settings sidebar. You are required to enter a valid email address, and a verification code will be sent to your email. You must input the code to confirm ownership of your account. After the verification, you will be directed to create a new password and confirm it. This ensures that your account remains secure.'),
(56, 'Professor', 'How can I customize my profile?', 'You can customize your settings by clicking <strong>Settings Customization</strong> tab. Here, you can also design borders for your <strong>cover photo and profile photo</strong> to personalize your profile further.'),
(57, 'Professor', 'How can I access support and send feedback?', 'You can access support by clicking <strong>Support tab</strong>. This will provide you with resources, guides, and options to contact the support team if you need help with the system. Under Support, there is an <strong>Email</strong> option where you can compose and send messages directly to the system’s support email. This allows you to report issues, ask questions, or request assistance from the support team.'),
(58, 'Professor', 'How can I provide feedback?', 'You can provide feedback by clicking <strong>Feedback tab</strong>. This allows you to share suggestions, report issues, or give comments directly to the development or support team to help improve the system.');

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
(1, 2, 'Read each question carefully and choose the best answer from the given options. Only one option is correct for each question. Once you move to the next question, you will not be able to return to the previous one, so review your answer before proceeding. The exam will automatically submit when the timer ends. Avoid refreshing or closing the browser during the exam to prevent submission issues.', 1200),
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
  `isRead` tinyint(1) DEFAULT 0,
  `timeSpent` int(11) DEFAULT NULL,
  `timeStart` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todo`
--

INSERT INTO `todo` (`todoID`, `userID`, `assessmentID`, `status`, `updatedAt`, `isRead`, `timeSpent`, `timeStart`) VALUES
(1, 2, 1, 'Missing', '2025-11-11 02:36:50', 1, NULL, NULL),
(2, 2, 4, 'Submitted', '2025-11-13 19:59:36', 0, NULL, NULL),
(3, 2, 2, 'Pending', '2025-11-04 18:16:16', 1, 0, NULL);

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
(1, 2, 'prof.png', 'Jane', 'Mendoza', 'Smith', '202310001', '1', 'Female', '2', 1, 'jane.smith@university.edu', '', '', 'https://instagram.com/jane.smith', '2025-08-30 08:18:53', 0),
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

-- --------------------------------------------------------

--
-- Table structure for table `webstars`
--

CREATE TABLE `webstars` (
  `webstarsID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `assessmentID` int(11) DEFAULT NULL,
  `sourceType` varchar(50) NOT NULL,
  `pointsChanged` int(100) NOT NULL,
  `dateEarned` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `webstars`
--

INSERT INTO `webstars` (`webstarsID`, `userID`, `assessmentID`, `sourceType`, `pointsChanged`, `dateEarned`) VALUES
(22, 2, NULL, 'Shop Purchase', -150, '2025-11-06 17:56:42');

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
-- Indexes for table `colortheme`
--
ALTER TABLE `colortheme`
  ADD PRIMARY KEY (`colorThemeID`);

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
-- Indexes for table `coverimage`
--
ALTER TABLE `coverimage`
  ADD PRIMARY KEY (`coverImageID`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`criterionID`);

--
-- Indexes for table `emblem`
--
ALTER TABLE `emblem`
  ADD PRIMARY KEY (`emblemID`);

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
-- Indexes for table `myitems`
--
ALTER TABLE `myitems`
  ADD PRIMARY KEY (`myItemID`),
  ADD UNIQUE KEY `userID` (`userID`,`coverImageID`),
  ADD UNIQUE KEY `userID_2` (`userID`,`emblemID`),
  ADD UNIQUE KEY `userID_3` (`userID`,`colorThemeID`);

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
-- Indexes for table `selectedlevels`
--
ALTER TABLE `selectedlevels`
  ADD PRIMARY KEY (`selectedLevelID`);

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
-- Indexes for table `supports`
--
ALTER TABLE `supports`
  ADD PRIMARY KEY (`supportID`);

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
-- Indexes for table `webstars`
--
ALTER TABLE `webstars`
  ADD PRIMARY KEY (`webstarsID`);

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
  MODIFY `noteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignmentID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `badgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `colortheme`
--
ALTER TABLE `colortheme`
  MODIFY `colorThemeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

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
-- AUTO_INCREMENT for table `coverimage`
--
ALTER TABLE `coverimage`
  MODIFY `coverImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `criterionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `emblem`
--
ALTER TABLE `emblem`
  MODIFY `emblemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `leaderboardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lessonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `levelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `myitems`
--
ALTER TABLE `myitems`
  MODIFY `myItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `rubricID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `scoreID` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `selectedlevels`
--
ALTER TABLE `selectedlevels`
  MODIFY `selectedLevelID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `settingsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `studentbadges`
--
ALTER TABLE `studentbadges`
  MODIFY `studentBadgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submissionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supports`
--
ALTER TABLE `supports`
  MODIFY `supportID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

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
  MODIFY `testResponseID` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `testID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
-- AUTO_INCREMENT for table `webstars`
--
ALTER TABLE `webstars`
  MODIFY `webstarsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
