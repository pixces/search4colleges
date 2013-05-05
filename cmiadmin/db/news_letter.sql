CREATE TABLE IF NOT EXISTS `$prefixnews_letters` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `new_content` text NOT NULL,
  `added_date` int(11) NOT NULL,
  `last_modified_date` int(11) NOT NULL default '0',
  `status` enum('active','inactive','delete') NOT NULL default 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;