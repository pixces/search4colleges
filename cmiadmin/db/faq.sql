CREATE TABLE IF NOT EXISTS `$prefixfaq` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`question` varchar(255) NOT NULL,
	`answer` text NOT NULL,
	`sort_order` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`last_modified_date` int(11) NOT NULL DEFAULT '0',
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
)ENGINE=MyISAM ;