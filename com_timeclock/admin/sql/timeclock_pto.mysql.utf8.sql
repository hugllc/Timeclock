CREATE TABLE IF NOT EXISTS `#__timeclock_pto` (
  `pto_id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(16) NOT NULL DEFAULT 'ACCRUAL',
  `user_id` int NOT NULL,
  `hours` float NOT NULL DEFAULT '0',
  `link_id` int NOT NULL DEFAULT '0',
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `notes` text NOT NULL,
  `checked_out` int DEFAULT NULL,
  `checked_out_time` datetime DEFAULT NULL,
  PRIMARY KEY (`pto_id`),
  UNIQUE KEY `valid_from` (`user_id`,`valid_from`, `type`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
