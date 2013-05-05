CREATE TABLE IF NOT EXISTS `$prefixschools_additional` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`school_id` int(11) NOT NULL,
	`location_google_map` varchar(255) NOT NULL,
	`profile` text NOT NULL,
	`board_members` text NOT NULL,		
	`type_of_campus_area` varchar(255)  NULL,
	`student_population` varchar(255)  NULL,
	`student_body` varchar(255)  NULL,
	`cultural_diversity` text NOT NULL,
	`only_for_local` varchar(255)  NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
