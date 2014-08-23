CREATE TABLE IF NOT EXISTS `#__timeclock_timesheet` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `created_by` int(11) NOT NULL default '0',
  `hours1` float NOT NULL default '0',
  `hours2` float NOT NULL default '0',
  `hours3` float NOT NULL default '0',
  `hours4` float NOT NULL default '0',
  `hours5` float NOT NULL default '0',
  `hours6` float NOT NULL default '0',
  `worked` date NOT NULL default '0000-00-00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `notes` text character set utf8 NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `project_id` (`project_id`,`created_by`,`worked`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;