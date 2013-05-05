CREATE TABLE IF NOT EXISTS `$prefixschool_culture_campuslife_clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_title` varchar(128) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
