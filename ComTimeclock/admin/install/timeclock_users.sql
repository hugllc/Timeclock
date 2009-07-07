CREATE TABLE IF NOT EXISTS `#__timeclock_users` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  UNIQUE KEY `UNIQUE` (`id`,`user_id`)
);