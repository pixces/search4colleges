CREATE TABLE IF NOT EXISTS `$prefixschool_culture_campus_life` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `going_greek` varchar(255) NOT NULL,
  `dorm_sweet_dorm` varchar(255) NOT NULL,
  `selectivity` varchar(255) NOT NULL,
  `schoolculture_campuslife_gpa_id` int(11) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
