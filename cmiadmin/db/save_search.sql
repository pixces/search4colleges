CREATE TABLE IF NOT EXISTS `$prefixsave_search` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,
	`query` varchar(255) NOT NULL,	
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;