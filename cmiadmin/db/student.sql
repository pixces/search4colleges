CREATE TABLE IF NOT EXISTS `$prefixstudent` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`first_name` varchar(255) NOT NULL,		
	`last_name` varchar(255) NOT NULL,
	`gender` ENUM('male','female') NOT NULL DEFAULT  'male',
	`date_of_birth` varchar(255) NOT NULL,
	`educational_interest` varchar(255) NOT NULL,
	`address` text NOT NULL,
	`street` varchar(255) NOT NULL,
	`state` varchar(255) NOT NULL,
	`city` varchar(255) NOT NULL,
	`zip_code` varchar(255) NOT NULL,
	`short_description` text NOT NULL,
	`long_description` text NOT NULL,
	`goals_in_life` text NOT NULL,
	`expectation_from_s4c` text NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;