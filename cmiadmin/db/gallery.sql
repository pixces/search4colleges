CREATE TABLE IF NOT EXISTS `$prefixgallery` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,			
	`seo_keyword` VARCHAR(255) NULL,
	`image` text,
	`sort_order` text NOT NULL,					  
	`added_date` int(11) NOT NULL DEFAULT '0',
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM;