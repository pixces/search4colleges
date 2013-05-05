CREATE TABLE IF NOT EXISTS `$prefixschool_scholarship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `duration` varchar(128) NOT NULL,
  `criteria` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
