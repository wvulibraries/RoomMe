ALTER TABLE `reservePermissions` ADD COLUMN `username` varchar(50) DEFAULT NULL;

# Insert new error message into resultMessages table for new Restrictions
INSERT INTO resultMessages (name,value)
VALUES ('userNotListed','Username was not in the list of usernames allowed to reserve the room.');
