CREATE TABLE IF NOT EXISTS `$prefixsearch_history` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`keyword` varchar(255) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=InnoDB;