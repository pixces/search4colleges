CREATE TABLE IF NOT EXISTS `$prefixschool_exam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `major_category_id` int(11) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `exam_date` varchar(255) NOT NULL,
  `download_schedule` varchar(255) NOT NULL,
  `result_status` int(2) NOT NULL,
  `result_on_date` varchar(255) NOT NULL,
  `result_web_url` varchar(255) NOT NULL,
  `added_date` int(11) DEFAULT NULL,
  `status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB;
