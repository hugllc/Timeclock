CREATE TABLE IF NOT EXISTS `#__timeclock_users` (
  `project_id` int NOT NULL default '0',
  `user_id` int NOT NULL default '0',
  UNIQUE KEY `UNIQUE` (`project_id`,`user_id`)
) DEFAULT CHARSET=utf8;