
## This is for the timesheet table

ALTER TABLE `#__timeclock_timesheet` CHANGE `id` `timesheet_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_timesheet` CHANGE `created_by` `user_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_timesheet` ADD `created_by` INT NOT NULL DEFAULT '0' AFTER `worked` ;

ALTER TABLE `#__timeclock_customers` CHANGE `id` `customer_id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__timeclock_customers` ADD `contact_id` INT NOT NULL DEFAULT '0' AFTER `published`;
ALTER TABLE `#__timeclock_customers` ADD `modified` DATETIME NOT NULL AFTER `created`;

ALTER TABLE `#__timeclock_projects` CHANGE `id` `project_id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__timeclock_projects` ADD `modified` DATETIME NOT NULL AFTER `created`;

ALTER TABLE `#__timeclock_projects` CHANGE `manager` `manager_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_projects` CHANGE `customer` `customer_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_projects` ADD `department_id` INT NOT NULL DEFAULT '0' AFTER `customer_id`;

ALTER TABLE `#__timeclock_timesheet` CHANGE `id` `timesheet_id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__timeclock_timesheet` CHANGE `created_by` `user_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `#__timeclock_timesheet` ADD `created_by` INT NOT NULL DEFAULT '0' AFTER `worked`;
ALTER TABLE `#__timeclock_timesheet` ADD `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created`;

ALTER TABLE `#__timeclock_users` CHANGE `id` `project_id` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `#__timeclock_projects` CHANGE `type` `type` ENUM('PROJECT','CATEGORY','PTO','HOLIDAY','UNPAID','FLOATING_HOLIDAY') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'PROJECT';
ALTER TABLE `#__timeclock_projects` CHANGE `created` `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `checked_out_time` `checked_out_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `#__timeclock_projects` ADD `max_daily_hours` INT NOT NULL DEFAULT '0' AFTER `department_id`, ADD `min_daily_hours` INT NOT NULL DEFAULT '0' AFTER `max_daily_hours`, ADD `max_yearly_hours` INT NOT NULL DEFAULT '0' AFTER `min_daily_hours`;