CREATE TABLE IF NOT EXISTS `$prefixschool_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(128) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
