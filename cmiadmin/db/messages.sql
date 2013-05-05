CREATE TABLE IF NOT EXISTS `$prefixmessages` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`sender_id` int(11) NOT NULL,
	`message` text NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=InnoDB;