CREATE TABLE IF NOT EXISTS `$prefixschool_news_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` varchar(128) NOT NULL,
  `image` text NOT NULL,
  `short_description` text NOT NULL,
  `FullStory` text NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
