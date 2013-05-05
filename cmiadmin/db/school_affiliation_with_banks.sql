CREATE TABLE IF NOT EXISTS `$prefixschool_affiliation_with_banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('bank','financial institute','education_loan_provider') NOT NULL DEFAULT 'bank',
  `name` varchar(128) NOT NULL,
  `short_description` text NOT NULL,
  `web_url` text NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;