CREATE TABLE IF NOT EXISTS `$prefixschool_staff_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fe_staff_id` int(11) NOT NULL,
  `fe_school_id` int(11) NOT NULL,
  `allowed_sections` varchar(255) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
