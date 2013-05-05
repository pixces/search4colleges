CREATE TABLE IF NOT EXISTS `$prefixcity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `pincode` int(11) NOT NULL,
  `zipcode` int(11) NOT NULL,
  `code` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
