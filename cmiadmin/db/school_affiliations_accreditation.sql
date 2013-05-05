CREATE TABLE IF NOT EXISTS `$prefixschools_affiliations_accreditation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
