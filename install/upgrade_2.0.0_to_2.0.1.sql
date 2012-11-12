/* ========== 2010/01/15 ==================================================== */
ALTER TABLE `t_comment` ADD `reply_to` INT(11) DEFAULT '0' AFTER `activate_date`;
ALTER TABLE `t_comment` ADD `depth` INT(11) DEFAULT '0' AFTER `activate_date`;
ALTER TABLE `t_comment` ADD `ordering` INT(11) DEFAULT '0' AFTER `activate_date`;
ALTER TABLE `t_comment` ADD `path` varchar(255) DEFAULT NULL AFTER `activate_date`;
ALTER TABLE `t_comment` ADD `web_site` varchar(255) DEFAULT NULL AFTER `full_name`;
ALTER TABLE `t_comment` DROP COLUMN `module`;
ALTER TABLE `t_comment` DROP COLUMN `object_id`;
ALTER TABLE `t_comment` DROP COLUMN `object_type`;
ALTER TABLE `t_comment` CHANGE `url` `page_url` varchar(255);
UPDATE `t_comment` SET `ordering` = `comment_id`;
UPDATE `t_comment` SET `path` = CONCAT(`comment_id`,'-');

ALTER TABLE `t_comment` DROP INDEX idx_latest_type;
ALTER TABLE `t_comment` ADD INDEX idx_latest(`page_url`, `is_active`, `ordering`);