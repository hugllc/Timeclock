CREATE TABLE IF NOT EXISTS `#__timeclock_prefs` (
  `id` int(11) NOT NULL,
  `prefs` text,
  `published` smallint(6) NOT NULL default '0',
  `startDate` date NOT NULL default '0000-00-00',
  `endDate` date NOT NULL default '0000-00-00',
  `history` longtext,
  PRIMARY KEY  (`id`)
);

-- This is for upgrading.  It will fail on a new installation.
ALTER TABLE `jos_timeclock_prefs` ADD `manager` INT NOT NULL DEFAULT '0' AFTER `endDate`;