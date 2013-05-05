CREATE TABLE IF NOT EXISTS `$prefixfriends` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`friend_id` int(11) NOT NULL,
	`invitaion_status` ENUM('approved','declined','inprocess') NOT NULL DEFAULT  'inprocess',
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      