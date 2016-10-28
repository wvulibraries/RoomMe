DROP TABLE IF EXISTS `reservePermissions`;
CREATE TABLE `reservePermissions` (
	`ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`resourceID` int(10) unsigned DEFAULT NULL,
	`resourceType` varchar(30) DEFAULT NULL,
	`roomID` int(10) unsigned DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
	PRIMARY KEY (`ID`),
  KEY `resourceID` (`resourceID`),
  KEY `resourceType` (`resourceType`),
	KEY `roomID` (`roomID`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

# Insert new error message into resultMessages table for new Restrictions
INSERT INTO resultMessages (name,value)
VALUES ('emailNotinList','Error email address not on Restrictions list for this Room');
