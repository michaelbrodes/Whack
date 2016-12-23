-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 22, 2016 at 09:02 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `whack`
--

-- --------------------------------------------------------

--
-- Table structure for table `Account`
--

CREATE TABLE `Account` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL,
  `nick` varchar(30) DEFAULT NULL,
  `private_key` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Audio`
--

CREATE TABLE `Audio` (
  `id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `content_type` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Audio_Phrase`
--

CREATE TABLE `Audio_Phrase` (
  `Phrase_id` int(11) NOT NULL,
  `Audio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Image`
--

CREATE TABLE `Image` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `content_type` varchar(50) DEFAULT 'image/jpeg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Image_Phrase`
--

CREATE TABLE `Image_Phrase` (
  `Image_id` int(11) NOT NULL,
  `Phrase_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Phrase`
--

CREATE TABLE `Phrase` (
  `id` int(11) NOT NULL,
  `author` varchar(30) DEFAULT NULL,
  `statement` text NOT NULL,
  `char_count` int(11) NOT NULL,
  `origin` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Phrase`
--

INSERT INTO `Phrase` (`id`, `author`, `statement`, `char_count`, `origin`) VALUES
(1, 'André 3000', 'If what they say is "Nothing is forever", then what makes, then what makes, then what makes, then what makes, what makes, what makes, love the exception?', 153, 'Hey Ya!'),
(2, 'Joe Walsh', 'I can\'t complain, but sometimes I still do.', 43, 'Life\'s Been Good'),
(3, 'Atmosphere', 'You look like what I feel like when I\'m with you', 48, 'Shoulda Known'),
(4, 'Kanye West', '"Oh my god, is that a black card?" I turned around and replied, "Why yes but I prefer the term African American Express"', 120, 'Roll Call'),
(5, 'Kanye West', 'Who killing them in the UK? Everybody going to say "You K!"', 59, 'American Boy'),
(6, 'Phife Dawg', 'A man of the fame not a man of the people? Believe that if you wanna, but I tell you this much, riding on the train with no dough sucks.', 136, 'Buggin\' Out'),
(7, 'Q-tip', 'Shorty let me tell you about my only vice, It has to do with lots of loving and it ain\'t nothing nice', 101, 'Electric Relaxation');

-- --------------------------------------------------------

--
-- Table structure for table `Score`
--

CREATE TABLE `Score` (
  `Phrase_id` int(11) NOT NULL,
  `Account_id` int(11) NOT NULL,
  `wpm` decimal(10,5) NOT NULL,
  `accuracy` decimal(10,5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Token`
--

CREATE TABLE `Token` (
  `token` varchar(256) NOT NULL,
  `Account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Account`
--
ALTER TABLE `Account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Audio`
--
ALTER TABLE `Audio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Audio_Phrase`
--
ALTER TABLE `Audio_Phrase`
  ADD PRIMARY KEY (`Phrase_id`,`Audio_id`),
  ADD KEY `Audio_id` (`Audio_id`);

--
-- Indexes for table `Image`
--
ALTER TABLE `Image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Image_Phrase`
--
ALTER TABLE `Image_Phrase`
  ADD PRIMARY KEY (`Image_id`,`Phrase_id`),
  ADD KEY `Phrase_id` (`Phrase_id`);

--
-- Indexes for table `Phrase`
--
ALTER TABLE `Phrase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Score`
--
ALTER TABLE `Score`
  ADD PRIMARY KEY (`Phrase_id`,`Account_id`),
  ADD KEY `Account_id` (`Account_id`);

--
-- Indexes for table `Token`
--
ALTER TABLE `Token`
  ADD PRIMARY KEY (`Account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Account`
--
ALTER TABLE `Account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT for table `Audio`
--
ALTER TABLE `Audio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Image`
--
ALTER TABLE `Image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Phrase`
--
ALTER TABLE `Phrase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Audio_Phrase`
--
ALTER TABLE `Audio_Phrase`
  ADD CONSTRAINT `Audio_Phrase_ibfk_1` FOREIGN KEY (`Phrase_id`) REFERENCES `Phrase` (`id`),
  ADD CONSTRAINT `Audio_Phrase_ibfk_2` FOREIGN KEY (`Audio_id`) REFERENCES `Audio` (`id`);

--
-- Constraints for table `Image_Phrase`
--
ALTER TABLE `Image_Phrase`
  ADD CONSTRAINT `Image_Phrase_ibfk_1` FOREIGN KEY (`Image_id`) REFERENCES `Image` (`id`),
  ADD CONSTRAINT `Image_Phrase_ibfk_2` FOREIGN KEY (`Phrase_id`) REFERENCES `Phrase` (`id`);

--
-- Constraints for table `Score`
--
ALTER TABLE `Score`
  ADD CONSTRAINT `Score_ibfk_1` FOREIGN KEY (`Phrase_id`) REFERENCES `Phrase` (`id`),
  ADD CONSTRAINT `Score_ibfk_2` FOREIGN KEY (`Account_id`) REFERENCES `Account` (`id`);

--
-- Constraints for table `Token`
--
ALTER TABLE `Token`
  ADD CONSTRAINT `token_Account_id_fk` FOREIGN KEY (`Account_id`) REFERENCES `Account` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
