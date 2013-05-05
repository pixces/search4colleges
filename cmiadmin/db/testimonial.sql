CREATE TABLE IF NOT EXISTS `$prefixtestimonial` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`sender_name` varchar(255) NOT NULL,
	`short_description` text NOT NULL,	
	`seo_keyword` VARCHAR(255) NULL,
	`testimonial_date` int(11) NOT NULL,
	`image` TEXT NULL,		
	`sort_order` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=MyISAM;