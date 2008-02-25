CREATE TABLE IF NOT EXISTS `jos_timeclock_projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `user_id` int(11) default NULL,
  `date` date NOT NULL default '0000-00-00',
  `research` smallint(6) NOT NULL default '0',
  `status` enum('ACTIVE','DONE','HOLD','SPECIAL') NOT NULL default 'ACTIVE',
  `type` enum('PROJECT','UMBRELLA','VACATION','SICK','HOLIDAY','UNPAID') NOT NULL default 'PROJECT',
  `parent_id` int(11) NOT NULL default '0',
  `wcCode` varchar(10) NOT NULL default '8803',
  `customer` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ProjectName` (`name`,`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=304 ;
