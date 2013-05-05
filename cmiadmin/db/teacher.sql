CREATE TABLE IF NOT EXISTS `$prefixteacher` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`first_name` varchar(255) NOT NULL,		
	`last_name` varchar(255)  NULL,
	`gender` ENUM('male','female') NOT NULL DEFAULT  'male',
	`date_of_birth` varchar(255)  NULL,
	`qualification` varchar(255) NULL,
	`college_name` varchar(255)  NULL,
	`address` text NOT NULL,
	`street` varchar(255)  NULL,
	`state` varchar(255)  NULL,
	`city` varchar(255)  NULL,
	`experience` varchar(255) NOT NULL,
	`primary_phone` varchar(255) NOT NULL,
	`phone` varchar(255) NOT NULL,
	`private_email` varchar(255) NOT NULL,
	`comments` varchar(255) NOT NULL,
	`zip_code` varchar(255)  NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      