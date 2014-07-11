
CREATE DATABASE authentication;

GRANT ALL PRIVILEGES ON authentication.* TO 'username'@'localhost';

USE authentication;

-- MySQL dump 10.13  Distrib 5.1.71, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: authentication
-- ------------------------------------------------------
-- Server version	5.1.71-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accountUsernames`
--

DROP TABLE IF EXISTS `accountUsernames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountUsernames` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `username` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=126152 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alumni`
--

DROP TABLE IF EXISTS `alumni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumni` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(30) DEFAULT NULL,
  `lastname` varchar(30) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `birthday` varchar(2) DEFAULT NULL,
  `birthmonth` varchar(2) DEFAULT NULL,
  `birthyear` varchar(4) DEFAULT NULL,
  `registered` tinyint(3) unsigned DEFAULT NULL,
  `registerDate` int(10) unsigned NOT NULL,
  `currentMember` tinyint(1) unsigned DEFAULT NULL,
  `addedOn` int(11) DEFAULT NULL,
  `removedOn` int(11) DEFAULT NULL,
  `updatedOn` int(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=88228 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libraryAddedPatrons`
--

DROP TABLE IF EXISTS `libraryAddedPatrons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libraryAddedPatrons` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(20) DEFAULT NULL,
  `lastname` varchar(25) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `birthdate` bigint(20) DEFAULT NULL,
  `proxy` tinyint(4) DEFAULT NULL,
  `resident` tinyint(4) DEFAULT NULL,
  `expireDate` int(10) unsigned DEFAULT NULL,
  `neverExpire` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `system` tinyint(4) unsigned DEFAULT NULL,
  `emeritus` tinyint(4) DEFAULT NULL,
  `category` int(11) unsigned DEFAULT NULL,
  `middlename` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `createdBy` varchar(25) DEFAULT NULL,
  `createDate` varchar(11) DEFAULT NULL,
  `approvedBy` varchar(25) DEFAULT NULL,
  `approveDate` varchar(11) DEFAULT NULL,
  `startDate` int(11) unsigned DEFAULT NULL,
  `proxyUser` tinyint(1) unsigned DEFAULT NULL,
  `proxyOwner` varchar(9) DEFAULT NULL,
  `modifyDate` varchar(11) DEFAULT NULL,
  `modifyBy` varchar(25) DEFAULT NULL,
  `username` varchar(25) DEFAULT NULL,
  `street1` varchar(50) DEFAULT NULL,
  `street2` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libraryAddedPatrons_approverEmails`
--

DROP TABLE IF EXISTS `libraryAddedPatrons_approverEmails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libraryAddedPatrons_approverEmails` (
  `ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libraryAddedPatrons_categories`
--

DROP TABLE IF EXISTS `libraryAddedPatrons_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libraryAddedPatrons_categories` (
  `ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libraryAddedPatrons_category`
--

DROP TABLE IF EXISTS `libraryAddedPatrons_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libraryAddedPatrons_category` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libraryAddedPatrons_emailMsgs`
--

DROP TABLE IF EXISTS `libraryAddedPatrons_emailMsgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libraryAddedPatrons_emailMsgs` (
  `ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `patronExpire` text,
  `approveNotify` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `master`
--

DROP TABLE IF EXISTS `master`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(20) DEFAULT NULL,
  `lastname` varchar(25) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `birthdate` int(10) unsigned DEFAULT NULL,
  `proxy` tinyint(4) DEFAULT NULL,
  `resident` tinyint(4) DEFAULT NULL,
  `expireDate` int(10) unsigned DEFAULT NULL,
  `neverExpire` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `system` tinyint(4) unsigned DEFAULT NULL,
  `emeritus` tinyint(4) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `fromFile` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=56441 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tempAccounts`
--

DROP TABLE IF EXISTS `tempAccounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tempAccounts` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(15) DEFAULT NULL,
  `password` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-07-11  7:28:23
