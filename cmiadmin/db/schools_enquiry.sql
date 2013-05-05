CREATE TABLE IF NOT EXISTS `$prefixschools_enquiry` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`school_id` int(11) NOT NULL,
	`fullname` varchar(255)  NULL,		
	`email` varchar(255)  NULL,
	`enquiry` varchar(255)  NULL,
	`phone` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB; 