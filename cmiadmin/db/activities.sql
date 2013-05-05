CREATE TABLE IF NOT EXISTS `$prefixactivities` 
(
	`id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
	`userid` bigint(10) unsigned NOT NULL DEFAULT '0',
	`ip` varchar(15) NOT NULL DEFAULT '',
	`section` varchar(60) NOT NULL DEFAULT '',
	`action` varchar(40) NOT NULL DEFAULT '',
	`url` varchar(100) NOT NULL DEFAULT '',
	`time` bigint(10) unsigned NOT NULL DEFAULT '0',
	`info` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM ;