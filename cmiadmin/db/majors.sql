CREATE TABLE IF NOT EXISTS `$prefixmajors` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`parent_id` int(11) NOT NULL,
	`seo_keyword` VARCHAR(255) NULL,
	`sort_order` int(11) NOT NULL,	
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=MyISAM;