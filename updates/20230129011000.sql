CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `messages` varchar(255) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `dungeons` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `monster_id` varchar(20) NOT NULL,
  `difficult` varchar(2) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `dungeons` (`id`, `name`, `monster_id`, `difficult`, `description`) VALUES
(1, 'Rat Cave', '1', '1', 'A rather small rat cave with healthy amount of rats.'),
(2, 'Rotworm Cave', '2', '1', 'asd'),
(3, 'Dragon Lair', '3', '5', 'asd'),
(4, 'Hatchling Cave', '4', '5', 'asd');

CREATE TABLE `exp_table` (
  `id` int(11) NOT NULL,
  `level` int(100) NOT NULL,
  `experience` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `hunting` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `dungeon_id` int(10) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `amount` int(10) NOT NULL,
  `worth` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_id` int(5) NOT NULL,
  `name` varchar(20) NOT NULL,
  `grade` int(5) NOT NULL,
  `is_weapon` int(10) NOT NULL DEFAULT '0',
  `is_armor` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `items` (`id`, `item_id`, `name`, `grade`, `is_weapon`, `is_armor`) VALUES
(1, 1, 'Gold Coins', 1, 0, 0),
(2, 2, 'Cheese', 1, 0, 0),
(3, 3, 'Short Sword', 1, 1, 0);

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `monster` (
  `id` int(11) NOT NULL,
  `monster_id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `health` int(10) NOT NULL,
  `experience` int(10) NOT NULL,
  `attack` int(5) NOT NULL,
  `defense` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `monster` (`id`, `monster_id`, `name`, `health`, `experience`, `attack`, `defense`) VALUES
(1, 1, 'Rat', 20, 5, 5, 1),
(2, 2, 'Rotworm', 40, 25, 8, 2),
(3, 3, 'Dragon', 1000, 700, 30, 20),
(4, 4, 'Dragon Hatchling', 450, 200, 15, 10);

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

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `tid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `timetable` (`id`, `name`, `tid`) VALUES
(1, 'stamina', '2023-01-29 00:01:23');

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `anv` varchar(20) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `last_ip` varchar(20) NOT NULL DEFAULT '0',
  `banned` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dungeons`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `exp_table`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hunting`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `monster`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `players`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `dungeons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `exp_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `hunting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `monster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

CREATE TABLE `version`(`current` VARCHAR(255) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `version` ADD UNIQUE (`current`);