CREATE TABLE IF NOT EXISTS `#__timeclock_departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `manager_id` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`department_id`),
  KEY `manager_id` (`manager_id`),
  KEY `created_by` (`created_by`)
) DEFAULT CHARSET=utf8;
