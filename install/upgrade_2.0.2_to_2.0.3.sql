/**
 * If TomatoCMS installed on your server has version 2.0.3.1430 or older,
 * execute queries below
 */ 
ALTER TABLE `t_core_session` DROP COLUMN `access_time`;
ALTER TABLE `t_core_session` ADD `modified` INT;
ALTER TABLE `t_core_session` ADD `lifetime` INT;
