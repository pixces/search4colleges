CREATE TABLE IF NOT EXISTS `$prefixcounselor_query` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`counselor_id` int(11) NOT NULL,
	`topic` varchar(255) NOT NULL,		
	`question` text NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

      