CREATE TABLE IF NOT EXISTS `$prefixacademic` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`degree` varchar(255) NOT NULL,		
	`year_of_passing` VARCHAR(255) NOT NULL,
	`user_id` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;