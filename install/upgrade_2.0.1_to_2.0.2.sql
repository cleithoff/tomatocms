CREATE TABLE `t_menu` (
  `menu_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  `json_data` text,
  `user_id` int(11) default NULL,
  `user_name` varchar(255) default NULL,
  `created_date` datetime default NULL,
  PRIMARY KEY  (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `t_tag` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `tag_text` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `t_tag_item_assoc` (
  `tag_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(30) NOT NULL,
  `route_name` varchar(200) NOT NULL,
  `details_route_name` varchar(200) NOT NULL,
  `params` varchar(255) default NULL,
  PRIMARY KEY  (`tag_id`,`item_id`,`item_name`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/* === Update pages === */
DELETE FROM `t_core_page` WHERE `name` = 'news_article';
DELETE FROM `t_core_page` WHERE `name` = 'news_category';

DELETE FROM `t_core_page` WHERE `name` = 'home';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('home','Homepage',NULL,'/','static',NULL,0);
DELETE FROM `t_core_page` WHERE `name` = 'news_article_category';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('news_article_category','View articles in category',NULL,'news/category/view/(\\d+)','regex','{\"category_id\": \"1\"}',2);
DELETE FROM `t_core_page` WHERE `name` = 'news_article_details';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('news_article_details','View article details',NULL,'news/article/view/(\\d+)/(\\d+)','regex','{\"category_id\": \"1\", \"article_id\": \"2\"}',1);
DELETE FROM `t_core_page` WHERE `name` = 'news_article_search';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('news_article_search', 'Search for articles', NULL, 'news/search', 'regex', '{}', '3');
DELETE FROM `t_core_page` WHERE `name` = 'multimedia_file_details';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('multimedia_file_details', 'View multimedia file details', NULL, 'multimedia/file/details/(\\d+)', 'regex', '{\"file_id\": \"1\"}', '4');
DELETE FROM `t_core_page` WHERE `name` = 'multimedia_set_details';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('multimedia_set_details', 'View set details', NULL, 'multimedia/set/details/(\\d+)', 'regex', '{\"set_id\": \"1\"}', '5');
DELETE FROM `t_core_page` WHERE `name` = 'tag_tag_details';
INSERT INTO `t_core_page`(name, title, description, url, url_type, params, ordering) VALUES ('tag_tag_details', 'List of items tagged by given tag', NULL, 'tag/details/(\\w+)/(\\d+)', 'regex', '{\"details_route_name\": \"1\", \"set_id\": \"2\"}', '6');

