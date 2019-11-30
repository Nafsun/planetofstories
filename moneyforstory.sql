-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 14, 2019 at 12:52 AM
-- Server version: 5.7.17-log
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moneyforstory`
--

-- --------------------------------------------------------

--
-- Table structure for table `nafsun_317596436`
--

CREATE TABLE `nafsun_317596436` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `comment` text,
  `ip` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nafsun_317596436`
--

INSERT INTO `nafsun_317596436` (`id`, `name`, `comment`, `ip`) VALUES
(1, 'muhammad', 'what is this joke', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `nafsun_chat`
--

CREATE TABLE `nafsun_chat` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `message` text,
  `dtofchat` varchar(100) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nafsun_chat`
--

INSERT INTO `nafsun_chat` (`id`, `name`, `message`, `dtofchat`, `ip`) VALUES
(1, 'nafsun', 'how are you today', '09/08/19 09:47:49 am', '::1'),
(2, 'muhammad', 'I am fine thank you', '09/08/19 09:48:14 am', '::1'),
(3, 'nafsun', 'thank God bro', '09/08/19 09:48:40 am', '::1'),
(4, 'nafsun', 'Good guys', '09/08/19 10:15:56 am', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `nafsun_dislike_445220948`
--

CREATE TABLE `nafsun_dislike_445220948` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nafsun_dislike_445220948`
--

INSERT INTO `nafsun_dislike_445220948` (`id`, `name`) VALUES
(1, 'nafsun');

-- --------------------------------------------------------

--
-- Table structure for table `nafsun_like_760375977`
--

CREATE TABLE `nafsun_like_760375977` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nafsun_like_760375977`
--

INSERT INTO `nafsun_like_760375977` (`id`, `name`) VALUES
(1, 'nafsun');

-- --------------------------------------------------------

--
-- Table structure for table `nafsun_post`
--

CREATE TABLE `nafsun_post` (
  `id` int(11) NOT NULL,
  `post` text,
  `rvforpost` varchar(100) DEFAULT NULL,
  `dtofpost` varchar(100) DEFAULT NULL,
  `storytype` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nafsun_post`
--

INSERT INTO `nafsun_post` (`id`, `post`, `rvforpost`, `dtofpost`, `storytype`) VALUES
(1, 'I hate java programming language because it is easy', 'nafsun_317596436', '09/08/19 09:43:05 am', 'sad'),
(2, 'Jaskido is very funny', 'nafsun_508544922', '09/08/19 10:12:09 am', 'joke');

-- --------------------------------------------------------

--
-- Table structure for table `nafsun_subscribe`
--

CREATE TABLE `nafsun_subscribe` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `post` text,
  `rvforpost` varchar(100) DEFAULT NULL,
  `likepost` varchar(100) NOT NULL DEFAULT '0',
  `dislikepost` varchar(100) NOT NULL DEFAULT '0',
  `dtofpost` varchar(100) DEFAULT NULL,
  `storytype` varchar(100) DEFAULT NULL,
  `ipaddress` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `username`, `post`, `rvforpost`, `likepost`, `dislikepost`, `dtofpost`, `storytype`, `ipaddress`) VALUES
(1, 'nafsun', 'I hate java programming language because it is simple', 'nafsun_317596436', 'nafsun_like_760375977', 'nafsun_dislike_445220948', '09/08/19 09:43:05 am', 'sad', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `usersinfo`
--

CREATE TABLE `usersinfo` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phonenumber` varchar(100) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `age` varchar(3) DEFAULT NULL,
  `hobby` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `localgovt` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `bio` text,
  `yearofregistration` varchar(100) DEFAULT NULL,
  `dateandtimeofregistration` varchar(100) NOT NULL,
  `yearlypostcount` varchar(100) DEFAULT NULL,
  `monthlypostcount` varchar(100) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `activation` int(11) NOT NULL DEFAULT '0',
  `hash` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usersinfo`
--

INSERT INTO `usersinfo` (`id`, `fullname`, `email`, `username`, `phonenumber`, `gender`, `age`, `hobby`, `country`, `state`, `localgovt`, `city`, `bio`, `yearofregistration`, `dateandtimeofregistration`, `yearlypostcount`, `monthlypostcount`, `ipaddress`, `activation`, `hash`) VALUES
(1, 'Muhammad Aliyu', 'nafsun11@gmail.com', 'nafsun', '07088172088', 'male', '20', 'Programming', 'Nigeria', 'Kano', 'Gwale', 'Kano', 'I am a computer genius and software Einstein', '19', '09/08/19 09:39:42 am', '1', '1', '::1', 1, 'aab3238922bcc25a6f606eb525ffdc56');

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `verification`
--

INSERT INTO `verification` (`id`, `username`, `password`) VALUES
(1, 'nafsun', '2600');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nafsun_317596436`
--
ALTER TABLE `nafsun_317596436`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nafsun_chat`
--
ALTER TABLE `nafsun_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nafsun_dislike_445220948`
--
ALTER TABLE `nafsun_dislike_445220948`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nafsun_like_760375977`
--
ALTER TABLE `nafsun_like_760375977`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nafsun_post`
--
ALTER TABLE `nafsun_post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nafsun_subscribe`
--
ALTER TABLE `nafsun_subscribe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usersinfo`
--
ALTER TABLE `usersinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nafsun_317596436`
--
ALTER TABLE `nafsun_317596436`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nafsun_chat`
--
ALTER TABLE `nafsun_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `nafsun_dislike_445220948`
--
ALTER TABLE `nafsun_dislike_445220948`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nafsun_like_760375977`
--
ALTER TABLE `nafsun_like_760375977`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nafsun_post`
--
ALTER TABLE `nafsun_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `nafsun_subscribe`
--
ALTER TABLE `nafsun_subscribe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `usersinfo`
--
ALTER TABLE `usersinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
