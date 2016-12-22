-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 16, 2016 at 01:30 AM
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
-- Table structure for table `Image`
--

CREATE TABLE `Image` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `content_type` varchar(50) DEFAULT 'image/jpeg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Image`
--

INSERT INTO `Image` (`id`, `image_path`, `content_type`) VALUES
(1, '/assets/images/Example.jpg', 'image/jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `Image_Phrase`
--

CREATE TABLE `Image_Phrase` (
  `Image_id` int(11) NOT NULL,
  `Phrase_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Image_Phrase`
--

INSERT INTO `Image_Phrase` (`Image_id`, `Phrase_id`) VALUES
(1, 6);

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

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for dumped tables
--

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
-- Constraints for table `Image_Phrase`
--
ALTER TABLE `Image_Phrase`
  ADD CONSTRAINT `Image_Phrase_ibfk_1` FOREIGN KEY (`Image_id`) REFERENCES `Image` (`id`),
  ADD CONSTRAINT `Image_Phrase_ibfk_2` FOREIGN KEY (`Phrase_id`) REFERENCES `Phrase` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
