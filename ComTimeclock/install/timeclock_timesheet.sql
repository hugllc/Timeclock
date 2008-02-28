CREATE TABLE IF NOT EXISTS `#__timeclock_timesheet` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `hours` float NOT NULL default '0',
  `Date` date NOT NULL default '0000-00-00',
  `insertDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Notes` text character set utf8 NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id` (`project_id`,`user_id`,`Date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63140 ;
