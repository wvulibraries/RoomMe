ALTER TABLE `policies` ADD COLUMN `roomsClosed` tinyint(4) DEFAULT 0;
ALTER TABLE `policies` ADD COLUMN `roomsClosedSnippet` tinyint(4) DEFAULT NULL;
ALTER TABLE `rooms` ADD COLUMN `roomClosed` tinyint(4) DEFAULT 0;
INSERT INTO `resultMessages` (`name`,`value`) VALUES ('roomClosed','This room is closed for reservations.');