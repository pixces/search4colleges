CREATE TABLE IF NOT EXISTS `$prefixschool_image_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `type` varchar(128) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `caption` varchar(128) NOT NULL,
  `date` varchar(128) NOT NULL,
  `publish` int(11) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;