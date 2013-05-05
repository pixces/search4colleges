CREATE TABLE IF NOT EXISTS `$prefixschools_news` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`school_id` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`date` varchar(255)  NULL,		
	`image` varchar(255)  NULL,
	`short_description` varchar(255)  NULL,
	`details` varchar(255)  NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB; 