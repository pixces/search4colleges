CREATE TABLE IF NOT EXISTS `$prefixschool_club_mm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
