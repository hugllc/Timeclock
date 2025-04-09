CREATE TABLE IF NOT EXISTS `#__timeclock_departments` (
  `department_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_by` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `manager_id` int NOT NULL DEFAULT '0',
  `checked_out` int DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`department_id`),
  KEY `manager_id` (`manager_id`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8;
