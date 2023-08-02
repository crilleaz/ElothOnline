-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 29, 2023 at 01:08 AM
-- Server version: 5.7.41-0ubuntu0.18.04.1
-- PHP Version: 7.2.34-37+ubuntu18.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eloth`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `messages` varchar(255) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dungeons`
--

CREATE TABLE `dungeons` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `monster_id` varchar(20) NOT NULL,
  `difficult` varchar(2) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dungeons`
--

INSERT INTO `dungeons` (`id`, `name`, `monster_id`, `difficult`, `description`) VALUES
(1, 'Rat Cave', '1', '1', 'A rather small rat cave with healthy amount of rats.'),
(2, 'Rotworm Cave', '2', '1', 'asd'),
(3, 'Dragon Lair', '3', '5', 'asd'),
(4, 'Hatchling Cave', '4', '5', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `exp_table`
--

CREATE TABLE `exp_table` (
  `id` int(11) NOT NULL,
  `level` int(100) NOT NULL,
  `experience` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `exp_table`
--

INSERT INTO `exp_table` (`id`, `level`, `experience`) VALUES
(1, 1, 0),
(2, 2, 100),
(3, 3, 200),
(4, 4, 400),
(5, 5, 800),
(6, 6, 1500),
(7, 7, 2600),
(8, 8, 4200),
(9, 9, 6400),
(10, 10, 9300),
(11, 11, 13000),
(12, 12, 17600),
(13, 13, 23200),
(14, 14, 29900),
(15, 15, 37800),
(16, 16, 47000),
(17, 17, 57600),
(18, 18, 69700),
(19, 19, 83400),
(20, 20, 98800);

-- --------------------------------------------------------

--
-- Table structure for table `hunting`
--

CREATE TABLE `hunting` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `dungeon_id` int(10) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hunting`
--

-- this entry can not exist since there is no player with such name. Likely forgot to dump that one
--  INSERT INTO `hunting` (`id`, `username`, `dungeon_id`, `tid`) VALUES (86, 'crilleaz', 1, '2023-01-29 00:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `amount` int(10) NOT NULL,
  `worth` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_id` int(5) NOT NULL,
  `name` varchar(20) NOT NULL,
  `grade` int(5) NOT NULL,
  `is_weapon` int(10) NOT NULL DEFAULT '0',
  `is_armor` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_id`, `name`, `grade`, `is_weapon`, `is_armor`) VALUES
(1, 1, 'Gold Coins', 1, 0, 0),
(2, 2, 'Cheese', 1, 0, 0),
(3, 3, 'Short Sword', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`id`, `username`, `message`, `tid`) VALUES
(1, 'crilleaz', '[Dungeon] You gained 800 experience points.', '2023-01-29 00:01:23'),
(2, 'crilleaz', '[Dungeon] You looted a dead rat, found 2 gold.', '2023-01-29 00:01:23'),
(3, 'crilleaz', '[Dungeon] You gained 800 experience points.', '2023-01-29 00:01:34'),
(4, 'crilleaz', '[Dungeon] You looted a dead rat, found 7 gold.', '2023-01-29 00:01:34'),
(5, 'crilleaz', '[Dungeon] You looted a dead rat, found 1 cheese.', '2023-01-29 00:01:34');

-- --------------------------------------------------------

--
-- Table structure for table `monster`
--

CREATE TABLE `monster` (
  `id` int(11) NOT NULL,
  `monster_id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `health` int(10) NOT NULL,
  `experience` int(10) NOT NULL,
  `attack` int(5) NOT NULL,
  `defense` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `monster`
--

INSERT INTO `monster` (`id`, `monster_id`, `name`, `health`, `experience`, `attack`, `defense`) VALUES
(1, 1, 'Rat', 20, 5, 5, 1),
(2, 2, 'Rotworm', 40, 25, 8, 2),
(3, 3, 'Dragon', 1000, 700, 30, 20),
(4, 4, 'Dragon Hatchling', 450, 200, 15, 10);

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `level` int(255) NOT NULL DEFAULT '1',
  `experience` varchar(255) NOT NULL,
  `stamina` varchar(5) NOT NULL DEFAULT '100',
  `health` int(10) NOT NULL DEFAULT '100',
  `health_max` varchar(255) NOT NULL DEFAULT '100',
  `magic` int(10) NOT NULL DEFAULT '0',
  `strength` int(10) NOT NULL DEFAULT '10',
  `defense` int(10) NOT NULL DEFAULT '10',
  `woodcutting` int(10) NOT NULL DEFAULT '0',
  `mining` int(10) NOT NULL DEFAULT '0',
  `gathering` int(10) NOT NULL DEFAULT '0',
  `harvesting` int(10) NOT NULL DEFAULT '0',
  `blacksmith` int(10) NOT NULL DEFAULT '0',
  `herbalism` int(10) NOT NULL DEFAULT '0',
  `gold` varchar(255) NOT NULL DEFAULT '10',
  `crystals` varchar(255) NOT NULL DEFAULT '0',
  `in_combat` int(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `name`, `tid`) VALUES
(1, 'stamina', '2023-01-29 00:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `anv` varchar(20) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `last_ip` varchar(20) NOT NULL DEFAULT '0',
  `banned` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dungeons`
--
ALTER TABLE `dungeons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exp_table`
--
ALTER TABLE `exp_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hunting`
--
ALTER TABLE `hunting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monster`
--
ALTER TABLE `monster`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `dungeons`
--
ALTER TABLE `dungeons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `exp_table`
--
ALTER TABLE `exp_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `hunting`
--
ALTER TABLE `hunting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;
--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `monster`
--
ALTER TABLE `monster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
