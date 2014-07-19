#Series
DROP TABLE IF EXISTS `seriesReservations`;
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
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

ALTER TABLE `reservations` ADD COLUMN `seriesID` int(10) unsigned DEFAULT NULL;