-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 22, 2018 at 04:03 PM
-- Server version: 5.7.21
-- PHP Version: 7.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shuttle`
--

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

DROP TABLE IF EXISTS `car`;
CREATE TABLE IF NOT EXISTS `car` (
  `CarID` int(11) NOT NULL AUTO_INCREMENT,
  `License` varchar(255) NOT NULL,
  `Seats` int(11) NOT NULL,
  PRIMARY KEY (`CarID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `car`
--

INSERT INTO `car` (`CarID`, `License`, `Seats`) VALUES
(1, '993-RDJ', 5),
(2, '359-UTN', 12),
(3, '771-ZVX', 8);

-- --------------------------------------------------------

--
-- Table structure for table `place`
--

DROP TABLE IF EXISTS `place`;
CREATE TABLE IF NOT EXISTS `place` (
  `PlaceID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Lon` float NOT NULL,
  `Lat` float NOT NULL,
  PRIMARY KEY (`PlaceID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `place`
--

INSERT INTO `place` (`PlaceID`, `Name`, `Lon`, `Lat`) VALUES
(2, 'North Side Sheboygan Walmart', 43.7926, -87.7673),
(1, 'Lakeland Unversity', 43.8416, -87.8834),
(3, 'Blue Harbor', 43.7462, -87.7066),
(4, 'Osthoff Resort', 43.83, -88.0147);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
CREATE TABLE IF NOT EXISTS `request` (
  `RequestID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `LocationTo` varchar(255) CHARACTER SET utf8 NOT NULL,
  `LocationFrom` varchar(255) CHARACTER SET utf8 NOT NULL,
  `TimePickUp` timestamp NOT NULL,
  `TimePickUpNew` timestamp NULL DEFAULT NULL,
  `DriverID` int(10) UNSIGNED DEFAULT NULL,
  `CarID` int(11) DEFAULT NULL,
  PRIMARY KEY (`RequestID`),
  KEY `DriverIDNumber` (`DriverID`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requeststudent`
--

DROP TABLE IF EXISTS `requeststudent`;
CREATE TABLE IF NOT EXISTS `requeststudent` (
  `RequestID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `Went` int(11) DEFAULT '0',
  UNIQUE KEY `requestID` (`RequestID`,`StudentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reset`
--

DROP TABLE IF EXISTS `reset`;
CREATE TABLE IF NOT EXISTS `reset` (
  `ResetID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `StudentID` int(10) UNSIGNED NOT NULL,
  `Time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ResetID`),
  KEY `StudentID` (`StudentID`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `IDNumber` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `Password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `StudentID` int(10) UNSIGNED NOT NULL,
  `Phone` varchar(255) NOT NULL,
  `Driver` tinyint(1) NOT NULL DEFAULT '0',
  `Notify_new` tinyint(1) NOT NULL DEFAULT '1',
  `Notify_empty` int(11) NOT NULL DEFAULT '1',
  `Notify_time` tinyint(1) NOT NULL DEFAULT '1',
  `Notify_time_amount` int(11) NOT NULL DEFAULT '30',
  PRIMARY KEY (`IDNumber`),
  KEY `StudentID` (`StudentID`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `studentidtable`
--

DROP TABLE IF EXISTS `studentidtable`;
CREATE TABLE IF NOT EXISTS `studentidtable` (
  `StudentID` int(10) UNSIGNED NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Driver` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`StudentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `studentidtable`
--

INSERT INTO `studentidtable` (`StudentID`, `Name`, `Email`, `Driver`) VALUES
(220193, 'Daniel Koeber', 'koerberd@lakeland.edu', 0),
(224796, 'Nicholas Koerber', 'koerbern@lakeland.edu', 0),
(123, 'Bob Smith', 'smithb@lakeland.edu', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
