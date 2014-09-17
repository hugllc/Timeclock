
## This is for the timesheet table

ALTER TABLE `#__timeclock_timesheet` CHANGE `id` `timesheet_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_timesheet` CHANGE `created_by` `user_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_timesheet` ADD `created_by` INT NOT NULL DEFAULT '0' AFTER `worked` ;
