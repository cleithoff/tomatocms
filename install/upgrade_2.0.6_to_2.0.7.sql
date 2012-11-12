CREATE TABLE `core_dashboard` (
  `dashboard_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `layout` text NOT NULL,
  `is_default` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`dashboard_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;