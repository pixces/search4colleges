CREATE TABLE IF NOT EXISTS `$prefixusefull_link` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,	
	`link` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,		
	`description` text NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      