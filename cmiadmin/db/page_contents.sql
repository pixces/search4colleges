CREATE TABLE IF NOT EXISTS `$prefixpage_contents` 
	(
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`page_name` varchar(255) NOT NULL,
		`page_contents` text NOT NULL,
		`added_date` int(11) NOT NULL,
		`last_modified_date` int(11) NOT NULL DEFAULT '0',
		`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM;