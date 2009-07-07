CREATE TABLE IF NOT EXISTS `jos_timeclock_projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `created_by` int(11) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `research` smallint(6) NOT NULL default '0',
  `type` enum('PROJECT','UMBRELLA','VACATION','SICK','HOLIDAY','UNPAID') NOT NULL default 'PROJECT',
  `parent_id` int(11) NOT NULL default '0',
  `wcCode` varchar(10) NOT NULL default '8803',
  `customer` int(11) NOT NULL default '0',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ProjectName` (`name`,`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
