CREATE TABLE IF NOT EXISTS `$prefixfe_users` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`email` varchar(255) NOT NULL,		
	`password` VARCHAR(255) NOT NULL,
	`isapproved` int(1) NOT NULL DEFAULT 0,
	`user_id` int(11) NOT NULL,
	`user_type` VARCHAR(255) NOT NULL,
	`added_date` int(11) NOT NULL,
	`status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;