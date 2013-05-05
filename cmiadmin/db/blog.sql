CREATE TABLE IF NOT EXISTS `$prefixblog` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`topic` text NOT NULL,
	`short_description` text NOT NULL,	
	`date` int(11) NOT NULL,			
	`sort_order` int(11) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=InnoDB;