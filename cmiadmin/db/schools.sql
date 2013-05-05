CREATE TABLE IF NOT EXISTS `$prefixschools` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`school_name` varchar(255) NOT NULL,
	`school_website` varchar(255) NOT NULL,
	`first_name` varchar(255)  NULL,		
	`last_name` varchar(255)  NULL,
	`gender` ENUM('male','female') NOT NULL DEFAULT  'male',
	`designation` varchar(255)  NULL,
	`address` text NOT NULL,
	`street` varchar(255)  NULL,
	`state` varchar(255) NOT NULL,
	`city` varchar(255) NOT NULL,
	`zip_code` varchar(255) NOT NULL,
	`department` varchar(255)  NULL,
	`department_head` varchar(255)  NULL,
	`department_emailid` varchar(255)  NULL,
	`phone` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB; 