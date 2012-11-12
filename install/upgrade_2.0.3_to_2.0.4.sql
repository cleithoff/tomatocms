CREATE TABLE `t_news_article_revision` (
  `revision_id` int(11) NOT NULL auto_increment,
  `article_id` int(11) NOT NULL,
  `category_id` smallint(6) default NULL,
  `title` varchar(255) default NULL,
  `sub_title` varchar(255) default NULL,
  `slug` varchar(255) default NULL,
  `description` text,
  `content` mediumtext,
  `author` varchar(255) default NULL,
  `icons` varchar(255) default NULL,
  `created_date` datetime default NULL,
  `created_user_id` int(11) default NULL,
  `created_user_name` varchar(255) default NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `idx_article_id` (`article_id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `t_multimedia_note` (
  `note_id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `left` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `content` varchar(200) default NULL,
  `is_active` tinyint(1) default '0',
  `user_id` int(11) default NULL,
  `user_name` varchar(100) default NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY  (`note_id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
