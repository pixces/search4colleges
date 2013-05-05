CREATE TABLE IF NOT EXISTS `$prefixschools_brochures` (
  `id` int(11) NOT NULL auto_increment,
  `school_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `filename` varchar(255) default NULL,
  `short_description` varchar(255) default NULL,
  `added_date` int(11) NOT NULL,
  `status` enum('active','inactive','delete') NOT NULL default 'active',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;  