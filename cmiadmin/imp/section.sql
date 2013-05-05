CREATE TABLE IF NOT EXISTS `$prefixsection` 
	(
	  `id` int(5) NOT NULL AUTO_INCREMENT,
	  `section_name` varchar(60) NOT NULL,
	  `link_url` varchar(50) NOT NULL,
	  `parent_id` int(11) NOT NULL DEFAULT '0',
	  `sort_order` int(11) NOT NULL DEFAULT '0',
	  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
	  `hide_menu` tinyint(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM ;