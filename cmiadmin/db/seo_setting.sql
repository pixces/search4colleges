CREATE TABLE IF NOT EXISTS `$prefixseo_setting` 
	(
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`page_name` varchar(200) NOT NULL,
		`meta_title` varchar(255) NOT NULL,
		`meta_description` text NOT NULL,
		`meta_keywords` text NOT NULL,
		`added_date` int(11) NOT NULL,
		`last_modified_date` int(11) NOT NULL DEFAULT '0',
		`status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM ;