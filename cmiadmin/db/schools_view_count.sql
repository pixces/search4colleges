CREATE TABLE IF NOT EXISTS `$prefixschools_view_count` 
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`school_id` int(11) NOT NULL,
	`ip` varchar(255)  NULL,		
	`count` TINYINT( 3 ) NOT NULL ,
	`added_date` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB; 