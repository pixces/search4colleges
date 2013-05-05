CREATE TABLE IF NOT EXISTS `$prefixstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statusname` varchar(255) NOT NULL,
  `added_date` int(11) NOT NULL,
  `status` ENUM('active','inactive','delete') NOT NULL DEFAULT  'active',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
