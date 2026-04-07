-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2026 at 05:48 PM
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
(6, 4, 'admin', 'Other', 'admin', '2026-04-07', 'admin', 'admin teest', '2026-04-07 15:31:49');

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
(5, 'acc test', 'acctest@gmail.com', '$2y$10$MSKHeN3glvX1io.R8JcXE.m1vENn1KoY1G091/hHAuOZYGAIr5kd2', '2026-04-07 15:38:47', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

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
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
