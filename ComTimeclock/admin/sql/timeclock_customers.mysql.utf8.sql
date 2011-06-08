CREATE TABLE IF NOT EXISTS `#__timeclock_customers` (
  `id` int(11) NOT NULL auto_increment,
  `company` varchar(64) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `address1` varchar(64) NOT NULL default '',
  `address2` varchar(64) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `state` varchar(64) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `country` varchar(64) NOT NULL default 'US',
  `notes` text NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `published` smallint(6) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `bill_pto` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;