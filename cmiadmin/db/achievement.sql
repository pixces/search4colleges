CREATE TABLE IF NOT EXISTS `$prefixachievement` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL ,
	`caption` varchar(255) NOT NULL,
	`short_description` varchar(255) NOT NULL,	
	`year` varchar(255) NOT NULL,	
	`image` text,
	`added_date` int(11) NOT NULL DEFAULT '0',
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM;