CREATE TABLE IF NOT EXISTS `$prefixevent` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`short_description` text NOT NULL,
	`sort_order` int(11) NOT NULL,
	 `seo_keyword` VARCHAR(255) NULL,
	`opening_date` int(11) NOT NULL,
	`closing_date` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=MyISAM;