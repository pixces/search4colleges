CREATE TABLE IF NOT EXISTS `$prefixblog_comment` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`blog_id` int(11) NOT NULL,			
	`user_id` int(11) NOT NULL,
	`comment` text NOT NULL,	
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)

) ENGINE=InnoDB;