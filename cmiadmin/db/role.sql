CREATE TABLE IF NOT EXISTS `$prefixrole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(255) NOT NULL,
  `added_date` int(11) NOT NULL,
  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
