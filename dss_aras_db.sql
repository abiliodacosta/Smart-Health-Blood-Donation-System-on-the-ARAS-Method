-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 29, 2026 at 08:29 AM
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
-- Database: `dss_aras_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatives`
--

CREATE TABLE `alternatives` (
  `id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sexu` varchar(20) DEFAULT NULL,
  `tinan` int(11) DEFAULT NULL,
  `hela_fatin` text DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipu_ran` varchar(5) DEFAULT NULL,
  `is_ideal` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alternatives`
--

INSERT INTO `alternatives` (`id`, `code`, `name`, `sexu`, `tinan`, `hela_fatin`, `telefone`, `tipu_ran`, `is_ideal`) VALUES
(1, 'A0', 'system', NULL, NULL, NULL, NULL, NULL, 1),
(13, 'A1', 'Abilio da Costa', 'Mane', 24, 'Matadoru', '75767879', 'B', 0),
(14, 'A2', 'Lucas G. do Nasimentu', 'Mane', 25, 'Bedois', '75767879', 'A', 0),
(15, 'A3', 'Pedro de Jesus dos Reis', 'Mane', 50, 'Hidi Laran', '75767879', 'AB', 0),
(16, 'A4', 'Nelson Amaral', 'Mane', 27, 'Bedois', '75767879', 'O', 0),
(17, 'A5', 'Mariano Tolo Elo', 'Mane', 20, 'Marconi', '767676789', 'B', 0),
(18, 'A6', 'Joao da Costa', 'Mane', 22, 'Tasi Tolu ', '74678909', 'B', 0);

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL,
  `name` varchar(100) NOT NULL,
  `weight` float NOT NULL,
  `type` enum('benefit','cost') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `code`, `name`, `weight`, `type`) VALUES
(11, 'K1', 'Idade 17 to 60', 0.2, 'benefit'),
(13, 'K3', 'Deskansa (7 - 8 Jam)', 0.2, 'benefit'),
(14, 'K4', 'Moras Kronik', 0.2, 'cost'),
(15, 'K5', 'Teste Ran', 0.2, 'benefit'),
(16, 'K2', 'Todan F45 M50', 0.2, 'benefit');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `alternative_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `value` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `alternative_id`, `criteria_id`, `value`) VALUES
(61, 14, 11, 80),
(63, 14, 13, 90),
(64, 14, 14, 30),
(65, 14, 15, 60),
(66, 13, 11, 90),
(68, 13, 13, 90),
(69, 13, 14, 20),
(70, 13, 15, 100),
(71, 15, 11, 60),
(73, 15, 13, 70),
(74, 15, 14, 50),
(75, 15, 15, 50),
(81, 16, 11, 50),
(83, 16, 13, 50),
(84, 16, 14, 40),
(85, 16, 15, 40),
(86, 17, 11, 60),
(88, 17, 13, 40),
(89, 17, 14, 40),
(90, 17, 15, 70),
(96, 1, 11, 100),
(98, 1, 13, 100),
(99, 1, 14, 10),
(100, 1, 15, 100),
(101, 1, 16, 100),
(102, 13, 16, 90),
(103, 14, 16, 80),
(104, 15, 16, 80),
(105, 16, 16, 50),
(106, 18, 11, 60),
(107, 18, 13, 50),
(108, 18, 14, 50),
(109, 18, 15, 10),
(110, 18, 16, 10),
(111, 17, 16, 60);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(20) DEFAULT 'Admin',
  `is_active` tinyint(1) DEFAULT 1,
  `foto` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `sexu` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `is_active`, `foto`, `full_name`, `sexu`) VALUES
(2, 'costavidigal', '$2y$10$OQIUZw98LtJoAILcQrvrWu06JDQCtsc2OpYifsnL2EaVCfNTmHRZi', 'Admin', 1, '1778573271_costavidigal.png', 'Madalena dos Santos Vidigal', 'F'),
(3, 'Abiliodacosta', '$2y$10$hH8yBaKncPRtSm80xVh.LOOcC/udWrH.GjYRbI2uuR4ILg.BqbGZm', 'Administrator', 1, '1778742481_Abiliodacosta.JPG', 'Abilio da Costa', 'L'),
(5, 'admin', '$2y$10$ntRzqUuzQNRHG46HUKdSJOMb1NZ9CqaOPubA5vPQWD1/YAGFaeyYq', 'Admin', 0, NULL, 'Administrator', NULL),
(6, 'Lucas', '$2y$10$Vcuf4k36URNC6VmQQEJ8VehTpoChcI5IROlMn2iss2vwmjD98P07i', 'Admin', 1, '1778823254_Lucas.JPG', 'Lucas G. do Nasimento', 'L'),
(7, 'Pedro', '$2y$10$P2JgOtRGvcQDHo/XkzcK7ODEXyTww0Mqh7liE4P2dRNpg9U2dt/EO', 'Admin', 1, '1778823340_Pedro.JPG', 'Pedro de Jesu Pereira', 'L');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alternative_id` (`alternative_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatives`
--
ALTER TABLE `alternatives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
