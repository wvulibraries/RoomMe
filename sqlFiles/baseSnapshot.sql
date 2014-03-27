-- MySQL dump 10.13  Distrib 5.1.71, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: roomReservations
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
-- Table structure for table `building`
--

DROP TABLE IF EXISTS `building`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `building` (
  `ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fromEmail` varchar(50) DEFAULT NULL,
  `url` varchar(75) DEFAULT NULL,
  `maxHoursAllowed` tinyint(4) DEFAULT '0',
  `period` smallint(6) NOT NULL DEFAULT '0',
  `bookingsAllowedInPeriod` tinyint(4) NOT NULL DEFAULT '0',
  `fineAmount` decimal(10,0) NOT NULL DEFAULT '0',
  `hoursRSS` varchar(200) DEFAULT NULL,
  `imageURL` varchar(200) DEFAULT NULL,
  `fineLookupURL` varchar(200) DEFAULT NULL,
  `roomListDisplay` varchar(50) DEFAULT NULL,
  `roomSortOrder` varchar(15) NOT NULL DEFAULT 'name',
  `policyURL` varchar(200) DEFAULT NULL,
  `hoursURL` varchar(200) DEFAULT NULL,
  `externalURL` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `building`
--

LOCK TABLES `building` WRITE;
/*!40000 ALTER TABLE `building` DISABLE KEYS */;
/*!40000 ALTER TABLE `building` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailMessages`
--

DROP TABLE IF EXISTS `emailMessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailMessages` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailMessages`
--

LOCK TABLES `emailMessages` WRITE;
/*!40000 ALTER TABLE `emailMessages` DISABLE KEYS */;
/*!40000 ALTER TABLE `emailMessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipement`
--

DROP TABLE IF EXISTS `equipement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipement` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `url` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipement`
--

LOCK TABLES `equipement` WRITE;
/*!40000 ALTER TABLE `equipement` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipementTypes`
--

DROP TABLE IF EXISTS `equipementTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipementTypes` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipementTypes`
--

LOCK TABLES `equipementTypes` WRITE;
/*!40000 ALTER TABLE `equipementTypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipementTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pageContent`
--

DROP TABLE IF EXISTS `pageContent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pageContent` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `snippetName` varchar(50) DEFAULT NULL,
  `content` blob,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pageContent`
--

LOCK TABLES `pageContent` WRITE;
/*!40000 ALTER TABLE `pageContent` DISABLE KEYS */;
/*!40000 ALTER TABLE `pageContent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policies` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `hoursAllowed` tinyint(4) DEFAULT NULL,
  `period` smallint(6) DEFAULT NULL,
  `allowWithFines` tinyint(4) NOT NULL,
  `fineAmount` decimal(10,0) DEFAULT NULL,
  `publicScheduling` tinyint(4) DEFAULT NULL,
  `publicViewing` tinyint(4) DEFAULT NULL,
  `reservationIncrements` tinyint(4) DEFAULT NULL,
  `bookingsAllowedInPeriod` smallint(6) DEFAULT NULL,
  `futureScheduleLength` smallint(6) DEFAULT NULL,
  `url` varchar(75) DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `maxLoanLength` tinyint(4) DEFAULT NULL,
  `sameDayReservations` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

LOCK TABLES `policies` WRITE;
/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `createdOn` int(10) unsigned DEFAULT NULL,
  `createdBy` varchar(30) DEFAULT NULL,
  `createdVia` varchar(20) DEFAULT NULL,
  `roomID` smallint(6) DEFAULT NULL,
  `startTime` int(10) unsigned DEFAULT NULL,
  `endTime` int(10) unsigned DEFAULT NULL,
  `modifiedOn` int(11) DEFAULT NULL,
  `modifiedBy` varchar(30) DEFAULT NULL,
  `username` varchar(25) DEFAULT NULL,
  `initials` varchar(10) DEFAULT NULL,
  `groupname` varchar(30) DEFAULT NULL,
  `comments` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=51992 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultMessages`
--

DROP TABLE IF EXISTS `resultMessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultMessages` (
  `name` varchar(50) NOT NULL,
  `value` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultMessages`
--

LOCK TABLES `resultMessages` WRITE;
/*!40000 ALTER TABLE `resultMessages` DISABLE KEYS */;
/*!40000 ALTER TABLE `resultMessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roomTemplatePolicies`
--

DROP TABLE IF EXISTS `roomTemplatePolicies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roomTemplatePolicies` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `roomTemplateID` smallint(6) DEFAULT NULL,
  `roomPoliciesID` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roomTemplatePolicies`
--

LOCK TABLES `roomTemplatePolicies` WRITE;
/*!40000 ALTER TABLE `roomTemplatePolicies` DISABLE KEYS */;
/*!40000 ALTER TABLE `roomTemplatePolicies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roomTemplates`
--

DROP TABLE IF EXISTS `roomTemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roomTemplates` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `fromEmail` varchar(50) DEFAULT NULL,
  `url` varchar(75) DEFAULT NULL,
  `policy` smallint(6) DEFAULT NULL,
  `mapURL` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roomTemplates`
--

LOCK TABLES `roomTemplates` WRITE;
/*!40000 ALTER TABLE `roomTemplates` DISABLE KEYS */;
/*!40000 ALTER TABLE `roomTemplates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roomTypeEquipment`
--

DROP TABLE IF EXISTS `roomTypeEquipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roomTypeEquipment` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `roomTemplateID` smallint(6) DEFAULT NULL,
  `equipmentID` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roomTypeEquipment`
--

LOCK TABLES `roomTypeEquipment` WRITE;
/*!40000 ALTER TABLE `roomTypeEquipment` DISABLE KEYS */;
/*!40000 ALTER TABLE `roomTypeEquipment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `number` varchar(15) DEFAULT NULL,
  `building` smallint(6) DEFAULT NULL,
  `roomTemplate` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siteConfig`
--

DROP TABLE IF EXISTS `siteConfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siteConfig` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siteConfig`
--

LOCK TABLES `siteConfig` WRITE;
/*!40000 ALTER TABLE `siteConfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `siteConfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `via`
--

DROP TABLE IF EXISTS `via`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `via` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `via`
--

LOCK TABLES `via` WRITE;
/*!40000 ALTER TABLE `via` DISABLE KEYS */;
/*!40000 ALTER TABLE `via` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'roomReservations'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-03-26 11:00:17
