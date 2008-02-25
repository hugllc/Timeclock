CREATE TABLE IF NOT EXISTS `jos_timeclock_users` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  UNIQUE KEY `UNIQUE` (`id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
