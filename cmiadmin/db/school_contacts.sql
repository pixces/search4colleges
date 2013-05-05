CREATE TABLE IF NOT EXISTS `$prefixschool_contacts` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`school_id` int(11) NOT NULL,		
	`contact_person` varchar(255)  NULL,
	`address` varchar(255) NOT NULL,
	`state` varchar(255)  NULL,
	`city` varchar(255)  NULL,
	`phone` varchar(255) NOT NULL,
	`zip_code` varchar(255)  NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      