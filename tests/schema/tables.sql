
CREATE TABLE IF NOT EXISTS `jos_timeclock_timesheet` (
  `timesheet_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `hours1` float NOT NULL DEFAULT '0',
  `hours2` float NOT NULL DEFAULT '0',
  `hours3` float NOT NULL DEFAULT '0',
  `hours4` float NOT NULL DEFAULT '0',
  `hours5` float NOT NULL DEFAULT '0',
  `hours6` float NOT NULL DEFAULT '0',
  `worked` date NOT NULL DEFAULT '0000-00-00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `notes` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  CONSTRAINT `project_id` UNIQUE (`project_id`,`user_id`,`worked`)
);

CREATE TABLE IF NOT EXISTS `jos_timeclock_pto` (
  `pto_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `type` varchar(16) NOT NULL DEFAULT 'ACCRUAL',
  `user_id` int(11) NOT NULL,
  `hours` float NOT NULL DEFAULT '0',
  `link_id` int(11) NOT NULL DEFAULT '0',
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `notes` text NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  CONSTRAINT `valid_from` UNIQUE (`user_id`,`valid_from`, `type`)
);

CREATE TABLE IF NOT EXISTS `jos_timeclock_reports` (
  `report_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `type` varchar(16) NOT NULL,
  `filter` text NOT NULL,
  `users` longtext NOT NULL,
  `timesheets` longtext NOT NULL,
  `projects` longtext NOT NULL,
  `customers` longtext NOT NULL,
  `departments` longtext NOT NULL,
  `published` smallint(6) NOT NULL,
  CONSTRAINT `date` UNIQUE (`type`,`name`)
);

CREATE TABLE IF NOT EXISTS `jos_timeclock_users` (
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  CONSTRAINT `project_id_user_id` UNIQUE (`project_id`,`user_id`)
);

CREATE TABLE IF NOT EXISTS `jos_timeclock_customers` (
  `customer_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `company` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `address1` varchar(64) NOT NULL DEFAULT '',
  `address2` varchar(64) NOT NULL DEFAULT '',
  `city` varchar(64) NOT NULL DEFAULT '',
  `state` varchar(64) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(64) NOT NULL DEFAULT 'US',
  `notes` text NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `published` smallint(6) NOT NULL,
  `contact_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `bill_pto` tinyint(4) NOT NULL
);
CREATE INDEX `created_by_timeclock_customers` ON `jos_timeclock_customers` (`created_by`);
CREATE INDEX `published_timeclock_customers` ON `jos_timeclock_customers` (`published`);

CREATE TABLE IF NOT EXISTS `jos_timeclock_departments` (
  `department_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `manager_id` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1'
);
CREATE INDEX `manager_id_timeclock_departments` ON `jos_timeclock_departments` (`manager_id`);
CREATE INDEX `created_by_timeclock_departments` ON `jos_timeclock_departments` (`created_by`);


CREATE TABLE IF NOT EXISTS `jos_timeclock_projects` (
  `project_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL,
  `manager_id` int(11) NOT NULL DEFAULT '0',
  `research` smallint(6) NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL DEFAULT 'PROJECT',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `wcCode1` int(11) NOT NULL DEFAULT '8803',
  `wcCode2` int(11) NOT NULL DEFAULT '0',
  `wcCode3` int(11) NOT NULL DEFAULT '0',
  `wcCode4` int(11) NOT NULL DEFAULT '0',
  `wcCode5` int(11) NOT NULL DEFAULT '0',
  `wcCode6` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  CONSTRAINT `ProjectName` UNIQUE (`name`,`parent_id`)
);
CREATE INDEX `customer_id_jos_timeclock_projects` ON `jos_timeclock_projects` (`customer_id`);
CREATE INDEX `department_id_jos_timeclock_projects` ON `jos_timeclock_projects` (`department_id`);
CREATE INDEX `manager_id_jos_timeclock_projects` ON `jos_timeclock_projects` (`manager_id`);
