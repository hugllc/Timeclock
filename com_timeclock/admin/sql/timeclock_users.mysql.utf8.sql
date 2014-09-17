CREATE TABLE IF NOT EXISTS `#__timeclock_users` (
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  UNIQUE KEY `UNIQUE` (`project_id`,`user_id`)
) DEFAULT CHARSET=utf8;