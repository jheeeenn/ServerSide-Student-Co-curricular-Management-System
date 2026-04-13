-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 06:20 PM
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
-- Database: `student_cocurricular_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `achievement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `achievement_type` varchar(100) NOT NULL,
  `organizer` varchar(150) NOT NULL,
  `date_achieved` date NOT NULL,
  `description` text NOT NULL,
  `certificate_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`achievement_id`, `user_id`, `title`, `achievement_type`, `organizer`, `date_achieved`, `description`, `certificate_file`, `created_at`, `updated_at`, `event_id`) VALUES
(11, 6, 'Hackaton', 'Award', 'UTAR', '2026-04-01', 'Champion', '1775799956_2.jpg', '2026-04-10 05:45:56', '2026-04-13 14:12:02', NULL),
(14, 6, 'App Development', 'Medal', 'UTAR', '2026-01-01', '2nd Place', '1775801224_1.png', '2026-04-10 06:07:04', '2026-04-13 14:12:05', NULL),
(15, 6, 'Cybersecurity Tournament', 'Other', 'TARC', '2024-02-02', '10th Place', '', '2026-04-10 06:13:40', '2026-04-13 14:12:13', NULL),
(22, 4, 'Best Facilitator Certificate', 'Certificate', 'Google Developer Student Club UTAR', '2026-03-12', 'Recognized for effective support and facilitation during the Flutter Fundamentals Workshop.', '', '2026-04-13 16:19:28', '2026-04-13 16:19:28', 14),
(23, 4, 'Appreciation Award for Volunteer Service', 'Award', 'Red Crescent Society', '2026-03-28', 'Awarded for active volunteer service and support during the Interfaculty Blood Donation Drive.', '', '2026-04-13 16:19:28', '2026-04-13 16:19:28', 15),
(24, 4, 'Quarter-Finalist - Model ASEAN Debate 2026', 'Other', 'Debate and Public Speaking Club', '2026-04-09', 'Reached the quarter-final stage in the Model ASEAN Debate 2026 competition.', '', '2026-04-13 16:19:28', '2026-04-13 16:19:28', 16),
(25, 4, 'Dean\'s Co-Curricular Excellence Recognition', 'Award', 'Faculty of ICT', '2026-04-25', 'Recognized for consistent participation and contribution across technical, service, and leadership activities.', '', '2026-04-13 16:19:28', '2026-04-13 16:19:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `club_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `club_name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `join_date` date NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`club_id`, `user_id`, `club_name`, `role`, `join_date`, `description`) VALUES
(2, 8, 'Chess Club', 'Member', '2025-07-11', 'Member that plays casually, not competitively.'),
(3, 8, 'Board Games Club', 'Member', '2025-01-22', 'Board Games Club Member'),
(4, 8, 'Dodgeball', 'Treasurer', '2024-02-05', 'Manages the finances of the dodgeball club to effectively utilize resources so that the club can benefit from it. Usual tasks include weekly monetary checkup, investing in proper equipment, etc.'),
(5, 9, 'Bodybuilding Club', 'President', '2023-08-01', 'President of the bodybuilding club, where members gain access to a multitude of gym equipment and seniors who are as friendly as they come. The motto: LET\'S GET SWOLE!'),
(6, 9, 'Cooking Club', 'Treasurer', '2025-01-04', 'Member of the cooking club, where regular cooking-related activities take place at the university kitchens, while accompanied and taught by the finest chefs and lecturers in the club.'),
(7, 9, 'Movie Club', 'Member', '2025-05-07', 'Where club members watch movies, then give their thoughts on it, while discussing and debating the movie\'s qualities with other members with the goal of determining the attributes that set a movie\'s watchability and reputation.'),
(8, 9, 'Movie Club', 'Vice President', '2026-03-08', 'Where club members watch movies, then give their thoughts on it, while discussing and debating the movie\'s qualities with other members with the goal of determining the attributes that set a movie\'s watchability and reputation.'),
(12, 4, 'Google Developer Student Club UTAR', 'Vice President', '2025-09-15', 'Helps organize technical workshops, sharing sessions, and student developer activities on campus.'),
(13, 4, 'Red Crescent Society', 'Member', '2025-10-02', 'Participates in volunteer work, blood donation campaigns, and welfare-related service activities.'),
(14, 4, 'Debate and Public Speaking Club', 'Secretary', '2026-01-10', 'Supports meeting coordination, event planning, and public speaking training activities.'),
(15, 4, 'Basketball Club', 'Member', '2025-08-20', 'Regularly joins training sessions, friendly matches, and interfaculty sports activities.');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_title` varchar(150) NOT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `organizer` varchar(150) NOT NULL,
  `event_date` date NOT NULL,
  `location` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `club_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `user_id`, `event_title`, `event_type`, `organizer`, `event_date`, `location`, `description`, `created_at`, `club_id`) VALUES
(1, 3, 'event 1', 'Competition', '1234', '2026-04-07', 'block n', '12312342131223123123123123123123213', '2026-04-07 12:21:52', NULL),
(3, 3, '第一个', '', '1234asdavac', '2027-01-29', 'block k   hall 1', '12321 wewasd asd asd \r\nasdasdas\r\nasdas\r\nasdasd\r\nasd', '2026-04-07 13:27:21', NULL),
(5, 3, 'event 3', 'Workshop', '123', '0132-03-12', '123', '123', '2026-04-07 14:03:21', NULL),
(10, 8, 'Men\'s 300M National Tournament Runner-Up', 'Competition', 'Omelympics', '2021-12-22', '4245, James F. Burlington Street, 10780, Gramma\'s Pavements, Sydney', 'Runner-up to men\'s 300M national tournament.', '2026-04-10 14:14:59', NULL),
(14, 4, 'Flutter Fundamentals Workshop', 'Workshop', 'Google Developer Student Club UTAR', '2026-03-12', 'ICT Lab 3', 'A hands-on workshop introducing Flutter basics, widget structure, and simple mobile UI development.', '2026-04-13 16:19:28', 12),
(15, 4, 'Interfaculty Blood Donation Drive', 'University Event', 'Red Crescent Society', '2026-03-28', 'Student Pavilion', 'A community service event encouraging blood donation among students and staff.', '2026-04-13 16:19:28', 13),
(16, 4, 'Model ASEAN Debate 2026', 'Competition', 'Debate and Public Speaking Club', '2026-04-09', 'Lecture Hall A', 'An inter-university debate competition focused on regional and policy-related issues.', '2026-04-13 16:19:28', 14),
(17, 4, 'AI Career Talk: Building with LLMs', 'Talk', 'Faculty of ICT', '2026-04-18', 'DK A', 'An industry sharing session on AI careers, large language models, and real-world software development.', '2026-04-13 16:19:28', 12),
(18, 4, '3-on-3 Friendly Basketball Carnival', 'Competition', 'Basketball Club', '2026-05-03', 'Sports Complex', 'A friendly sports event involving mixed teams from different faculties.', '2026-04-13 16:19:28', 15);

-- --------------------------------------------------------

--
-- Table structure for table `merits`
--

CREATE TABLE `merits` (
  `merit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_title` varchar(100) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `organizer` varchar(100) DEFAULT NULL,
  `activity_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_hours` decimal(5,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `event_id` int(11) DEFAULT NULL,
  `club_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merits`
--

INSERT INTO `merits` (`merit_id`, `user_id`, `activity_title`, `activity_type`, `organizer`, `activity_date`, `start_time`, `end_time`, `total_hours`, `description`, `created_at`, `updated_at`, `event_id`, `club_id`) VALUES
(1, 7, 'nnnkkk', 'Volunteering', 'nk', '2026-04-14', '09:00:00', '13:00:00', 4.00, 'papapapapappa papappa apapappapa papappapa', '2026-04-12 18:14:39', '2026-04-12 18:14:39', NULL, NULL),
(2, 7, 'badminton', 'Club Service', 'kkk', '2026-04-15', '12:00:00', '15:47:00', 3.78, 'cwqerg3qr', '2026-04-12 18:14:39', '2026-04-12 18:14:39', NULL, NULL),
(6, 7, 'new activity', 'Club Service', 'tom', '2026-04-29', '14:00:00', '18:00:00', 4.00, '', '2026-04-12 18:59:12', '2026-04-12 20:19:48', NULL, NULL),
(7, 8, 'badminton', 'Committee Work', 'badgear', '2026-04-17', '16:00:00', '22:00:00', 6.00, 'committee', '2026-04-12 19:39:23', '2026-04-12 19:41:27', NULL, NULL),
(8, 7, 'csadvsdv', 'Volunteering', 'dvszvsdv', '2026-04-24', '03:00:00', '15:00:00', 12.00, '', '2026-04-12 19:53:53', '2026-04-12 20:16:35', NULL, NULL),
(15, 4, 'Flutter Fundamentals Workshop', 'Committee Work', 'Google Developer Student Club UTAR', '2026-03-12', '09:00:00', '13:00:00', 4.00, 'Served as organizing committee member and helped manage registration and technical setup.', '2026-04-13 16:19:28', '2026-04-13 16:19:28', 14, 12),
(16, 4, 'Interfaculty Blood Donation Drive', 'Volunteering', 'Red Crescent Society', '2026-03-28', '08:30:00', '14:30:00', 6.00, 'Assisted in donor registration, queue coordination, and volunteer support throughout the event.', '2026-04-13 16:19:28', '2026-04-13 16:19:28', 15, 13),
(17, 4, 'Model ASEAN Debate 2026', 'Committee Work', 'Debate and Public Speaking Club', '2026-04-09', '10:00:00', '15:00:00', 5.00, 'Handled documentation, timekeeping, and contestant coordination during the debate competition.', '2026-04-13 16:19:28', '2026-04-13 16:19:28', 16, 14),
(18, 4, 'Basketball Club Weekly Training Support', 'Club Service', 'Basketball Club', '2026-04-20', '17:00:00', '19:30:00', 2.50, 'Helped prepare equipment, attendance, and court arrangement for the weekly training session.', '2026-04-13 16:19:28', '2026-04-13 16:19:28', NULL, 15),
(19, 4, 'Library Orientation Volunteer Team', 'University Service', 'UTAR Library', '2026-02-14', '09:00:00', '12:00:00', 3.00, 'Guided new students during orientation and assisted with registration and directional support.', '2026-04-13 16:19:28', '2026-04-13 16:19:28', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(1, 'deqerwip@gmail.com', '666b16524ab769c4a289e710549fc100522d7b7a8cc55ecec6c51a2b96e681e0cdba0b7b697214875ec2768288b2229c6c62', '2026-04-12 04:51:25'),
(2, 'deqerwip@gmail.com', '51170569fa3a053d77943e0131584e4b67074d068eccab6147d403f8806f31f149b64b097f3048fbe4b7bc2232a539f9a5a1', '2026-04-12 04:52:03'),
(3, 'deqerwip@gmail.com', 'cf11755e28069bee11fabd8f05d435fbb205e119943592e9f68b117fef3717538c8298871f2f1c8f3a7961558a5f5bc5b8e5', '2026-04-12 04:52:12'),
(4, 'deqerwip@gmail.com', '133a727bdfa6668c1fb3921ff70e4205df972fc952a779b9a2d56c38bfe338b9cf1007d48b3a28c6b9311b4ea98209ced137', '2026-04-12 05:08:12'),
(5, 'deqerwip@gmail.com', 'e26caa0dde93434264d6b982fb4d36b7bb76fadc3eb71314d842753d2d0951790b242b203dbc28eab246b07edac86de43f08', '2026-04-12 05:08:29'),
(7, 'gainsongains@gmail.com', '73c2620a4382134bd687b86397a8ff9df74c0356040ff798dda2c9501fa24f9e89ceb71e4312cbddb0586f34f18ea5fbf120', '2026-04-12 05:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `created_at`, `is_admin`) VALUES
(2, 'jxuan', 'jxuan04@icloud.com', '$2y$10$SLEtFD3IlpUinbuhbVPy3epiIEaZqb60hly.6wTP75wCz98Z/rmvm', '2026-04-13 13:24:53', 0),
(3, 'jheen', 'jheen04@gmail.com', '$2y$10$W8Espw9gLorXCj2b6fs9We45jkU5k2q6D6cETQa5vB54YiuZQ0fYa', '2026-04-07 15:40:41', 0),
(4, 'admin_jxuan', 'admin@icloud.com', '$2y$10$9hgsbQqTJiZzlfJhalsWiuw1ApvgBY5EhkSUVFxJbO3vaa3WhioVW', '2026-04-07 15:39:23', 1),
(5, 'acc test', 'acctest@gmail.com', '$2y$10$MSKHeN3glvX1io.R8JcXE.m1vENn1KoY1G091/hHAuOZYGAIr5kd2', '2026-04-07 15:38:47', 0),
(6, 'zt', 'zt@gmail.com', '$2y$10$b80wJBnhiVtPxLlfoojUJONqC0cHoGMM0z8igjTcgteh800KevL46', '2026-04-08 02:44:55', 0),
(7, 'tsen', 't@gmail.com', '$2y$10$uZY0du6vhC.fy6W7U.AL..gfBt.PIBbYD4hsSMIrL.fp7aw2Tr4Bu', '2026-04-08 12:50:44', 1),
(8, 'John Kaleeks', 'jleek@gmail.com', '$2y$10$ChM3UQipS92dD1MXfli9Z.3LoQE7vRO8Zv78Cb9D7n/Zlu4rhoXqS', '2026-04-10 14:12:34', 0),
(9, 'Meyers Armwhey', 'gainsongains@gmail.com', '6e6941c17eab8d6168da6c3ad66b0d1e', '2026-04-12 05:32:16', 0),
(10, 'Qaysi Rohns', 'deqerwip@gmail.com', '$2y$10$3lGcxfcix5.FVqCIb3ttWuYJrlsjCtUuZSEV.VsjD3Dz/1vIbCcei', '2026-04-12 04:51:09', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`achievement_id`),
  ADD KEY `fk_achievement_user` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`club_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `merits`
--
ALTER TABLE `merits`
  ADD PRIMARY KEY (`merit_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`,`club_id`),
  ADD KEY `fk_merit_club` (`club_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `achievements`
--
ALTER TABLE `achievements`
  MODIFY `achievement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `merits`
--
ALTER TABLE `merits`
  MODIFY `merit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `fk_achievement_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_achievement_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_event_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`club_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `merits`
--
ALTER TABLE `merits`
  ADD CONSTRAINT `fk_merit_club` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`club_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_merit_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `merits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
