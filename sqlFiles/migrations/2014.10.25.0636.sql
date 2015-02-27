ALTER TABLE `reservations` ADD COLUMN `openEvent` tinyint(3) DEFAULT 0;
ALTER TABLE `reservations` ADD COLUMN `openEventDescription` varchar(500) DEFAULT NULL;