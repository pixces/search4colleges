CREATE TABLE IF NOT EXISTS `$prefixschool_admission_details` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`school_id` int(11) NOT NULL,
	`degree_id` int(11) NOT NULL,
	`tuition fees` text NOT NULL,
	`duration` varchar(255)  NULL,
	`last_date_of_admission` varchar(100) NULL,
	`date_of_entrance_exam` varchar(100) NULL,
	`how_to_apply` text NOT NULL,
	`brochures_and_prospectus` text NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
