CREATE TABLE IF NOT EXISTS `#__timeclock_pto` (
  `pto_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`pto_id`),
  UNIQUE KEY `valid_from` (`user_id`,`valid_from`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
