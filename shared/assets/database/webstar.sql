-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 07:50 PM
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
(1, 2, 'Christian James submitted “Activity #1” Quest.', '2025-11-16 10:37:06'),
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
(5, 4, 2, '2025-11-17 15:05:48'),
(6, 3, 2, '2025-11-17 15:05:49'),
(7, 2, 2, '2025-11-17 15:05:51');

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
(2, 34, 1, '', 'PAPA MO', '2025-11-17', '14:11:00', 0),
(3, 34, 1, '', 'Github', '2025-11-17', '14:17:42', 0),
(4, 34, 1, '', 'SHEETS', '2025-11-17', '14:21:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `assessmentID` int(11) NOT NULL,
  `courseID` int(11) NOT NULL,
  `assessmentTitle` varchar(100) NOT NULL,
  `type` enum('Task','Test') DEFAULT 'Task',
  `deadline` datetime NOT NULL,
  `deadlineEnabled` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime DEFAULT current_timestamp(),
  `isArchived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `courseImage` varchar(255) NOT NULL DEFAULT 'default.png',
  `isActive` int(3) NOT NULL DEFAULT 1,
  `code` varchar(20) NOT NULL,
  `section` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`courseID`, `userID`, `courseCode`, `courseTitle`, `courseImage`, `isActive`, `code`, `section`) VALUES
(1, 1, 'COMP-006', 'Web Development', 'default.png', 1, '123456', 'BSIT 4-1'),
(2, 1, 'GEED-007', 'Web Development 2', 'webdev.jpg', 1, '789ABC', 'BSIT 4-1'),
(3, 1, 'MM-102', 'Multimedia', 'AdobeStock_359419956.jpeg', 1, '1Z8AQ8', 'BSIT 4-1'),
(33, 1, 'GEED-039', 'Capstone 2', 'default.png', 1, 'EKACOG', 'BSIT 2-1'),
(34, 1, 'GEED-102', 'Principles of Accounting', 'AdobeStock_1428263864.jpeg', 1, '05TMQM', 'BSIT 4-1');

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
(2, 2, 'Monday', '18:07:00', '19:07:00', '2025-11-03 18:07:13'),
(7, 6, 'Monday', '12:12:00', '14:12:00', '2025-11-16 00:12:28'),
(57, 3, 'Monday', '18:08:00', '20:07:00', '2025-11-18 13:09:10'),
(59, 1, 'Monday', '01:18:00', '01:19:00', '2025-11-18 13:10:04'),
(60, 33, 'Monday', '21:47:00', '21:47:00', '2025-11-18 17:45:35'),
(61, 34, 'Tuesday', '16:00:00', '18:00:00', '2025-11-18 17:46:09');

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
(1, 1, 'Introduction & Background', 'Clarity of purpose, hypothesis, and scientific context'),
(2, 1, 'Methods & Procedures', 'Detail and clarity enabling replication'),
(3, 1, 'Data Presentation', 'Accuracy and organization of data'),
(4, 1, 'Analysis & Discussion', 'Quality of interpretation and reasoning'),
(5, 1, 'Structure & Mechanics', 'Organization, formatting, grammar'),
(6, 2, 'Depth of Reflection', 'Level of personal insight and critical thinking'),
(7, 2, 'Connection to Experience', 'Quality of linking experience to learning'),
(8, 2, 'Organization', 'Logical flow and structure'),
(9, 2, 'Writing Quality', 'Grammar, clarity, and coherence'),
(10, 3, 'Breadth of Sources', 'Quantity & relevance of scholarly sources'),
(11, 3, 'Synthesis of Information', 'Integration into themes'),
(12, 3, 'Critical Evaluation', 'Assessment of strengths & weaknesses'),
(13, 3, 'Organization', 'Structure and flow'),
(14, 3, 'Citation Accuracy', 'Correct use of citation style'),
(15, 4, 'Thesis Statement', 'Clarity and strength of main argument'),
(16, 4, 'Argument Development', 'Evidence & reasoning'),
(17, 4, 'Organization', 'Structural flow'),
(18, 4, 'Grammar & Mechanics', 'Writing correctness'),
(19, 5, 'Problem Identification', 'Recognition of key issues'),
(20, 5, 'Analysis', 'Use of concepts and reasoning'),
(21, 5, 'Solutions/Recommendations', 'Quality & feasibility'),
(22, 5, 'Evidence Use', 'Support from case data'),
(23, 6, 'Summary Accuracy', 'Correctness of key points'),
(24, 6, 'Critical Analysis', 'Depth of evaluation'),
(25, 6, 'Evidence Integration', 'Use of examples from article'),
(26, 6, 'Writing Quality', 'Clarity and structure'),
(27, 7, 'Originality', 'Creativity and uniqueness'),
(28, 7, 'Technique', 'Skill in chosen medium'),
(29, 7, 'Purpose/Message', 'Clarity of intention'),
(30, 7, 'Presentation', 'Overall quality'),
(31, 8, 'Clarity of Concept', 'Definition and focus of idea'),
(32, 8, 'Rationale', 'Significance and justification'),
(33, 8, 'Feasibility', 'Practicality of idea'),
(34, 8, 'Organization', 'Logical structure'),
(35, 9, 'Usability', 'Ease of navigation and interaction'),
(36, 9, 'Visual Design', 'Aesthetics and consistency'),
(37, 9, 'Accessibility', 'Inclusiveness and compliance'),
(38, 9, 'Information Architecture', 'Logical arrangement of content'),
(39, 10, 'Functionality', 'Working features and performance'),
(40, 10, 'Design & Layout', 'Aesthetics and responsiveness'),
(41, 10, 'Code Quality', 'Structure and efficiency'),
(42, 10, 'Content Quality', 'Clarity and accuracy of text/media'),
(43, 10, 'User Experience', 'Overall interaction quality');

-- --------------------------------------------------------

--
-- Table structure for table `emailcredentials`
--

CREATE TABLE `emailcredentials` (
  `credentialID` int(2) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emailcredentials`
--

INSERT INTO `emailcredentials` (`credentialID`, `email`, `password`) VALUES
(1, 'learn.webstar@gmail.com', 'mtls vctd rhai cdem');

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
(13, 2, 3, 1),
(14, 2, 34, 1),
(15, 2, 33, 1);

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

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedbackID`, `senderID`, `receiverID`, `message`, `created_at`) VALUES
(3, 2, 18, 'heyyyy', '2025-11-17 17:44:05');

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
  `fileTitle` varchar(255) NOT NULL,
  `fileLink` varchar(500) NOT NULL,
  `uploadedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`fileID`, `courseID`, `userID`, `announcementID`, `lessonID`, `assignmentID`, `submissionID`, `fileAttachment`, `fileTitle`, `fileLink`, `uploadedAt`) VALUES
(1, 34, 1, 2, NULL, NULL, NULL, 'Social_&_Professional_MIDTERM.pdf', '', '', '2025-11-17 14:11:00'),
(2, 34, 1, 2, NULL, NULL, NULL, '', 'Youtu.be', 'https://youtu.be/Xejcpn3_atU', '2025-11-17 14:11:10'),
(3, 34, 1, 3, NULL, NULL, NULL, '', 'Alyssify', 'https://mrllalyssacto.github.io/', '2025-11-17 14:17:43'),
(4, 34, 1, 4, NULL, NULL, NULL, '', 'Webstar - Final Weeks', 'https://docs.google.com/spreadsheets/d/1zZneXyH61wTgs5ViQQ2IyHRC6wYjZIJ0NF8JspMBrOA/preview', '2025-11-17 14:21:37'),
(5, 34, 1, NULL, 2, NULL, NULL, 'IAS_MIDTERM.pdf', '', '', '2025-11-17 14:37:12'),
(6, 34, 1, NULL, 2, NULL, NULL, '', '[ Folk Dance Performance ] Tinikling', 'https://youtu.be/-powVSwYn0w', '2025-11-17 14:37:18'),
(7, 34, 2, NULL, NULL, NULL, 8, '0c5dc8b9-b04b-4622-8730-0223ae22f9a5.jpg', '', '', '2025-11-17 17:18:17'),
(9, 34, 2, NULL, NULL, NULL, 8, 'star-card-highres (41).png', '', '', '2025-11-17 17:24:44'),
(10, 34, 1, NULL, NULL, 4, NULL, 'webstar_(8).sql', '', '', '2025-11-17 18:14:25');

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `inboxID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `messageText` text NOT NULL,
  `notifType` varchar(50) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `isRead` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inbox`
--

INSERT INTO `inbox` (`inboxID`, `enrollmentID`, `messageText`, `notifType`, `createdAt`, `isRead`) VALUES
(1, 14, 'A new test has been posted: TEST NI DOM', 'Course Update', '2025-11-17 15:17:07', 1),
(2, 1, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(3, 3, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(4, 4, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(5, 5, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(6, 6, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(7, 7, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(8, 8, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(9, 9, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(10, 10, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(11, 11, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(12, 12, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:51', 1),
(17, 14, 'A new test has been posted: TEST ni DOM AGAIN', 'Course Update', '2025-11-17 15:22:54', 1),
(18, 14, 'A new test has been posted: TEST MULTIPLECHOICE', 'Course Update', '2025-11-17 15:33:20', 1),
(19, 14, 'A new test has been posted: TEST W PICTURE', 'Course Update', '2025-11-17 15:38:24', 1),
(20, 14, '\"TEST W PICTURE\" was returned by your instructor. You can now view the results.', 'Submissions Update', '2025-11-17 15:49:07', 1),
(21, 14, 'A new task has been assigned: hahaha', 'Course Update', '2025-11-17 16:02:04', 1),
(22, 14, 'A new task has been assigned: Activity #', 'Course Update', '2025-11-17 18:14:25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `leaderboardID` int(11) NOT NULL,
  `enrollmentID` int(11) NOT NULL,
  `timeRange` varchar(10) NOT NULL,
  `periodStart` date NOT NULL,
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `xpPoints` int(11) NOT NULL,
  `previousRank` int(11) NOT NULL,
  `currentRank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaderboard`
--

INSERT INTO `leaderboard` (`leaderboardID`, `enrollmentID`, `timeRange`, `periodStart`, `updatedAt`, `xpPoints`, `previousRank`, `currentRank`) VALUES
(1, 1, 'Weekly', '2025-08-25', '2025-08-30 12:00:00', 240, 0, 1),
(2, 2, 'Weekly', '2025-08-25', '2025-08-30 12:00:00', 0, 0, 1),
(3, 1, 'Weekly', '2025-09-28', '2025-09-28 16:50:22', 240, 0, 0),
(4, 3, 'Weekly', '2025-09-28', '2025-09-28 16:58:09', 0, 0, 2),
(5, 4, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 3),
(6, 5, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 4),
(7, 6, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 5),
(8, 7, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 6),
(9, 8, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 7),
(10, 9, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 8),
(11, 10, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 9),
(12, 11, 'Weekly', '2025-09-01', '2025-09-28 21:03:44', 0, 0, 10),
(13, 12, 'Monthly', '2025-09-01', '2025-09-28 21:34:51', 0, 0, 11),
(15, 14, '', '0000-00-00', '2025-11-17 14:08:20', 290, 0, 1),
(16, 15, '', '0000-00-00', '2025-11-17 16:06:41', 0, 0, 1),
(17, 14, '', '0000-00-00', '2025-11-17 17:18:33', 1044, 0, 0);

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
(1, 1, 'Lesson 1: Introduction to CSS Grid', '1. Explain what HTML is and its role in web development.\n2. Identify the basic structure of an HTML document.\n3. Use common HTML tags such as headings, paragraphs, and links. \n4. Create a simple webpage using basic HTML elements.', '2025-08-30 09:00:00'),
(2, 34, 'Lesson Mo', 'LESSONSSSS', '2025-11-17 14:37:12');

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
(1, 1, 'Excellent', 'Hypothesis clear, insightful; strong background connections', 20.00),
(2, 1, 'Proficient', 'Clear hypothesis, adequate background', 15.00),
(3, 1, 'Basic', 'Hypothesis present but unclear or underdeveloped', 10.00),
(4, 1, 'Needs Improvement', 'Missing or inaccurate introduction elements', 5.00),
(5, 2, 'Excellent', 'Detailed, organized, replicable procedure', 20.00),
(6, 2, 'Proficient', 'Mostly clear with minor gaps', 15.00),
(7, 2, 'Basic', 'Partially explained; difficult to replicate', 10.00),
(8, 2, 'Needs Improvement', 'Missing or unclear procedure', 5.00),
(9, 3, 'Excellent', 'Complete, accurate, professional data displays', 20.00),
(10, 3, 'Proficient', 'Clear with minor errors', 15.00),
(11, 3, 'Basic', 'Noticeable inaccuracies or disorder', 10.00),
(12, 3, 'Needs Improvement', 'Missing or inaccurate data', 5.00),
(13, 4, 'Excellent', 'Insightful analysis linked to scientific concepts', 20.00),
(14, 4, 'Proficient', 'Logical interpretation with adequate depth', 15.00),
(15, 4, 'Basic', 'General or superficial discussion', 10.00),
(16, 4, 'Needs Improvement', 'Weak, incorrect, or missing analysis', 5.00),
(17, 5, 'Excellent', 'Clear, polished, error-free', 20.00),
(18, 5, 'Proficient', 'Minor writing issues', 15.00),
(19, 5, 'Basic', 'Frequent errors but readable', 10.00),
(20, 5, 'Needs Improvement', 'Disorganized, many writing issues', 5.00),
(21, 6, 'Exceptional', 'Deep, meaningful connections and insights', 25.00),
(22, 6, 'Strong', 'Clear insights with thoughtful reflection', 20.00),
(23, 6, 'Adequate', 'Basic reflection; surface level', 15.00),
(24, 6, 'Limited', 'Minimal insight', 10.00),
(25, 7, 'Exceptional', 'Strong integration between experience and concepts', 25.00),
(26, 7, 'Strong', 'Relevant, clear connections', 20.00),
(27, 7, 'Adequate', 'Somewhat connected but general', 15.00),
(28, 7, 'Limited', 'Few or no links to learning', 10.00),
(29, 8, 'Exceptional', 'Smooth, logical organization', 25.00),
(30, 8, 'Strong', 'Well-organized with minor issues', 20.00),
(31, 8, 'Adequate', 'Some inconsistencies', 15.00),
(32, 8, 'Limited', 'Poor organization', 10.00),
(33, 9, 'Exceptional', 'Polished, error-free', 25.00),
(34, 9, 'Strong', 'Minor errors', 20.00),
(35, 9, 'Adequate', 'Noticeable errors', 15.00),
(36, 9, 'Limited', 'Many errors making reading difficult', 10.00),
(37, 10, 'Excellent', 'Wide, diverse, highly relevant sources', 20.00),
(38, 10, 'Good', 'Adequate number of sources', 15.00),
(39, 10, 'Fair', 'Limited or partially relevant sources', 10.00),
(40, 10, 'Poor', 'Few or irrelevant sources', 5.00),
(41, 11, 'Excellent', 'Synthesizes studies into coherent themes', 20.00),
(42, 11, 'Good', 'Shows connections with minor gaps', 15.00),
(43, 11, 'Fair', 'Mostly summaries; limited synthesis', 10.00),
(44, 11, 'Poor', 'No integration', 5.00),
(45, 12, 'Excellent', 'Deep critique with strong reasoning', 20.00),
(46, 12, 'Good', 'Adequate assessment', 15.00),
(47, 12, 'Fair', 'Minimal critique', 10.00),
(48, 12, 'Poor', 'No evaluation', 5.00),
(49, 13, 'Excellent', 'Clear, logical thematic structure', 20.00),
(50, 13, 'Good', 'Mostly organized', 15.00),
(51, 13, 'Fair', 'Noticeable structural issues', 10.00),
(52, 13, 'Poor', 'Disorganized', 5.00),
(53, 14, 'Excellent', 'Accurate and consistent', 20.00),
(54, 14, 'Good', 'Minor errors', 15.00),
(55, 14, 'Fair', 'Several errors', 10.00),
(56, 14, 'Poor', 'Frequent or missing citations', 5.00),
(57, 15, 'Excellent', 'Clear, arguable, insightful thesis', 25.00),
(58, 15, 'Good', 'Clear and focused', 15.00),
(59, 15, 'Fair', 'Present but weak', 10.00),
(60, 15, 'Poor', 'Unclear or missing', 5.00),
(61, 16, 'Excellent', 'Strong supporting evidence and logic', 25.00),
(62, 16, 'Good', 'Adequate support', 15.00),
(63, 16, 'Fair', 'Limited reasoning', 10.00),
(64, 16, 'Poor', 'Unsupported ideas', 5.00),
(65, 17, 'Excellent', 'Well-structured and cohesive', 25.00),
(66, 17, 'Good', 'Mostly organized', 15.00),
(67, 17, 'Fair', 'Some disorder', 10.00),
(68, 17, 'Poor', 'Hard to follow', 5.00),
(69, 18, 'Excellent', 'Polished, error-free', 25.00),
(70, 18, 'Good', 'Minor issues', 15.00),
(71, 18, 'Fair', 'Multiple errors', 10.00),
(72, 18, 'Poor', 'Frequent mechanical errors', 5.00),
(73, 19, 'Excellent', 'Thorough, accurate identification of all issues', 25.00),
(74, 19, 'Good', 'Identifies major issues', 15.00),
(75, 19, 'Fair', 'Partially identifies issues', 10.00),
(76, 19, 'Poor', 'Issues unclear or misunderstood', 5.00),
(77, 20, 'Excellent', 'Deep, theory-based analysis', 25.00),
(78, 20, 'Good', 'Solid analysis', 15.00),
(79, 20, 'Fair', 'General or limited analysis', 10.00),
(80, 20, 'Poor', 'Weak or incorrect', 5.00),
(81, 21, 'Excellent', 'Practical, well-supported solutions', 25.00),
(82, 21, 'Good', 'Reasonable recommendations', 15.00),
(83, 21, 'Fair', 'Vague or general', 10.00),
(84, 21, 'Poor', 'Unsupported or unrealistic', 5.00),
(85, 22, 'Excellent', 'Strong, appropriate evidence', 25.00),
(86, 22, 'Good', 'Adequate support', 15.00),
(87, 22, 'Fair', 'Some evidence', 10.00),
(88, 22, 'Poor', 'Lacks evidence', 5.00),
(89, 23, 'Excellent', 'Complete, accurate summary', 25.00),
(90, 23, 'Good', 'Mostly accurate', 15.00),
(91, 23, 'Fair', 'Minor misinterpretations', 10.00),
(92, 23, 'Poor', 'Incorrect or incomplete', 5.00),
(93, 24, 'Excellent', 'Insightful, well-reasoned critique', 25.00),
(94, 24, 'Good', 'Clear critique', 15.00),
(95, 24, 'Fair', 'Limited critical thinking', 10.00),
(96, 24, 'Poor', 'Minimal or no critique', 5.00),
(97, 25, 'Excellent', 'Strong, relevant evidence', 25.00),
(98, 25, 'Good', 'Adequate examples', 15.00),
(99, 25, 'Fair', 'Some evidence', 10.00),
(100, 25, 'Poor', 'Weak or no evidence', 5.00),
(101, 26, 'Excellent', 'Clear, organized, error-free', 25.00),
(102, 26, 'Good', 'Minor clarity or grammar issues', 15.00),
(103, 26, 'Fair', 'Multiple errors', 10.00),
(104, 26, 'Poor', 'Disorganized, error-ridden', 5.00),
(105, 27, 'Outstanding', 'Highly original and imaginative', 25.00),
(106, 27, 'Strong', 'Good originality', 15.00),
(107, 27, 'Basic', 'Some creativity', 10.00),
(108, 27, 'Limited', 'Lacks originality', 5.00),
(109, 28, 'Outstanding', 'Exceptional technique', 25.00),
(110, 28, 'Strong', 'Solid skills', 15.00),
(111, 28, 'Basic', 'Adequate technique', 10.00),
(112, 28, 'Limited', 'Weak execution', 5.00),
(113, 29, 'Outstanding', 'Strong, meaningful message', 25.00),
(114, 29, 'Strong', 'Clear purpose', 15.00),
(115, 29, 'Basic', 'Some meaning', 10.00),
(116, 29, 'Limited', 'Unclear message', 5.00),
(117, 30, 'Outstanding', 'Outstanding', 25.00),
(118, 30, 'Outstanding', 'Neat and complete', 15.00),
(119, 30, 'Basic', 'Some presentation issues', 10.00),
(120, 30, 'Limited', 'Poor presentation', 5.00),
(121, 31, 'Excellent', 'Clear, strongly articulated concept', 25.00),
(122, 31, 'Excellent', 'Mostly clear', 15.00),
(123, 31, 'Fair', 'Concept somewhat unclear', 10.00),
(124, 31, 'Poor', 'Vague or undeveloped', 5.00),
(125, 32, 'Excellent', 'Strong justification', 25.00),
(126, 32, 'Good', 'Adequate rationale', 15.00),
(127, 32, 'Fair', 'Weak explanation', 10.00),
(128, 32, 'Poor', 'Missing rationale', 5.00),
(129, 33, 'Excellent', 'Highly feasible and well-supported', 25.00),
(130, 33, 'Good', 'Mostly feasible', 15.00),
(131, 33, 'Fair', 'Some feasibility concerns', 10.00),
(132, 33, 'Poor', 'Not feasible', 5.00),
(133, 34, 'Excellent', 'Well-organized', 25.00),
(134, 34, 'Good', 'Minor issues', 15.00),
(135, 34, 'Fair', 'Some disorganization', 10.00),
(136, 34, 'Poor', 'Poorly structured', 5.00),
(137, 35, 'Excellent', 'Highly intuitive, user-friendly', 25.00),
(138, 35, 'Good', 'Mostly intuitive', 15.00),
(139, 35, 'Fair', 'Some usability issues', 10.00),
(140, 35, 'Poor', 'Hard to use', 5.00),
(141, 36, 'Excellent', 'Professional, cohesive visual design', 25.00),
(142, 36, 'Good', 'Attractive and consistent', 15.00),
(143, 36, 'Fair', 'Minor inconsistencies', 10.00),
(144, 36, 'Poor', 'Unappealing or messy', 5.00),
(145, 37, 'Excellent', 'Fully accessible', 25.00),
(146, 37, 'Good', 'Mostly accessible', 15.00),
(147, 37, 'Fair', 'Limited accessibility', 10.00),
(148, 37, 'Poor', 'Not accessible', 5.00),
(149, 38, 'Excellent', 'Clear structure and flow', 25.00),
(150, 38, 'Good', 'Mostly clear', 15.00),
(151, 38, 'Fair', 'Some confusion in structure', 10.00),
(152, 38, 'Poor', 'Poorly organized', 5.00),
(153, 39, 'Excellent', 'Fully functional with no errors', 20.00),
(154, 39, 'Good', 'Minor issues', 15.00),
(155, 39, 'Fair', 'Some features broken', 10.00),
(156, 39, 'Poor', 'Major issues or nonfunctional', 5.00),
(157, 40, 'Excellent', 'Clean, responsive, visually appealing', 20.00),
(158, 40, 'Good', 'Mostly strong design', 15.00),
(159, 40, 'Fair', 'Some layout issues', 10.00),
(160, 40, 'Poor', 'Poor design or non-responsive', 5.00),
(161, 41, 'Excellent', 'Clean, documented, efficient code', 20.00),
(162, 41, 'Good', 'Minor inefficiencies', 15.00),
(163, 41, 'Fair', 'Some code issues', 10.00),
(164, 41, 'Poor', 'Poorly structured code', 5.00),
(165, 42, 'Excellent', 'Accurate, clear, engaging content', 20.00),
(166, 42, 'Good', 'Mostly clear', 15.00),
(167, 42, 'Fair', 'Some unclear or inaccurate info', 10.00),
(168, 42, 'Poor', 'Incomplete or poorly written', 5.00),
(169, 43, 'Excellent', 'Excellent', 20.00),
(170, 43, 'Good', 'Mostly smooth', 15.00),
(171, 43, 'Fair', 'Some issues', 10.00),
(172, 43, 'Poor', 'Frustrating experience', 5.00);

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
(158, 2, NULL, NULL, 3, '2025-11-06 17:56:42'),
(161, 2, NULL, NULL, 4, '2025-11-16 02:50:00'),
(162, 2, 7, NULL, NULL, '2025-11-16 02:51:21'),
(163, 2, NULL, 17, NULL, '2025-11-16 02:51:32'),
(164, 2, NULL, NULL, 9, '2025-11-16 02:51:39');

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
  `colorThemeID` int(11) NOT NULL DEFAULT 1,
  `starCard` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profileID`, `userID`, `bio`, `webstars`, `emblemID`, `coverImageID`, `colorThemeID`, `starCard`) VALUES
(1, 2, 'Welcome to my Webstar profile!', 2132, 7, 17, 9, 2),
(2, 4, 'Welcome to my Webstar profile!', 700, 1, 1, 1, 2);

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
(1, 1, 480, 1, '2025-11-20 02:45:43'),
(2, 3, 0, 3, '2025-11-20 02:45:43'),
(3, 8, 0, 8, '2025-11-20 02:45:43'),
(4, 6, 0, 6, '2025-11-20 02:45:43'),
(5, 11, 0, 11, '2025-11-20 02:45:43'),
(6, 10, 0, 10, '2025-11-20 02:45:43'),
(7, 5, 0, 5, '2025-11-20 02:45:43'),
(8, 7, 0, 7, '2025-11-20 02:45:43'),
(9, 9, 0, 9, '2025-11-20 02:45:43'),
(10, 4, 0, 4, '2025-11-20 02:45:43'),
(11, 12, 0, 2, '2025-11-20 02:45:43'),
(12, 2, 0, 1, '2025-11-17 18:25:49'),
(13, 14, 1334, 1, '2025-11-17 18:28:07'),
(14, 15, 0, 1, '2025-11-17 16:06:49');

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
(1, 'Laboratory Report Rubric', 'Preset', NULL, 100),
(2, 'Reflection Paper Rubric', 'Preset', NULL, 100),
(3, 'Literature Review Rubric', 'Preset', NULL, 100),
(4, 'Essay Rubric', 'Preset', NULL, 100),
(5, 'Case Study Rubric', 'Preset', NULL, 100),
(6, 'Article Review Rubric', 'Preset', NULL, 100),
(7, 'Creative Work Rubric', 'Preset', NULL, 100),
(8, 'Concept Paper Rubric', 'Preset', NULL, 100),
(9, 'UI/UX Design Rubric', 'Preset', NULL, 100),
(10, 'Website Development Rubric', 'Preset', NULL, 100);

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

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`scoreID`, `userID`, `submissionID`, `testID`, `score`, `feedback`, `gradedAt`) VALUES
(1, 2, NULL, 5, 5, NULL, '2025-11-17 15:47:17'),
(2, 1, 8, NULL, 65, 'Nice try', '2025-11-17 17:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `selectedlevels`
--

CREATE TABLE `selectedlevels` (
  `selectedLevelID` int(11) NOT NULL,
  `submissionID` int(11) NOT NULL,
  `levelID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `selectedlevels`
--

INSERT INTO `selectedlevels` (`selectedLevelID`, `submissionID`, `levelID`) VALUES
(1, 8, 154),
(2, 8, 160),
(3, 8, 164),
(4, 8, 165),
(5, 8, 169);

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

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submissionID`, `userID`, `assessmentID`, `scoreID`, `submittedAt`, `isSubmitted`, `modalShown`) VALUES
(1, 2, 2, 0, '2025-11-17 14:47:08', 0, 0),
(2, 2, 3, 0, '2025-11-17 14:53:54', 0, 0),
(4, 2, 6, 0, '2025-11-17 15:00:46', 0, 0),
(5, 2, 2, 0, '2025-11-17 15:25:26', 0, 0),
(6, 2, 4, 0, '2025-11-17 15:36:13', 0, 0),
(7, 2, 5, 0, '2025-11-17 15:42:31', 0, 0),
(8, 2, 7, 2, '2025-11-17 17:24:43', 1, 1);

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
(2, 'Student', 'What is required on the Login Setup Page?', 'If you are a new user, you will be directed to the Account Setup Page where you are required to enter a <strong>valid email address</strong>. A <strong>verification code</strong> will be sent to your <strong>email</strong>, and you must input that <strong>code</strong> to confirm your <strong>ownership of the account</strong>. After the <strong>verification</strong>, you will be directed to create a <strong>new password</strong> and <strong>confirm it</strong>. Once you complete these steps, you'),
(3, 'Student', 'How do I enroll in my first course?', 'You enroll by entering the <strong>access code</strong> given by your <strong>professor</strong>. Your <strong>new course</strong> will appear in the \"<strong>Your Courses</strong>\" list immediately. The rest of your <strong>dashboard</strong> will show an <strong>empty state</strong> until your <strong>course</strong> is active with <strong>announcements</strong> and <strong>tasks</strong>.'),
(4, 'Student', 'What is the Home Page and what does it show?', 'The <strong>Webstar Home page</strong> is your central, <strong>personalized dashboard</strong> providing a quick overview of your <strong>academic life</strong>, featuring your <strong>enrolled courses</strong>, <strong>recent announcements</strong>, a <strong>timeline</strong> of <strong>upcoming tasks</strong>, and your <strong>leaderboard rank</strong>.'),
(5, 'Student', 'What information is available on the Courses page?', 'The <strong>Courses page</strong> serves as your complete <strong>directory</strong>, listing every <strong>course</strong> you\'ve enrolled in, with each <strong>card</strong> displaying <strong>key details</strong> like the <strong>Course Code</strong> and <strong>Name</strong>, the <strong>Professor Name</strong>, and the <strong>class Schedule</strong>.'),
(6, 'Student', 'How can I manage or find my courses?', 'The <strong>Courses page</strong> offers several <strong>organizational tools</strong>, including a <strong>Search Bar</strong> to find <strong>courses</strong> by <strong>code</strong> or <strong>name</strong>, and a <strong>Status Filter</strong> that allows you to sort the list to view either \"<strong>Active</strong>\" or \"<strong>Archived</strong>\" classes.'),
(7, 'Student', 'How do I add a new course?', 'To add an <strong>additional course</strong>, click the \"<strong>+ Join Course</strong>\" button in the <strong>top right corner</strong>, then enter the <strong>access code</strong> provided by your <strong>professor</strong> in the <strong>access form</strong> and click \"<strong>Enroll</strong>\" to add the <strong>class</strong> to your list.'),
(8, 'Student', 'What does the Course Info page contain?', 'The <strong>Course Info page</strong> aggregates all <strong>resources</strong> and <strong>activities</strong> for a <strong>single class</strong>, featuring <strong>top navigation tabs</strong> for <strong>Announcements, Lessons, To-do, Files, Leaderboard, Report</strong>, and <strong>Students</strong>, alongside a <strong>sidebar card</strong> that tracks your overall <strong>Class Standing, level</strong>, and <strong>current Quests</strong>.'),
(9, 'Student', 'How do I enroll or unenroll from a course?', 'You can choose to <strong>unenroll</strong> from a <strong>course</strong> by clicking the <strong>three-dot menu</strong> at the <strong>top</strong> of the <strong>course card</strong> on the <strong>left side</strong> of the <strong>Course Info page</strong>.'),
(10, 'Student', 'How do I view professor announcements and access a', 'The <strong>Announcements tab</strong> contains <strong>messages</strong> and <strong>updates</strong> posted by your <strong>professor</strong>, which you can <strong>filter</strong> to view the <strong>newest</strong> or <strong>oldest messages</strong>. If an <strong>announcement</strong> includes <strong>extra resources</strong>, you can click the <strong>designated button</strong> to view any <strong>attached files</strong>.'),
(11, 'Student', 'Where can I find the learning materials and lectur', 'The <strong>Lessons tab</strong> contains all the organized <strong>lectures</strong> and <strong>learning materials</strong> for the <strong>course</strong>. Clicking on any <strong>lesson card</strong> will redirect you to the <strong>Lesson Info</strong>, where you can access and review the specific <strong>content</strong> provided by your <strong>professor</strong>.'),
(12, 'Student', 'How do I view and manage my activities for the cou', 'The <strong>To-do tab</strong> lists all required <strong>course activities</strong>, which you can <strong>manage</strong> by <strong>sorting</strong> them or <strong>filtering</strong> by <strong>Status</strong> (<strong>Pending, Missing, Done</strong>). Clicking an <strong>activity</strong> takes you to the respective page where you can <strong>submit your work</strong>, take your <strong>exams</strong>.'),
(13, 'Student', 'How do I submit my work for an assignment or activ', 'In the <strong>submission area</strong>, you can attach your completed <strong>work</strong> either by <strong>uploading files</strong> from your <strong>device</strong> or by providing a <strong>link</strong> to an <strong>external source</strong> (like <strong>Google Drive</strong> or <strong>Figma</strong>), and then click \"<strong>Turn In</strong>\" to submit.'),
(14, 'Student', 'What rewards do I receive after submitting an assi', 'After successfully <strong>submitting</strong> a <strong>task</strong> or taking an <strong>exam</strong>, you will be shown a <strong>reward screen</strong> indicating the <strong>XPs (Experience Points)</strong> and <strong>Webstars</strong> you have earned.'),
(15, 'Student', 'What is the purpose of the XP Multiplier and how d', 'The <strong>XP Multiplier</strong> is used to instantly increase the <strong>Experience Points (XPs)</strong> you earned from an <strong>exam</strong>, helping you <strong>level up faster</strong> and improve your <strong>standing</strong>.'),
(16, 'Student', 'Where can I view the results and answers for my co', 'You can view the full <strong>results</strong>, including your <strong>score</strong> and the <strong>correct/incorrect answers</strong>, by returning to the <strong>Exam Info</strong>. The <strong>results</strong> become available only after your <strong>instructor</strong> has released them; at that point, you will see a \"<strong>View Results</strong>\" button on the page.'),
(17, 'Student', 'Where can I find and download course materials sha', 'The <strong>Files tab</strong> serves as a <strong>repository</strong> for all <strong>documents</strong> and <strong>links</strong> shared by your <strong>professor</strong>. If the <strong>item</strong> is a <strong>document</strong>, clicking it allows you to <strong>view the file</strong> and offers a <strong>download button</strong>. If it is a <strong>link</strong>, clicking it will open the <strong>external resource</strong>.'),
(18, 'Student', 'What information is in the Leaderboard?', 'The <strong>Leaderboard tab</strong> shows a <strong>ranked list</strong> of <strong>students</strong> based on their <strong>performance</strong>, highlighting the <strong>top 3</strong> in the class. You can also use the <strong>search function</strong> to quickly find any of your <strong>classmates</strong> within the list.'),
(19, 'Student', 'How can I view a detailed performance report for a', 'To view a <strong>detailed performance report</strong> for a <strong>classmate</strong>, you must go to the <strong>Leaderboard</strong>, click on their <strong>name</strong>, and you\'ll be redirected to their <strong>Report tab</strong>, which shows their <strong>on-time submissions, overall performance, records, charts</strong>, and <strong>badges</strong>.'),
(20, 'Student', 'How can I view a list of all students enrolled in ', 'To see everyone <strong>enrolled</strong>, navigate to the <strong>student’s tab</strong> within the <strong>Course Info</strong>. You can view the full <strong>class roster</strong>, use the <strong>search bar</strong> to quickly find a specific <strong>student</strong>, and apply <strong>filters</strong> to sort the list by <strong>newest</strong> or <strong>oldest student</strong>.'),
(21, 'Student', 'What notifications will I find in my Inbox?', '<strong>Inbox</strong> serves as your <strong>notification center</strong>, containing <strong>updates</strong> on every relevant <strong>course activity</strong>, including <strong>notifications</strong> for <strong>submitted tasks</strong>, <strong>lessons</strong>, <strong>exams</strong>, <strong>announcements</strong>, <strong>submissions</strong>, and <strong>feedback</strong>. You can <strong>sort</strong> your <strong>notifications</strong> and <strong>filter</strong> them by <strong>cour'),
(22, 'Student', 'What will I see in the My To-do section, and how c', '<strong>My To-do</strong> displays all your <strong>activities, exams</strong>, and <strong>tasks</strong> from every <strong>course</strong> you are enrolled in. You can organize this list by using <strong>filters</strong> to view <strong>activities</strong> by <strong>specific course</strong> or by <strong>Status</strong> (<strong>Pending, Missing, Done</strong>), and you can <strong>sort</strong> the items from <strong>newest</strong> to <strong>oldest</strong>. Clicking on any <strong>activi'),
(23, 'Student', 'What is the purpose of the Explore page?', 'The <strong>Explore page</strong> serves as the main <strong>search directory</strong> for the platform, allowing you to easily <strong>search</strong> for and find <strong>information</strong> about <strong>students</strong> and <strong>professors</strong> enrolled or working within the <strong>Webstar system</strong>.'),
(24, 'Student', 'What kinds of items can I purchase in the Shop?', 'The <strong>Shop</strong> allows you to purchase various <strong>cosmetic items</strong> to personalize your <strong>profile</strong>, including <strong>emblems, cover photos, moving profile photos</strong>, and <strong>profile colors</strong>. You can view all the <strong>items</strong> you currently own in the \"<strong>My Items</strong>\" section.'),
(25, 'Student', 'What will I use to buy the items in the Shop?', 'You can use your earned <strong>Webstars</strong> to purchase <strong>items</strong> in the <strong>Shop</strong>. Each <strong>item</strong> has a <strong>different price</strong> indicated in <strong>Webstars</strong>.'),
(26, 'Student', 'What happens after I purchase an item from the Sho', 'Once you successfully <strong>purchase</strong> an <strong>item</strong>, it is immediately <strong>applied</strong> to your <strong>profile</strong> to <strong>customize</strong> your <strong>look</strong> and display your <strong>achievement</strong>.'),
(27, 'Student', 'How can I edit my profile?', 'You can <strong>edit your profile</strong> by clicking <strong>Edit Profile</strong> in the <strong>Settings sidebar</strong>. Here, you can update your <strong>personal information</strong> such as your <strong>name, email, and profile picture</strong>. Once you <strong>save</strong> the changes, your <strong>updated information</strong> will be reflected across the <strong>system</strong>.'),
(28, 'Student', 'How can I share my Star Card with others?', 'You can <strong>share your Star Card</strong> by going to your <strong>Settings sidebar</strong> and clicking \"<strong>Edit Profile</strong>\". On the <strong>profile editing page</strong>, locate the specific <strong>section</strong> or <strong>card</strong> for the <strong>Star Card</strong>, where you will find and click the <strong>Share button</strong> and you can <strong>download</strong> the <strong>Star Card</strong> in <strong>My Star Card tab</strong>.'),
(29, 'Student', 'How can I reset my password?', 'You can <strong>reset your password</strong> under the <strong>Edit Profile tab</strong> in the <strong>Settings sidebar</strong>. You are required to enter a <strong>valid email address</strong>, and a <strong>verification code</strong> will be sent to your <strong>email</strong>. You must input the <strong>code</strong> to confirm <strong>ownership of your account</strong>. After the <strong>verification</strong>, you will be directed to create a <strong>new password</strong> and <strong>confi'),
(30, 'Student', 'How can I customize my profile?', 'You can <strong>customize your settings</strong> by clicking <strong>Settings Customization tab</strong>. Here, you can also <strong>design borders</strong> for your <strong>cover photo</strong> and <strong>profile photo</strong> to <strong>personalize your profile</strong> further.'),
(31, 'Student', 'How can I access support and send feedback?', 'You can <strong>access support</strong> by clicking <strong>Support tab</strong>. This will provide you with <strong>resources, guides</strong>, and <strong>options</strong> to contact the <strong>support team</strong> if you need help with the <strong>system</strong>. Under <strong>Support</strong>, there is an <strong>Email option</strong> where you can compose and send <strong>messages</strong> directly to the <strong>system’s support email</strong>. This allows you to <strong>report issues</'),
(32, 'Student', 'How can I provide feedback?', 'You can <strong>provide feedback</strong> by clicking <strong>Feedback tab</strong>. This allows you to <strong>share suggestions</strong>, <strong>report issues</strong>, or give <strong>comments</strong> directly to the <strong>development or support team</strong> to help <strong>improve the system</strong>.'),
(33, 'Student', 'How can I manage and control my email notification', 'You can <strong>manage your email notifications</strong> by navigating to the <strong>Preferences tab</strong> within your <strong>settings</strong>. This page provides <strong>toggle buttons</strong> that allow you to select whether you wish to receive <strong>email updates</strong> regarding your <strong>course activities, quests, deadlines</strong>, and <strong>announcements</strong>.'),
(34, 'Professor', 'What is Webstar', 'Webstar is an interactive online learning platform designed to help users explore courses, track their progress, and earn points through engaging activities. It focuses on making learning fun, structured, and community driven.'),
(35, 'Professor', 'What is required on the Login Setup Page?', 'If you are a new user, you will be directed to the <strong>Account Setup Page</strong> where you are required to enter a <strong>valid email address</strong>. A <strong>verification code</strong> will be sent to your email, and you must input that code to confirm your ownership of the account. After the verification, you will be directed to create a new password and confirm it. Once you complete these steps, you can proceed to the professor dashboard.'),
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
(1, 4, 'Mabaho'),
(2, 4, 'Mabango'),
(3, 4, 'DI ko alam, amuyin mo');

-- --------------------------------------------------------

--
-- Table structure for table `testquestions`
--

CREATE TABLE `testquestions` (
  `testQuestionID` int(5) NOT NULL,
  `testID` int(5) NOT NULL,
  `testQuestion` varchar(100) NOT NULL,
  `questionType` enum('Multiple Choice','Identification') NOT NULL DEFAULT 'Multiple Choice',
  `testQuestionImage` varchar(256) DEFAULT NULL,
  `correctAnswer` varchar(50) NOT NULL,
  `testQuestionPoints` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testquestions`
--

INSERT INTO `testquestions` (`testQuestionID`, `testID`, `testQuestion`, `questionType`, `testQuestionImage`, `correctAnswer`, `testQuestionPoints`) VALUES
(1, 1, 'Anong pangalan ng aso ni marimar?', 'Identification', '1763363827_logo-removebg-preview.png', 'Pulgoso', 2),
(3, 3, 'Anong pangalan ng aso ni marimar?', 'Identification', NULL, 'Pulgoso', 12),
(4, 4, 'Anong amoy ng utot ni dom?', 'Multiple Choice', NULL, 'DI ko alam, amuyin mo', 2),
(5, 5, 'Crush mo ba ako?', 'Identification', '1763365103_profile2.jpg', 'oo', 1);

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
(2, 4, 4, 2, 'Mabaho', 0),
(3, 5, 5, 2, 'oO', 0);

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
(1, 2, 1, 'Pending', '2025-11-17 15:17:07', 1, NULL, NULL),
(13, 2, 3, 'Pending', '2025-11-17 15:22:53', 1, NULL, NULL),
(14, 2, 4, 'Submitted', '2025-11-17 15:36:13', 1, 60, '2025-11-17 15:35:13'),
(15, 2, 5, 'Returned', '2025-11-17 15:42:31', 1, 120, '2025-11-17 15:38:34'),
(16, 2, 7, 'Graded', '2025-11-17 17:40:23', 1, NULL, NULL),
(17, 2, 8, 'Pending', '2025-11-17 18:20:10', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userinfo`
--

CREATE TABLE `userinfo` (
  `userInfoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `profilePicture` varchar(100) NOT NULL DEFAULT 'defaultProfile.png',
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
(1, 2, 'defaultProfile.png', 'James', 'Mendoza', 'Smith', '202310001', '1', 'Female', '2', 1, 'jane.smith@university.edu', '', '', 'https://instagram.com/jane.smith', '2025-08-30 08:18:53', 0),
(2, 1, 'defaultProfile.png', 'Christopher Jay', '', 'De Claro', '202310002', '1', 'Male', '2', 1, 'james.dom@university.edu', 'https://facebook.com/james.dom', 'https://linkedin.com/in/james-dom', 'https://instagram.com/james.dom', '2025-08-30 08:18:53', 1),
(3, 3, 'defaultProfile.png', 'John', 'Cruz', 'Doe', '202310003', '1', 'Male', '2', 1, 'john.doe@university.edu', '', '', '', '2025-09-28 11:58:33', 0),
(4, 4, 'defaultProfile.png', 'Michael', 'A.', 'Lee', '202310003', '1', 'Male', '2', 1, 'michael.lee@school.edu', NULL, NULL, NULL, '2025-09-28 20:59:48', 0),
(5, 5, 'defaultProfile.png', 'Sophia', 'B.', 'Garcia', '202310004', '1', 'Female', '2', 1, 'sophia.garcia@school.edu', 'facebook.com/sophia.garcia', 'linkedin.com/in/sophiagarcia', 'instagram.com/sophia.garcia', '2025-09-28 20:59:48', 1),
(6, 6, 'defaultProfile.png', 'Daniel', 'C.', 'Kim', '202310005', '1', 'Male', '3', 2, 'daniel.kim@school.edu', 'facebook.com/daniel.kim', 'linkedin.com/in/danielkim', 'instagram.com/daniel.kim', '2025-09-28 20:59:48', 0),
(7, 7, 'defaultProfile.png', 'Olivia', 'D.', 'Brown', '202310006', '1', 'Female', '2', 2, 'olivia.brown@school.edu', 'facebook.com/olivia.brown', 'linkedin.com/in/oliviabrown', 'instagram.com/olivia.brown', '2025-09-28 20:59:48', 0),
(8, 8, 'defaultProfile.png', 'Ethan', 'E.', 'Wilson', '202310007', '1', 'Male', '2', 1, 'ethan.wilson@school.edu', 'facebook.com/ethan.wilson', 'linkedin.com/in/ethanwilson', 'instagram.com/ethan.wilson', '2025-09-28 20:59:48', 1),
(9, 9, 'defaultProfile.png', 'Isabella', 'F.', 'Martin', '202310008', '1', 'Female', '2', 1, 'isabella.martin@school.edu', 'facebook.com/isabella.martin', 'linkedin.com/in/isabellamartin', 'instagram.com/isabella.martin', '2025-09-28 20:59:48', 1),
(10, 10, 'defaultProfile.png', 'Liam', 'G.', 'Torres', '202310009', '1', 'Male', '1', 2, 'liam.torres@school.edu', 'facebook.com/liam.torres', 'linkedin.com/in/liamtorres', 'instagram.com/liam.torres', '2025-09-28 20:59:48', 1),
(11, 11, 'defaultProfile.png', 'Emma', 'H.', 'Davis', '202310010', '1', 'Female', '2', 1, 'emma.davis@school.edu', 'facebook.com/emma.davis', 'linkedin.com/in/emmadavis', 'instagram.com/emma.davis', '2025-09-28 20:59:48', 1),
(12, 12, 'defaultProfile.png', 'Chloe', 'I.', 'Nguyen', '202310011', '1', 'Female', '2', 1, 'chloe.nguyen@school.edu', '', 'linkedin.com/in/chloenguyen', 'instagram.com/chloe.nguyen', '2025-09-28 21:34:51', 1),
(13, 18, 'defaultProfile.png', 'Admin', '', 'User', NULL, '6', '', '4', 4, 'admin@example.com', NULL, NULL, NULL, '2025-11-03 17:57:47', 1),
(14, 19, 'defaultProfile.png', 'Christian James', '', 'Torrillo', NULL, '6', '', '4', 1, 'christian@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(15, 20, 'defaultProfile.png', 'Ayisha Sofhia', '', 'Estoque', NULL, '6', '', '4', 1, 'ayisha@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(16, 21, 'defaultProfile.png', 'Marielle Alyssa', '', 'Cato', NULL, '6', '', '4', 1, 'marielle@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(17, 22, 'defaultProfile.png', 'Neil Jeferson', '', 'Vergara', NULL, '6', '', '4', 1, 'neil@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1),
(18, 23, 'defaultProfile.png', 'Shane Rhyder', '', 'Silverio', NULL, '6', '', '4', 1, 'shane@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 11),
(19, 24, 'defaultProfile.png', 'Kimberly Joan', '', 'Palla', NULL, '6', '', '4', 1, 'kimberly@example.com', NULL, NULL, NULL, '2025-11-03 17:57:48', 1);

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
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `populate_profile_table` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    INSERT INTO profile (
        userID,
        webstars,
        emblemID,
        coverImageID,
        colorThemeID,
        starCard
    ) VALUES (
        NEW.userID,
        1000,
        1,
        1,
        1,
        ''
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `visitID` int(11) NOT NULL,
  `dateVisited` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`visitID`, `dateVisited`) VALUES
(1, '2025-11-20 02:45:32');

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
(22, 2, NULL, 'Shop Purchase', -150, '2025-11-06 17:56:42'),
(25, 2, NULL, 'Shop Purchase', -150, '2025-11-16 02:50:00'),
(26, 2, NULL, 'Shop Purchase', -250, '2025-11-16 02:51:21'),
(27, 2, NULL, 'Shop Purchase', -100, '2025-11-16 02:51:32'),
(28, 2, NULL, 'Shop Purchase', -50, '2025-11-16 02:51:39'),
(29, 2, 2, 'Tests', 3, '2025-11-17 14:47:08'),
(31, 2, 3, 'Tests', 14, '2025-11-17 14:53:54'),
(32, 2, 5, 'Tests', 3, '2025-11-17 14:59:45'),
(33, 2, 6, 'Tests', 1, '2025-11-17 15:00:46'),
(34, 2, 2, 'Tests', 12, '2025-11-17 15:25:26'),
(35, 2, 2, 'XP Multiplier Usage', -1000, '2025-11-17 15:28:03'),
(36, 2, 4, 'Tests', 2, '2025-11-17 15:36:13'),
(37, 2, 4, 'XP Multiplier Usage', -1000, '2025-11-17 15:37:16'),
(38, 2, 5, 'Tests', 1, '2025-11-17 15:42:31'),
(39, 2, 7, 'Submission', 104, '2025-11-17 17:18:33'),
(40, 2, 7, 'Unsubmit', -50, '2025-11-17 17:21:17');

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
-- Indexes for table `emailcredentials`
--
ALTER TABLE `emailcredentials`
  ADD PRIMARY KEY (`credentialID`);

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
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`visitID`);

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
  MODIFY `noteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignmentID` int(5) NOT NULL AUTO_INCREMENT;

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
  MODIFY `courseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `courseschedule`
--
ALTER TABLE `courseschedule`
  MODIFY `courseScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `coverimage`
--
ALTER TABLE `coverimage`
  MODIFY `coverImageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `criterionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `emailcredentials`
--
ALTER TABLE `emailcredentials`
  MODIFY `credentialID` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emblem`
--
ALTER TABLE `emblem`
  MODIFY `emblemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `fileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inbox`
--
ALTER TABLE `inbox`
  MODIFY `inboxID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `leaderboardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lessonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `levelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT for table `myitems`
--
ALTER TABLE `myitems`
  MODIFY `myItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `profileID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `programID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rubric`
--
ALTER TABLE `rubric`
  MODIFY `rubricID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `scoreID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `selectedlevels`
--
ALTER TABLE `selectedlevels`
  MODIFY `selectedLevelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `submissionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `supports`
--
ALTER TABLE `supports`
  MODIFY `supportID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `testquestionchoices`
--
ALTER TABLE `testquestionchoices`
  MODIFY `choiceID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testquestions`
--
ALTER TABLE `testquestions`
  MODIFY `testQuestionID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `testresponses`
--
ALTER TABLE `testresponses`
  MODIFY `testResponseID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `testID` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `todoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `userinfo`
--
ALTER TABLE `userinfo`
  MODIFY `userInfoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `visitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `webstars`
--
ALTER TABLE `webstars`
  MODIFY `webstarsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
