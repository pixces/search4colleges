CREATE TABLE IF NOT EXISTS `$prefixschool_membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `school_memberShip_typeid` int(11) NOT NULL,
  `registeredon` varchar(128) NOT NULL,
  `expiryon` varchar(128) NOT NULL,
  `renewedon` varchar(128) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
