CREATE TABLE IF NOT EXISTS `#__timeclock_projects` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `description` text NOT NULL,
  `created_by` int(11) default NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `manager` int(11) NOT NULL default '0',
  `research` smallint(6) NOT NULL default '0',
  `type` enum('PROJECT','CATEGORY','PTO','HOLIDAY','UNPAID') NOT NULL default 'PROJECT',
  `parent_id` int(11) NOT NULL default '0',
  `wcCode1` int(11) NOT NULL default '8803',
  `wcCode2` int(11) NOT NULL default '0',
  `wcCode3` int(11) NOT NULL default '0',
  `wcCode4` int(11) NOT NULL default '0',
  `wcCode5` int(11) NOT NULL default '0',
  `wcCode6` int(11) NOT NULL default '0',
  `customer` int(11) NOT NULL default '0',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ProjectName` (`name`,`parent_id`)
);

REPLACE INTO `jos_timeclock_projects` (`id`, `name`, `description`, `created_by`, `created`, `manager`, `research`, `type`, `parent_id`, `wcCode1`, `wcCode2`, `wcCode3`, `wcCode4`, `wcCode5`, `wcCode6`, `customer`, `checked_out`, `checked_out_time`, `published`) VALUES(-3, 'Unpaid', 'This time is not used for billing customers or for payroll reports', NULL, '0000-00-00 00:00:00', 0, 0, 'CATEGORY', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 1);
REPLACE INTO `jos_timeclock_projects` (`id`, `name`, `description`, `created_by`, `created`, `manager`, `research`, `type`, `parent_id`, `wcCode1`, `wcCode2`, `wcCode3`, `wcCode4`, `wcCode5`, `wcCode6`, `customer`, `checked_out`, `checked_out_time`, `published`) VALUES(-2, 'Special', 'This category is for PTO and Holiday time.', NULL, '0000-00-00 00:00:00', 0, 0, 'CATEGORY', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 1);
REPLACE INTO `jos_timeclock_projects` (`id`, `name`, `description`, `created_by`, `created`, `manager`, `research`, `type`, `parent_id`, `wcCode1`, `wcCode2`, `wcCode3`, `wcCode4`, `wcCode5`, `wcCode6`, `customer`, `checked_out`, `checked_out_time`, `published`) VALUES(-1, 'General', 'This is uncategorized projects.  Everything without a category goes here.', NULL, '0000-00-00 00:00:00', 0, 0, 'CATEGORY', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 1);
