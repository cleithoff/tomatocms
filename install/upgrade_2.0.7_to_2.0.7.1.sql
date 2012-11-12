-- core module
ALTER TABLE `core_page` DROP COLUMN `description`;
ALTER TABLE `core_page` DROP COLUMN `url`;
ALTER TABLE `core_page` DROP COLUMN `url_type`;
ALTER TABLE `core_page` DROP COLUMN `params`;
ALTER TABLE `core_page` CHANGE `name` `route` varchar(50);

UPDATE core_page SET route = 'core_index_index' WHERE route = 'home';
UPDATE core_privilege SET name = 'index', controller_name = 'dashboard' WHERE name = 'dashboard' AND controller_name = 'index';
UPDATE core_resource SET controller_name = 'dashboard' WHERE module_name = 'core' AND controller_name = 'index';

CREATE TABLE `core_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date` datetime NOT NULL,
  `uri` text NOT NULL,
  `module` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `line` int(11) NOT NULL,
  `message` text NOT NULL,
  `trace` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ad module
ALTER TABLE `ad_page_assoc` CHANGE `page_name` `route` varchar(50);
ALTER TABLE `ad_page_assoc` ADD `page_title` varchar(255);

-- category module
ALTER TABLE `category`  ADD `parent_id` int(11) default NULL;

-- menu module
ALTER TABLE menu DROP COLUMN `json_data`;

CREATE TABLE `menu_item` (
  `menu_item_id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `label` varchar(200) NOT NULL,
  `link` text NOT NULL,
  `left_id` int(11) NOT NULL,
  `right_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY  (`menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
