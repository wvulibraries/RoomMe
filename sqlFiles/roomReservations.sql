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
  `restricted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `building`
--

LOCK TABLES `building` WRITE;
/*!40000 ALTER TABLE `building` DISABLE KEYS */;
INSERT INTO `building` VALUES (1,'Evansdale Library','evansdalecirculation@mail.wvu.edu','304-293-9759','evansdalecirculation@mail.wvu.edu','https://www.libraries.wvu.edu/libraries/evansdale/',20,1,30,'5','http://www.libraries.wvu.edu/hours/rss.php?p=dayNoRSS&library=2&day=','','http://database.lib.wvu.edu/cgi-bin/fines.pl?id=','{name} -- {number}','name','http://www.libraries.wvu.edu/services/rooms/snippetPublic.php?id=3','http://www.libraries.wvu.edu/hours/index.php?library=2',''),(2,'Downtown Campus Library','','304-293-4040','dclcirculation@mail.wvu.edu','https://www.libraries.wvu.edu/libraries/downtown/',6,14,12,'5','http://www.libraries.wvu.edu/hours/rss.php?p=dayNoRSS&library=1&day=','','http://database.lib.wvu.edu/cgi-bin/fines.pl?id=','{name} -- {number}','name','http://www.libraries.wvu.edu/services/rooms/snippetPublic.php?id=1','http://www.libraries.wvu.edu/hours/index.php?library=1',''),(3,'Health Sciences Library','','','someone@mail.wvu.edu','',0,0,0,'0','','','','{name} -- {number}','name','','','http://home.hsc.wvu.edu/its/forms/library-study-room-reservation/');
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
INSERT INTO `equipement` VALUES (1,'Apple Software','Includes iLife 09, iWork 09, MS Office 2008, Flip4Mac Free Player, Quicktime Pro',4,'http://systems.lib.wvu.edu/services/software/mac.php'),(2,'Dell Computer with DVD/CD drive','',1,''),(3,'iMac computer with DVD/CD drive','',1,''),(4,'Microsoft Office Suite 2008','Word, Excel, Access and PowerPoint',4,''),(5,'Seats up to 4 people','',3,''),(6,'Seats up to 6 people','',3,''),(7,'Seats up to 10 people','',3,''),(8,'46\" wall monitor','',2,''),(9,'Seats up to 50 people','',3,''),(10,'Projector','',5,''),(11,'Computer for instructor only','',1,''),(12,'Seats up to 30 people','',3,''),(13,'DVD/VHS Player','',7,''),(14,'Sympodium','Dell Computer with DVD/CD and VHS player ',8,''),(15,'Whiteboard','',9,''),(16,'Free standing plasma screen','',7,''),(17,'Mobile Plasma Screen','',1,''),(18,'Computer Monitor','',7,''),(19,'Chalkboard','',10,''),(20,'Seats up to 12 people','equipment',3,''),(21,'Seats up to 15 people','',3,''),(22,'Blu-ray DVD player','',7,'');
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
INSERT INTO `equipementTypes` VALUES (1,'Computers'),(2,'Televisions'),(3,'Seats'),(4,'Software'),(5,'Projectors'),(7,'Hardware'),(8,'Combined workstation - Multiple Electronic Equipme'),(9,'Dry Erase Whiteboard'),(10,'Chalkboard'),(11,'Seats up to 12 people');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pageContent`
--

LOCK TABLES `pageContent` WRITE;
/*!40000 ALTER TABLE `pageContent` DISABLE KEYS */;
INSERT INTO `pageContent` VALUES (1,'DCL Study Room Policies','<p>\r\n	<a href=\"https://lib.wvu.edu:443/services/rooms/snippetPublic.php?id=2\"><strong><span style=\"color:#ff0000;\">Special Room Reservations Procedures for the Last Week of Classes and Finals Week (DCL only)</span><span style=\"color: rgb(255, 0, 0);\">&nbsp;</span></strong></a></p>\r\n<p>\r\n	<strong style=\"font-family: arial, helvetica, sans-serif; font-size: 14px;\">Downtown Campus Library Study Room Policies:</strong></p>\r\n<ul>\r\n	<li>\r\n		Users may reserve up to 6 hours in every 14 day period</li>\r\n	<li>\r\n		Reservations may be made up to 14 days in advance</li>\r\n	<li>\r\n		Users may not make more than 6 reservations in every 14 day period</li>\r\n	<li>\r\n		Rooms may be reserved for up to 4 hours at one time.</li>\r\n	<li>\r\n		Study rooms are not limited by group size.</li>\r\n	<li>\r\n		Once a reservation time has begun, the room cannot be cancelled.&nbsp; Users who do not show up for their reserved time lose that time from their 6 hour allotment.</li>\r\n	<li>\r\n		Equipment for the room, i.e., wireless keyboard and mouse, must be checked out by the person holding the reservations</li>\r\n</ul>\r\n'),(2,'Special DCL Protocols for finals week','<p>\r\n	Due to the high demand for study rooms during the last week of classes and final examination week, the following <u>special policy</u> is will be in effect during this time:</p>\r\n<ul>\r\n	<li>\r\n		Reservations for the last week of classes and&nbsp;finals week will not be accepted until after April 24, 2015</li>\r\n	<li>\r\n		Users may reserve up to <strong>4 hours</strong> during the last week of classes and final examination week</li>\r\n	<li>\r\n		Reservations for this period will be accepted <strong>beginning at 8:00&nbsp;AM on Friday April 24, 2015</strong></li>\r\n</ul>\r\n'),(3,'Evansdale Study Room Protocols','<h2>\r\n	Room Reservation Policies</h2>\r\n<ul>\r\n	<li>\r\n		<span class=\"boldText\">Study Room </span>requests&nbsp;can also be made in-person at the Evansdale Library or by telephone 304.293.9759.</li>\r\n	<li>\r\n		Unoccupied rooms can be used on a first come basis, but reservations take precedence.</li>\r\n	<li>\r\n		Rooms can be reserved for no more than <span class=\"boldText\">4 hours per day</span>, per person at a time. No reservation is forefeited.</li>\r\n	<li>\r\n		These rooms are not soundproof. Please keep noise to a reasonable level.</li>\r\n	<li>\r\n		The library&rsquo;s covered-mug policy applies to these rooms.</li>\r\n	<li class=\"boldText\">\r\n		The library is not responsible for personal items left in the study rooms. In addition, you are liable for any library books, laptops, music reserve CDs, or other library materials that you use within the room.</li>\r\n	<li>\r\n		If you have questions concerning this policy or need to cancel your reservation, please contact the Evansdale Library Access Services Desk at 304.293.9759.</li>\r\n</ul>\r\n'),(4,'Evansdale Classroom Protocol','<h3>\r\n	Classroom Policies:</h3>\r\n<ul>\r\n	<li>\r\n		Library functions in the Classroom have priority over non-library functions.</li>\r\n	<li>\r\n		Priority of reserving for non-library classes or events are weighted toward large groups (8 or more people) and the need for the podium computer and display screen.</li>\r\n	<li>\r\n		Reservation requests can be made at the Evansdale Library Reference Desk (304-293-4695 or evansdalereference@mail.wvu.edu) on a first-come/first-available basis.</li>\r\n	<li>\r\n		We recommend reservations be made at least <b>7-14 days in advance</b>.</li>\r\n	<li>\r\n		Full-Semester regularly scheduled class reservations must be approved by the Evansdale Library Director, Mary Strife (mary.strife@mail.wvu.edu or 304-293-9756) or her designee.</li>\r\n	<li>\r\n		A regularly scheduled class may be asked, at least one class period in advance, to relocate in the event of a library instruction reservation.</li>\r\n	<li>\r\n		First time presenters/instructors are <u>strongly</u> encouraged to make an appointment with a library faculty member or the LA for Technology Services to test any presentation software and be instructed on the media use prior to the day of the reservation.</li>\r\n	<li>\r\n		The room is available only during published Evansdale Library hours. These hours are dependent on whether the University is in-session or on break. Exceptions can be requested from the Director or her designee.</li>\r\n	<li>\r\n		No software may be downloaded onto the instructor or individual workstation computers.</li>\r\n</ul>\r\n'),(5,'DCL Room 104 Viewing Room','<ul>\r\n	<li>\r\n		This room may be reserved by contacting the Multimedia Services Department at 304-293-0354</li>\r\n	<li>\r\n		The room may be reserved up to 7 times a semester by one faculty member</li>\r\n	<li>\r\n		The room may only be reserved by faculty and is not available for student study groups</li>\r\n</ul>\r\n'),(6,'DCL Room 136 Reference Classroom','<ul>\r\n	<li>\r\n		This room may be reserved by contacting the Jing Qiu at 304-293-0335</li>\r\n	<li>\r\n		The room may be reserved up to 4 times a semester by one faculty member</li>\r\n	<li>\r\n		The room may be reserved by faculty (in conjunction with a librarian)&nbsp;and is not available for student study groups</li>\r\n</ul>\r\n'),(7,'DCL Room 136 Policies','<p>\r\n	&nbsp;</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Guidelines for Using the</p>\r\n<p align=\"center\">\r\n	<strong>Downtown Campus Library Computer Classroom (Room 136) </strong></p>\r\n<p align=\"center\">\r\n	Revised January 2013</p>\r\n<p align=\"center\">\r\n	&nbsp;</p>\r\n<p>\r\n	<strong>Use:</strong></p>\r\n<ul>\r\n	<li>\r\n		The computer classroom is used primarily for classes, demonstrations, and training sessions pertaining to the Library.&nbsp; Library instruction has priority over any other type of use of the room.</li>\r\n	<li>\r\n		The computer classroom is open for public use when class is not in session. Daily teaching schedule is posted by the door. People are asked to vacate the room 15 minutes before the scheduled instruction sessions for the instructors to get prepared.</li>\r\n	<li>\r\n		The classroom may be scheduled for non-library related sessions if other options through <strong>Facilities Planning and Scheduling</strong> are unavailable.</li>\r\n	<li>\r\n		The Coordinator for Library Instruction assists in orienting session leaders to the setup and use of the equipment.</li>\r\n	<li>\r\n		Each instructor/session leader is responsible for getting the equipment set up for classes prior to sessions and turning things off properly after the sessions.</li>\r\n	<li>\r\n		No food or drink in the classroom.</li>\r\n</ul>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<strong>Scheduling:</strong></p>\r\n<ul>\r\n	<li>\r\n		Reservations are made at the Downtown Campus Library Reference Desk by calling 304-293-3640.</li>\r\n	<li>\r\n		Requests for library instruction are handled on a first-come-first-serve basis.&nbsp; Faculty are encouraged to make reservations as early as possible.</li>\r\n	<li>\r\n		Reservations for non-library related sessions may be made two days or up to two weeks in advance. Other advance reservations are handled on a case-by-case basis by the Coordinator for Library Instruction.</li>\r\n	<li>\r\n		Due to popular demand for the classroom, no more than four requests can be made by the same instructor for the same class each semester.</li>\r\n</ul>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	<strong>Equipment:</strong></p>\r\n<ul>\r\n	<li>\r\n		Equipment is not loaned out from the classroom.</li>\r\n	<li>\r\n		All software and configuration changes must be approved by the Systems and the Reference Departments.</li>\r\n	<li>\r\n		Printer and computers are maintained by the Systems Office. Multimedia equipment is maintained by the Media Services.</li>\r\n</ul>\r\n<p style=\"margin-left:.25in;\">\r\n	&nbsp;</p>\r\n<p>\r\n	<strong>Security:</strong></p>\r\n<ul>\r\n	<li>\r\n		Only the staff members who have keys to the room are permitted to open the classroom.&nbsp;</li>\r\n	<li>\r\n		Doors should be locked when the room is not in use. &nbsp;</li>\r\n</ul>\r\n'),(8,'No Public Scheduling','<p>\r\n	This room is a <strong>classroom</strong> that can only be reserved by <strong>faculty</strong>. It is not available for student study groups. To reserve this classroom for teaching, please contact the specific Library to reserve: Downtown Room 104 (304-293-0354), Downtown Room 136 or 2036 (304-293-0335) or Evansdale (304-293-4695)</p>\r\n<p>\r\n	.</p>\r\n');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

LOCK TABLES `policies` WRITE;
/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
INSERT INTO `policies` VALUES (1,4,1,0,'5',1,1,15,4,250,'','Evansdale Library Study R','Rooms located at the Evansdale Library for booking',4,1),(2,4,1,0,'5',1,1,15,1,30,'','Viewing Room','Rooms with special equipment',4,1),(3,100,30,0,'5',0,1,15,500,133,'http://www.libraries.wvu.edu/services/rooms/snippetPublic.php?id=4','Classroom','Instructional classroom',120,0),(4,6,14,0,'5',1,1,15,6,15,'http://www.libraries.wvu.edu/services/rooms/snippetPublic.php?id=1','Downtown Study Rooms','All study rooms at the DCL',4,1),(5,15,133,0,'5',0,1,15,7,133,'http://www.libraries.wvu.edu/services/rooms/snippetPublic.php?id=5','DCL Room 104','Room 104 Viewing Classroom',3,1),(6,127,133,0,'5',0,1,15,500,133,'http://www.libraries.wvu.edu/services/rooms/snippetPublic.php?id=7','DCL Research Services','Research Services Classroom',120,1),(8,100,30,0,'5',0,1,15,500,133,'','Listening Lab','Music listening lab',120,1),(9,4,1,0,'5',0,0,15,4,133,'','Closed for summer','Rooms unavailable during renovation',4,0);
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
  `seriesID` int(10) unsigned DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `openEvent` tinyint(3) DEFAULT '0',
  `openEventDescription` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=103729 DEFAULT CHARSET=latin1;
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
INSERT INTO `resultMessages` VALUES ('reservationCreated','Your reservation for {roomName} has been created.'),('dataValidationError','Data Validation Error'),('invalidUsername','Username is invalid or information could not be retrieved.'),('invalidDate','Invalid date selected.'),('endBeforeStart','The end time selected is before the start time.'),('duplicateReservation','This is a duplicate reservation request.'),('reservationConflict','This room is already reserved for all or part of the selected hours'),('policyError','Error occurred retrieving policy information'),('sameDayReservation','Unable to create reservation. Evansdale Reservation requests must be created 1 day in advance'),('systemsPolicyError','Error occurred retrieving system policy information'),('maxFineExceeded','User owes library fines in excess of ${amount}'),('patronReservationInfo','Error retrieving patron reservation information'),('libraryClose','The library is closed for part or all of the selected hours'),('reservationLengthTooLong','Reservation length exceeds time limit allowed by the policy.'),('userOverSystemHours','Reservation would put user over the number of hours allowed by the system per period.'),('userOverLibraryHours','Reservation would put user over the number of hours allowed by this library per period.'),('userOverPolicyHours','Reservation would put user over the number of hours allowed by the policy per period.'),('userOverSystemBookings','Reservation would put user over number of system reservations allowed per period.'),('userOverBuildingBookings','Reservation would put user over the number of reservations allowed by this library per period.'),('userOverPolicyBookings','Reservation would put user over total number of reservations allowed by the policy per period.'),('errorInserting','Error creating reservation. Due to the nature of this error, resubmitting will likely NOT work. Please contact the library to create your reservation.'),('tooFarInFuture','Reservation is too far in the future. '),('reservationUpdated','Reservation Updated.'),('policyLabel','Policy'),('reservationInPast','Cannot create reservations in the past'),('multipleRoomBookings','Cannot book multiple rooms at the same time.'),('emailNotProvided','Email Address is Required.');
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roomTemplates`
--

LOCK TABLES `roomTemplates` WRITE;
/*!40000 ALTER TABLE `roomTemplates` DISABLE KEYS */;
INSERT INTO `roomTemplates` VALUES (1,'Ground Floor Study Rooms','Rooms that seat 1-12 individuals','evansdalecirculation@mail.wvu.edu','',1,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_g.gif'),(2,'Classroom','Room with 30 seats and computer','evansdalecirculation@mail.wvu.edu','',3,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_g.gif'),(3,'Viewing Room','Rooms with special equipment or functionality','evansdalecirculation@mail.wvu.edu','',2,'https://www.libraries.wvu.edu/about/wayfinding/maps/evansdale/edale_floor_1.pdf'),(4,'Downtown Study Rooms: 1-4, 1st floor','Study room seats up to 4 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_1.gif'),(5,'Downtown Study Rooms: 1-4, 4th floor','Study rooms seats up to 4 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_4.gif'),(6,'Downtown Study Rooms: 1-4, 6th floor','Study rooms seats up to 4 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_6.gif'),(7,'Downtown Study Rooms: 1-4, Lower Level','Study room seats up to 4 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_ll.gif'),(8,'Downtown Study Rooms: 4-10, 4th floor','Study room seats up to10 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_4.gif'),(9,'Downtown Study Rooms: 4-10, 6th floor','Study room seats up to 10 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_6.gif'),(10,'Downtown Study Rooms: 1-6, LL','Study rooms seats up to 6 people','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_ll.gif'),(11,'DCL Room 104','Room seating up to 50 people.  Computer only for instructor.','multimedia@mail.wvu.edu','',5,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_ll.gif'),(12,'Research Services (DCL)','Research Services classroom','jing.qui@mail.wvu.edu','',6,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_ll.gif'),(13,'Media Viewing Rooms','Rooms on the Lower Level generally used by Multimedia Services','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_ll.gif'),(14,'Main Floor Study Rooms','Rooms that will seat 1-12 individuals','evansdalecirculation@mail.wvu.edu','',1,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_1.gif'),(15,'Second Floor Study Room 1-12','Rooms which seat 1-12 individuals','evansdalecirculation@mail.wvu.edu','',1,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_2.gif'),(16,'Second Floor Study Room 1-4','Rooms which seat 1 - 4 individuals','evansdalecirculation@mail.wvu.edu','',1,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_2.gif'),(17,'Second Floor Study Room 1- 6','Rooms which seat 1-6 individuals','evansdalecirculation@mail.wvu.edu','',1,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_2.gif'),(18,'Glasscock Listening Lab','Listening to music','evansdalecirculation@mail.wvu.edu','',8,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/evl_2.gif'),(19,'Rooms with only a computer monitor','Rooms with only a computer monitor','evansdalecirculation@mail.wvu.edu','',1,'https://www.libraries.wvu.edu/about/wayfinding/maps/evansdale/edale_floor_2.pdf'),(20,'Presentation Practice Room: DCL Room 1036','Room designed for practicing a  presentation.','dclcirculation@mail.wvu.edu','',4,'https://www.libraries.wvu.edu/services/computers/availableComputers/images/dcl_1.gif'),(21,'Closed for Renovation','Rooms closed during summer for renovation','','',9,''),(22,'Ground Floor Study Room Large','','','',1,'');
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
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roomTypeEquipment`
--

LOCK TABLES `roomTypeEquipment` WRITE;
/*!40000 ALTER TABLE `roomTypeEquipment` DISABLE KEYS */;
INSERT INTO `roomTypeEquipment` VALUES (16,7,8),(17,7,2),(18,7,4),(19,7,5),(20,10,1),(21,10,6),(22,10,3),(40,11,11),(41,11,2),(42,11,4),(43,11,10),(44,11,9),(49,12,11),(50,12,2),(51,12,4),(52,12,10),(53,12,12),(56,13,8),(57,13,13),(58,13,6),(61,2,12),(62,2,14),(65,18,17),(66,18,15),(69,3,18),(74,19,18),(75,17,18),(76,17,6),(77,17,15),(78,15,20),(79,16,5),(80,4,8),(81,4,2),(82,4,4),(83,4,5),(84,4,15),(85,5,8),(86,5,2),(87,5,4),(88,5,5),(89,5,15),(90,6,8),(91,6,2),(92,6,4),(93,6,5),(94,6,15),(95,8,8),(96,8,2),(97,8,4),(98,8,7),(99,8,15),(100,9,8),(101,9,2),(102,9,4),(103,9,7),(104,9,15),(115,20,8),(116,20,22),(117,20,11),(118,20,2),(119,20,10),(120,20,21),(121,1,5),(122,22,20);
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
  `pictureURL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'First floor','126',1,14,''),(2,'First Floor','127',1,14,''),(3,'First Floor ','131 ',1,3,''),(14,'Ground Floor ','G-25',1,22,'https://lib.wvu.edu/services/rooms/images/evansdale/G25b.jpg'),(15,'Ground Floor ','G-27 ',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G27b.jpg'),(16,'Ground Floor','G-28 ',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G28b.jpg'),(17,'Ground Floor','G-29 ',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G29b.jpg'),(18,'Ground Floor ','G-33 ',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G33b.jpg'),(19,'Ground Floor ','G-32',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G32b.jpg'),(20,'Ground Floor ','G-34 ',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G34b.jpg'),(21,' Classroom','130',1,2,'https://lib.wvu.edu/services/rooms/images/evansdale/130b.jpg'),(22,'Lower Level','114',2,7,' https://lib.wvu.edu/services/rooms/images/downtown/114.jpg'),(23,'Lower Level','124',2,7,' https://lib.wvu.edu/services/rooms/images/downtown/124.jpg'),(24,'Lower Level','134',2,7,' https://lib.wvu.edu/services/rooms/images/downtown/134.jpg'),(27,'1st Floor','1028',2,4,''),(28,'4th Floor','4000A',2,5,''),(29,'4th Floor','4000B',2,8,' https://lib.wvu.edu/services/rooms/images/downtown/4000B.jpg'),(30,'4th Floor','4000C',2,8,' https://lib.wvu.edu/services/rooms/images/downtown/4000c.jpg'),(31,'4th Floor','4000D',2,5,' https://lib.wvu.edu/services/rooms/images/downtown/4000D.jpg'),(32,'4th Floor','4036',2,5,''),(33,'4th Floor','4038',2,8,' https://lib.wvu.edu/services/rooms/images/downtown/4038.jpg'),(34,'6th Floor','6000A',2,6,' https://lib.wvu.edu/services/rooms/images/downtown/6000A.jpg'),(35,'6th Floor','6000B',2,9,' https://lib.wvu.edu/services/rooms/images/downtown/6000B.jpg'),(36,'6th Floor','6000C',2,9,' https://lib.wvu.edu/services/rooms/images/downtown/6000C.jpg'),(37,'6th Floor','6000D',2,6,' https://lib.wvu.edu/services/rooms/images/downtown/6000D.jpg'),(38,'6th Floor','6036',2,6,' https://lib.wvu.edu/services/rooms/images/downtown/6036.jpg'),(39,'6th Floor','6038',2,9,' https://lib.wvu.edu/services/rooms/images/downtown/6038.jpg'),(40,'Viewing Room','Classroom 104',2,11,''),(41,'Research Services 136 ','Classroom 136',2,12,''),(43,'Lower Level','100-A',2,13,' https://lib.wvu.edu/services/rooms/images/downtown/100A.jpg'),(44,'Lower Level','100-B',2,13,' https://lib.wvu.edu/services/rooms/images/downtown/100B.jpg'),(47,'2nd floor','200',1,15,'https://lib.wvu.edu/services/rooms/images/evansdale/200b.jpg'),(48,'2nd floor','201',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/201b.jpg'),(49,'2nd floor ','217 - Glasscock',1,18,'https://lib.wvu.edu/services/rooms/images/evansdale/217.jpg'),(51,'2nd floor','202',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/202b.jpg'),(52,'2nd floor','203',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/203b.jpg'),(53,'2nd floor','204',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/204b.jpg'),(55,'2nd floor','209',1,17,'https://lib.wvu.edu/services/rooms/images/evansdale/209b.jpg'),(56,'2nd floor','210',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/210b.jpg'),(57,'2nd floor','211',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/211b.jpg'),(58,'2nd floor','212',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/212.jpg'),(59,'2nd floor','213',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/213b.jpg'),(60,'2nd floor','214',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/214b.jpg'),(61,'2nd floor','215',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/215b.jpg'),(63,'2nd floor  ','218 ',1,17,'https://lib.wvu.edu/services/rooms/images/evansdale/218b.jpg'),(64,'2nd floor','219',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/219b.jpg'),(65,'2nd floor','216',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/216b.jpg'),(66,'2nd floor','220',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/220b.jpg'),(67,'2nd floor','221',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/221b.jpg'),(68,'2nd floor ','222 ',1,17,'https://lib.wvu.edu/services/rooms/images/evansdale/222b.jpg'),(69,'2nd floor','227',1,17,'https://lib.wvu.edu/services/rooms/images/evansdale/227.jpg'),(72,'2nd floor','230',1,17,'https://lib.wvu.edu/services/rooms/images/evansdale/230b.jpg'),(73,'2nd floor','231',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/231b.jpg'),(74,'2nd floor','232',1,16,'https://lib.wvu.edu/services/rooms/images/evansdale/232b.jpg'),(76,'Lower Level','122A',2,10,' https://lib.wvu.edu/services/rooms/images/downtown/122A.jpg'),(77,'Lower Level','122B',2,10,' https://lib.wvu.edu/services/rooms/images/downtown/122B.jpg'),(80,'2nd floor','228/229',1,2,'https://lib.wvu.edu/services/rooms/images/evansdale/228.jpg'),(81,'Presentation Practice Room','Practice 1036',2,20,' https://lib.wvu.edu/services/rooms/images/downtown/1036.jpg'),(82,'Research Services 2036','Classroom 2036',2,12,''),(83,'Collaborative Training Lab','G16',1,2,'https://lib.wvu.edu/services/rooms/images/evansdale/G16.jpg'),(84,'Ground Floor','G-26',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G26b.jpg'),(85,'Ground Floor','G-35',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G35b.jpg'),(86,'Ground Floor','G-41',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G41b.jpg'),(87,'Ground Floor','G-42',1,1,'https://lib.wvu.edu/services/rooms/images/evansdale/G42b.jpg'),(88,'Ground Floor ','G-43',1,1,'');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seriesReservations`
--

DROP TABLE IF EXISTS `seriesReservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seriesReservations` (
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
  `allDay` tinyint(4) NOT NULL DEFAULT '1',
  `frequency` tinyint(4) NOT NULL DEFAULT '0',
  `weekdays` varchar(5000) DEFAULT NULL,
  `seriesEndDate` int(10) unsigned DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seriesReservations`
--

LOCK TABLES `seriesReservations` WRITE;
/*!40000 ALTER TABLE `seriesReservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `seriesReservations` ENABLE KEYS */;
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
INSERT INTO `siteConfig` VALUES ('24hour','0'),('adjustedDeleteTime','5'),('allowMultipleBookings','0'),('calendarDisplayName','{number}'),('calendarHourDisplay','g:ia'),('calendarHourPrior','0'),('daysToDisplayOnCancelledPage','20'),('defaultReservationIncrements','15'),('displayDurationOnBuildingCal','0'),('displayDurationOnRoomsCal','0'),('displayNameAs','username'),('hoursOnReservationTable','1'),('maxBookingsAllowedSystem','80'),('maxFineAllowedSystem','5.00'),('maxHoursAllowedSystem','200'),('openEventEmail','foo@bar.com'),('periodSystem','0'),('showOpenEvent','0');
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
INSERT INTO `via` VALUES (1,'Staff, Over Phone'),(2,'Staff, In Person'),(3,'Staff, via email');
/*!40000 ALTER TABLE `via` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'roomReservations'
--

--
-- Dumping routines for database 'roomReservations'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-04-17 14:36:50
