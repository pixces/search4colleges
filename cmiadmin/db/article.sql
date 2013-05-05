CREATE TABLE IF NOT EXISTS `$prefixarticle` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,	
	`category_id` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`seo_keyword` varchar(255) NOT NULL,
	`description` text NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      