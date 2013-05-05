CREATE TABLE IF NOT EXISTS `$prefixarticle_category` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`seo_keyword` varchar(255) NOT NULL,
	`sort_order` int(11) NOT NULL DEFAULT 0,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      