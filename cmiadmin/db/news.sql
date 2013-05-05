CREATE TABLE IF NOT EXISTS `$prefixnews` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`long_description` text NOT NULL,		
	`seo_keyword` VARCHAR(255) NULL,
	`sort_order` int(11) NOT NULL,
	`news_date` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=MyISAM;