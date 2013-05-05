CREATE TABLE IF NOT EXISTS `$prefixschool_culture_campus_life_sports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `team_description` text NOT NULL,
  `team_type` text NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;