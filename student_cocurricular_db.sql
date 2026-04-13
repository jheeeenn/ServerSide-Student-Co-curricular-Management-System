-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 12, 2026 at 11:58 AM
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`achievement_id`, `user_id`, `title`, `achievement_type`, `organizer`, `date_achieved`, `description`, `certificate_file`, `created_at`, `updated_at`) VALUES
(11, 6, 'Hackaton', 'Award', 'UTAR', '2026-04-01', 'Champion', '1775799956_2.jpg', '2026-04-10 05:45:56', '2026-04-10 05:45:56'),
(14, 6, 'App Development', 'Medal', 'UTAR', '2026-01-01', '2nd Place', '1775801224_1.png', '2026-04-10 06:07:04', '2026-04-10 06:08:16'),
(15, 6, 'Cybersecurity Tournament', 'Other', 'TARC', '2024-02-02', '10th Place', '', '2026-04-10 06:13:40', '2026-04-10 06:13:40');

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
(8, 9, 'Movie Club', 'Vice President', '2026-03-08', 'Where club members watch movies, then give their thoughts on it, while discussing and debating the movie\'s qualities with other members with the goal of determining the attributes that set a movie\'s watchability and reputation.');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `user_id`, `event_title`, `event_type`, `organizer`, `event_date`, `location`, `description`, `created_at`) VALUES
(1, 3, 'event 1', 'Competition', '1234', '2026-04-07', 'block n', '12312342131223123123123123123123213', '2026-04-07 12:21:52'),
(3, 3, '第一个', '', '1234asdavac', '2027-01-29', 'block k   hall 1', '12321 wewasd asd asd \r\nasdasdas\r\nasdas\r\nasdasd\r\nasd', '2026-04-07 13:27:21'),
(5, 3, 'event 3', 'Workshop', '123', '0132-03-12', '123', '123', '2026-04-07 14:03:21'),
(6, 4, 'admin', 'Other', 'admin', '2026-04-07', 'admin', 'admin teest', '2026-04-07 15:31:49'),
(10, 8, 'Men\'s 300M National Tournament Runner-Up', 'Competition', 'Omelympics', '2021-12-22', '4245, James F. Burlington Street, 10780, Gramma\'s Pavements, Sydney', 'Runner-up to men\'s 300M national tournament.', '2026-04-10 14:14:59');

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
(2, 'jxuan', 'jxuan04@icloud.com', '$2y$10$dJ9tXGRdWRArfBuGkkN4Ne9LJmkhx.5K0iYHqROn/ThR0iZzEo.DC', '2026-04-07 15:40:57', 0),
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
  ADD KEY `fk_achievement_user` (`user_id`);

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
  ADD PRIMARY KEY (`event_id`);

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
  MODIFY `achievement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `fk_achievement_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `clubs`
--
ALTER TABLE `clubs`
  ADD CONSTRAINT `clubs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
