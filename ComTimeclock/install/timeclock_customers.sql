CREATE TABLE IF NOT EXISTS `jos_timeclock_customers` (
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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;
