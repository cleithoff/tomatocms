USE MASTER
GO

--IF EXISTS (SELECT NAME FROM SYSDATABASES WHERE NAME ='tomato_cms')
--DROP DATABASE tomato_cms
--GO
--CREATE DATABASE tomato_cms
--GO
USE tomato_cms
GO

-- Table structure for table ad_banner
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='ad_banner' AND TYPE='U')
DROP TABLE ad_banner
CREATE TABLE ad_banner(
	banner_id int identity(1,1) NOT NULL,
	name nvarchar(200) NULL,
	text nvarchar(255) NULL,
	more_info ntext NULL,
	num_clicks int default 0,
	created_date varchar(19) NULL,
	start_date varchar(19) NULL,
	expired_date varchar(19) NULL,
	publish_up varchar(19) NULL,
	publish_down varchar(19) NULL,
	client_id int NULL,
	code ntext NULL,
	click_url ntext NULL,
	target nvarchar(11) check(target in ('new_tab','new_window','same_window')) default 'new_tab',
	format nvarchar(5) check(format in ('image','flash','text','html')) default 'image',
	image_url nvarchar(255) NULL,
	ordering int default 0,
	mode nvarchar(9) check(mode in ('unique','share','alternate')) default 'unique',
	timeout int default 15,
	status nvarchar(8) check(status in ('active','inactive')) default 'active',
	PRIMARY KEY (banner_id)
) 
CREATE INDEX idx_status ON ad_banner (status) 
GO

-- Table structure for table ad_click
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='ad_click' AND TYPE='U')
DROP TABLE ad_click
CREATE TABLE ad_click(
	banner_id int NOT NULL,
	zone_id int NOT NULL,
	page_id int NULL,
	clicked_date varchar(19) NOT NULL,
	ip nvarchar(30) NOT NULL,
	from_url ntext NULL
) 
GO

-- Table structure for table ad_client
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='ad_client' AND TYPE='U')
DROP TABLE ad_client
CREATE TABLE ad_client(
	client_id int identity(1,1) NOT NULL,
	name nvarchar(200) NOT NULL,
	email nvarchar(200) NULL,
	telephone nvarchar(50) NULL,
	address ntext NULL,
	created_date varchar(19) NOT NULL,
	PRIMARY KEY (client_id)
) 
GO

-- Table structure for table ad_page_assoc
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='ad_page_assoc' AND TYPE='U')
DROP TABLE ad_page_assoc
CREATE TABLE ad_page_assoc(
	ad_page_id int identity(1,1) NOT NULL,
	banner_id int NOT NULL,
	[route] nvarchar(50) NULL,
	zone_id int NOT NULL,
	page_url nvarchar(200) NULL,
	page_title nvarchar(255) NULL
	PRIMARY KEY (ad_page_id)
) 
GO

-- Table structure for table ad_page_assoc
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='ad_zone' AND TYPE='U')
DROP TABLE ad_zone
CREATE TABLE ad_zone(
	zone_id int identity(1,1) NOT NULL,
	width int NOT NULL,
	height int NOT NULL,
	name nvarchar(200) NOT NULL,
	description nvarchar(255) NULL,
	price nvarchar(255) NULL,
	PRIMARY KEY (zone_id)
) 
GO

-- Table structure for table category
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='category' AND TYPE='U')
DROP TABLE category
CREATE TABLE category(
	category_id int identity(1,1) NOT NULL,
	name nvarchar(255) NOT NULL,
	slug nvarchar(100) NOT NULL,
	meta ntext NULL,
	left_id int NOT NULL,
	right_id int NOT NULL,
	parent_id int NOT NULL default 0,
	num_views int NULL,
	is_active bit default 1,
	created_date varchar(19) NULL,
	modified_date varchar(19) NULL,
	user_id int NULL,
	language varchar(10) default NULL,
	PRIMARY KEY (category_id)
) 
CREATE INDEX idx_left_right ON category (left_id,right_id);
CREATE INDEX idx_language ON category (language);
GO

-- Table structure for table comment
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='comment' AND TYPE='U')
DROP TABLE comment
CREATE TABLE comment(
	comment_id int identity(1,1) NOT NULL,
	title nvarchar(255) NOT NULL,
	content ntext NOT NULL,
	full_name nvarchar(255) NULL,
	web_site nvarchar(255) NULL,
	email nvarchar(100) NOT NULL,
	user_id int NULL,
	user_name nvarchar(100) NULL,
	page_url nvarchar(255) NULL,
	ip nvarchar(40) NOT NULL,
	created_date varchar(19) NOT NULL,
	is_active numeric(3, 0) NOT NULL,
	activate_date varchar(19) NULL,
	path nvarchar(255) NULL,
	ordering int default 0,
	depth int default 0,
	reply_to int default 0,
	PRIMARY KEY (comment_id)
) 
CREATE INDEX idx_latest ON comment (page_url,is_active,ordering);
GO

-- Table structure for table menu_item
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='menu_item' AND TYPE='U')
DROP TABLE menu_item
CREATE TABLE menu_item (
  menu_item_id int identity(1,1) NOT NULL,
  item_id int NOT NULL,
  menu_id int NOT NULL,
  label nvarchar(200) NOT NULL,
  link text NOT NULL,
  left_id int NOT NULL,
  right_id int NOT NULL,
  parent_id int NOT NULL,
  PRIMARY KEY  (menu_item_id)
)
GO

-- Table structure for table core_dashboard
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_dashboard' AND TYPE='U')
DROP TABLE core_dashboard
CREATE TABLE core_dashboard (
  dashboard_id int identity(1,1) NOT NULL,
  user_id int NOT NULL,
  user_name nvarchar(50) NOT NULL,
  layout ntext NOT NULL,
  is_default int default 0,
  PRIMARY KEY  (dashboard_id)
);
CREATE INDEX idx_user_id ON core_dashboard (user_id);
CREATE INDEX idx_is_default ON core_dashboard (is_default);
GO

-- Table structure for table core_hook
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_hook' AND TYPE='U')
DROP TABLE core_hook
CREATE TABLE core_hook(
	hook_id int identity(1,1) NOT NULL,
	module nvarchar(100) NULL,
	name nvarchar(100) NOT NULL,
	description ntext NOT NULL,
	thumbnail ntext NULL,
	author nvarchar(255) NOT NULL,
	email nvarchar(100) NOT NULL,
	version nvarchar(20) NULL,
	license ntext NULL,
	PRIMARY KEY (hook_id)
) 
GO

IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_log' AND TYPE='U')
DROP TABLE core_log
CREATE TABLE core_log(
	log_id int identity(1,1) NOT NULL,
	created_date datetime NOT NULL,
	uri ntext NOT NULL,
	module nvarchar(255) NULL,
	controller nvarchar(255) NOT NULL,
	[action] nvarchar(255) NOT NULL,
	class nvarchar(255) NOT NULL,
	[file] nvarchar(255) NOT NULL,
	line int NOT NULL,
	[message] ntext NOT NULL,
	[trace] ntext NOT NULL,
	PRIMARY KEY (log_id)
) 
GO

-- Table structure for table core_module
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_module' AND TYPE='U')
DROP TABLE core_module
CREATE TABLE core_module(
	module_id int identity(1,1) NOT NULL,
	name nvarchar(100) NOT NULL,
	description ntext NOT NULL,
	thumbnail ntext NULL,
	author nvarchar(255) NULL,
	email nvarchar(100) NULL,
	version nvarchar(20) NULL,
	license ntext NULL,
	PRIMARY KEY (module_id)
) 
GO


-- Table structure for table core_page
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_page' AND TYPE='U')
DROP TABLE core_page
CREATE TABLE core_page(
	page_id int identity(1,1) NOT NULL,
	[route] nvarchar(50) NOT NULL,
	title nvarchar(100) NOT NULL,
	ordering smallint default 0,
	PRIMARY KEY (page_id)
) 
GO

-- Table structure for table core_plugin
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_plugin' AND TYPE='U')
DROP TABLE core_plugin
CREATE TABLE core_plugin(
	plugin_id int identity(1,1) NOT NULL,
	name nvarchar(100) NOT NULL,
	description ntext NOT NULL,
	thumbnail ntext NULL,
	author nvarchar(255) NULL,
	email nvarchar(100) NULL,
	version nvarchar(20) NULL,
	license ntext NULL,
	ordering smallint NULL,
	PRIMARY KEY (plugin_id)
) 
CREATE INDEX idx_ordering ON core_plugin (ordering);
GO

-- Table structure for table core_privilege
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_privilege' AND TYPE='U')
DROP TABLE core_privilege
CREATE TABLE core_privilege(
	privilege_id int identity(1,1) NOT NULL,
	name nvarchar(255) NOT NULL,
	description ntext NULL,
	module_name nvarchar(100) NULL,
	controller_name nvarchar(100) NULL,
	PRIMARY KEY (privilege_id)
) 
GO

-- Table structure for table core_request_log
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_request_log' AND TYPE='U')
DROP TABLE core_request_log
CREATE TABLE core_request_log(
	log_id int identity(1,1) NOT NULL,
	ip nvarchar(30) NOT NULL,
	agent nvarchar(255) NULL,
	browser nvarchar(100) NULL,
	version nvarchar(30) NULL,
	platform nvarchar(30) NULL,
	bot nvarchar(100) NULL,
	uri ntext NULL,
	full_url ntext NULL,
	refer_url ntext NULL,
	access_time varchar(19) NOT NULL,
	PRIMARY KEY (log_id)
) 
GO

-- Table structure for table core_resource
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_resource' AND TYPE='U')
DROP TABLE core_resource
CREATE TABLE core_resource(
	resource_id int identity(1,1) NOT NULL,
	description ntext NULL,
	parent_id nvarchar(50) NULL,
	module_name nvarchar(255) NULL,
	controller_name nvarchar(255) NULL,
	PRIMARY KEY (resource_id)
) 
CREATE INDEX idx_name_parent ON core_resource (parent_id);
GO

-- Table structure for table core_role
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_role' AND TYPE='U')
DROP TABLE core_role
CREATE TABLE core_role (
	role_id int identity(1,1) NOT NULL,
	name nvarchar(200) NOT NULL,
	description nvarchar(255) NOT NULL,
	locked numeric(3, 0) NOT NULL,
	PRIMARY KEY (role_id)
) 
CREATE INDEX name ON core_role (name);
GO

-- Table structure for table core_role_inheritance
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_role_inheritance' AND TYPE='U')
DROP TABLE core_role_inheritance
CREATE TABLE core_role_inheritance(
	child_id int NOT NULL,
	parent_id int NOT NULL,
	ordering int default 0,
	PRIMARY KEY (child_id,parent_id)
) 
CREATE INDEX parent_id ON core_role_inheritance (parent_id);
GO

-- Table structure for table core_rule
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_rule' AND TYPE='U')
DROP TABLE core_rule
CREATE TABLE core_rule(
	rule_id int identity(1,1) NOT NULL,
	obj_id int NOT NULL,
	obj_type nchar(4) check(obj_type in ('user','role')) default 'role',
	privilege_id int NULL,
	allow bit default 0,
	resource_name nvarchar(100) NULL,
	PRIMARY KEY (rule_id)
) 
GO

-- Table structure for table core_session
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_session' AND TYPE='U')
DROP TABLE core_session
CREATE TABLE core_session(
	session_id nvarchar(255) NOT NULL,
	data ntext NOT NULL,
	modified int NULL,
	lifetime int NULL,
	PRIMARY KEY (session_id)
) 
GO 

-- Table structure for table core_target
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_target' AND TYPE='U')
DROP TABLE core_target
CREATE TABLE core_target(
	target_id int identity(1,1) NOT NULL,
	target_module nvarchar(100) NULL,
	target_name nvarchar(255) NOT NULL,
	description ntext NULL,
	hook_module nvarchar(100) NULL,
	hook_name nvarchar(100) NOT NULL,
	hook_type nchar(6) check(hook_type in ('action','filter')) default 'action',
	PRIMARY KEY (target_id)
) 
GO

IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_translation' AND TYPE='U')
DROP TABLE core_translation
CREATE TABLE core_translation
(
	translation_id int identity(1,1) NOT NULL,
	item_id int NOT NULL,
	item_class nvarchar(100) NOT NULL,	
	source_item_id int NOT NULL,
	[language] nvarchar(10) DEFAULT NULL,
	source_language nvarchar(10) DEFAULT NULL,
	PRIMARY KEY  (translation_id)
)
CREATE INDEX idx_item ON core_translation (item_id,item_class);
CREATE INDEX idx_source_item ON core_translation (source_item_id,source_language);
GO

-- Table structure for table core_user
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_user' AND TYPE='U')
DROP TABLE core_user
CREATE TABLE core_user(
	user_id int identity(1,1) NOT NULL,
	role_id int NOT NULL,
	user_name nvarchar(100) NOT NULL,
	password nvarchar(50) NOT NULL,
	full_name nvarchar(100) NOT NULL,
	email nvarchar(255) NOT NULL,
	is_active bit NOT NULL default 0,
	created_date varchar(19) NULL,
	logged_in_date varchar(19) NULL,
	is_online bit default 0,
	PRIMARY KEY (user_id)
) 
CREATE INDEX user_name ON core_user (user_name);
CREATE INDEX email ON core_user (email);
GO

-- Table structure for table core_widget
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='core_widget' AND TYPE='U')
DROP TABLE core_widget
CREATE TABLE core_widget(
	widget_id int identity(1,1) NOT NULL,
	name nvarchar(100) NOT NULL,
	title nvarchar(100) NULL,
	module nvarchar(100) NOT NULL,
	description ntext NULL,
	thumbnail ntext NULL,
	author nvarchar(255) NULL,
	email nvarchar(100) NULL,
	version nvarchar(20) NULL,
	license ntext NULL,
	PRIMARY KEY (widget_id)
) 
GO

-- Table structure for table menu
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='menu' AND TYPE='U')
DROP TABLE menu
CREATE TABLE menu(
	menu_id int identity(1,1) NOT NULL,
	name nvarchar(255) NULL,
	description ntext NULL,
	user_id int NULL,
	user_name nvarchar(255) NULL,
	created_date varchar(19) NULL,
	language varchar(10) default NULL,
	PRIMARY KEY (menu_id)
) 
CREATE INDEX idx_language ON menu (language);
GO

-- Table structure for table multimedia_file
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='multimedia_file' AND TYPE='U')
DROP TABLE multimedia_file
CREATE TABLE multimedia_file(
	file_id int identity(1,1) NOT NULL,
	category_id int NULL,
	title nvarchar(255) NULL,
	slug nvarchar(255) NULL,
	description ntext NULL,
	content ntext NULL,
	image_square ntext NULL,
	image_thumbnail ntext NULL,
	image_small ntext NULL,
	image_crop ntext NULL,
	image_medium ntext NULL,
	image_large ntext NULL,
	image_original ntext NULL,
	num_views int default 0,
	created_date varchar(19) NULL,
	created_user int NULL,
	created_user_name nvarchar(255) NULL,
	allow_comment bit default 1,
	ordering int default 0,
	num_comments int default 0,
	url nvarchar(255) NULL,
	html_code ntext NULL,
	is_active bit default 1,
	file_type nchar(5) check(file_type in ('image','video','audio','game')) default 'image',
	PRIMARY KEY (file_id)
) 
CREATE INDEX idx_latest ON multimedia_file (is_active,file_type);
GO

-- Table structure for table multimedia_file_set_assoc
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='multimedia_file_set_assoc' AND TYPE='U')
DROP TABLE multimedia_file_set_assoc
CREATE TABLE multimedia_file_set_assoc(
	file_id int NOT NULL,
	set_id int NOT NULL,
	PRIMARY KEY (file_id,set_id)
) 
GO

-- Table structure for table multimedia_note
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='multimedia_note' AND TYPE='U')
DROP TABLE multimedia_note
CREATE TABLE multimedia_note(
	note_id int identity(1,1) NOT NULL,
	[file_id] int NOT NULL,
	[top] int NOT NULL,
	[left] int NOT NULL,
	width int NOT NULL,
	height int NOT NULL,
	content nvarchar(200) NULL,
	is_active bit default 0,
	[user_id] int NULL,
	[user_name] nvarchar(100) NULL,
	created_date varchar(19) NOT NULL,
	PRIMARY KEY (note_id)
) 
CREATE INDEX file_id ON multimedia_note (file_id);
GO

-- Table structure for table multimedia_set
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='multimedia_set' AND TYPE='U')
DROP TABLE multimedia_set
CREATE TABLE multimedia_set(
	set_id int identity(1,1) NOT NULL,
	slug nvarchar(255) NULL,
	title nvarchar(255) NOT NULL,
	description ntext NULL,
	created_date varchar(19) NULL,
	updated_date varchar(19) NULL,
	created_user_id int NULL,
	created_user_name nvarchar(255) NULL,
	num_views int default 0,
	num_comments int default 0,
	is_active bit default 1,
	image_square ntext NULL,
  	image_thumbnail ntext NULL,
  	image_small ntext NULL,
  	image_crop ntext NULL,
  	image_medium ntext NULL,
  	image_large ntext NULL,
	PRIMARY KEY (set_id)
) 
GO

-- Table structure for table news_article
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='news_article' AND TYPE='U')
DROP TABLE news_article
CREATE TABLE news_article(
	article_id int identity(1,1) NOT NULL,
	category_id smallint NULL,
	title nvarchar(255) NULL,
	sub_title nvarchar(255) NULL,
	slug nvarchar(255) NULL,
	description ntext NULL,
	content ntext NULL,
	author nvarchar(255) NULL,
	icons nvarchar(255) NULL,
	image_square varchar(255) NULL,
    image_thumbnail varchar(255) NULL,
    image_small varchar(255) NULL,
    image_crop varchar(255) NULL,
    image_medium varchar(255) NULL,
    image_large varchar(255) NULL,
	status nvarchar(8) check(status in ('deleted','draft','inactive','active')) default 'inactive',
	num_views int NULL,
	created_date varchar(19) NULL,
	created_user_id int NULL,
	created_user_name nvarchar(255) NULL,
	updated_date varchar(19) NULL,
	updated_user_id int NULL,
	updated_user_name nvarchar(255) NULL,
	activate_user_id int NULL,
	activate_user_name nvarchar(50) NULL,
	activate_date varchar(19) NULL,
	allow_comment numeric(3, 0) NULL,
	num_comments int default 0,
	is_hot bit default 0,
	ordering int default 0,
	show_date varchar(19) NULL,
	sticky bit NOT NULL default 0,
	[language] varchar(10) default NULL,
	PRIMARY KEY (article_id)
) 
CREATE INDEX idx_latest ON news_article (status,activate_date);
CREATE INDEX idx_latest_category ON news_article (category_id,status,activate_date);
CREATE INDEX idx_most_commented ON news_article (category_id,status,num_comments);
CREATE INDEX idx_most_viewed ON news_article (category_id,status,num_views);
CREATE INDEX idx_most_viewed_2 ON news_article (status,num_views);
CREATE INDEX idx_created_user ON news_article (created_user_id,article_id);
CREATE INDEX idx_language ON news_article ([language],article_id);
GO

-- Table structure for table news_article
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='news_article_category_assoc' AND TYPE='U')
DROP TABLE news_article_category_assoc
CREATE TABLE news_article_category_assoc(
	article_id int NOT NULL,
	category_id int NOT NULL,
	PRIMARY KEY (article_id,category_id)
) 
CREATE INDEX idx_category ON news_article_category_assoc (category_id);
GO

-- Table structure for table news_article_hot
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='news_article_hot' AND TYPE='U')
DROP TABLE news_article_hot
CREATE TABLE news_article_hot(
	article_id int NOT NULL,
	created_date varchar(19) NULL,
	ordering smallint NULL,
	PRIMARY KEY (article_id)
) 
CREATE INDEX idx_ordering ON news_article_hot (ordering);
GO

-- Table structure for table news_article_rate
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='news_article_rate' AND TYPE='U')
DROP TABLE news_article_rate
CREATE TABLE dbo.news_article_rate(
	article_id int NOT NULL,
	rate nchar(1) check(rate in ('1','2','3','4','5')) NOT NULL,
	ip nvarchar(40) NOT NULL,
	created_date varchar(19) NULL
)
GO

-- Table structure for table news_article_revision
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='news_article_revision' AND TYPE='U')
DROP TABLE news_article_revision
CREATE TABLE dbo.news_article_revision(
	revision_id int identity(1,1) NOT NULL,
	article_id int NOT NULL,
	category_id smallint NULL,
	title nvarchar(255) NULL,
	sub_title nvarchar(255) NULL,
	slug nvarchar(255) NULL,
	description ntext NULL,
	content ntext NULL,
	author nvarchar(255) NULL,
	icons nvarchar(255) NULL,
	created_date varchar(19) NULL,
	created_user_id int NULL,
	created_user_name nvarchar(255) NULL,
	PRIMARY KEY (revision_id)
) 
CREATE INDEX idx_article_id ON news_article_revision (article_id);
GO

-- Table structure for table news_article_revision
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='page' AND TYPE='U')
DROP TABLE page
CREATE TABLE dbo.page(
  page_id int identity(1,1) NOT NULL,
  name nvarchar(255) NOT NULL,
  slug nvarchar(100) NOT NULL,
  description ntext NOT NULL,
  content ntext NOT NULL,
  left_id int NOT NULL,
  right_id int NOT NULL,
  parent_id int default 0,
  num_views int default NULL,
  created_date varchar(19) default NULL,
  modified_date varchar(19) default NULL,
  [user_id] int default NULL,
  [language] varchar(10) default null,
  PRIMARY KEY  (page_id)
)
CREATE INDEX idx_left_right ON page (left_id, right_id);
CREATE INDEX idx_language ON page ([language]);
GO


-- Table structure for table poll_answer
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='poll_answer' AND TYPE='U')
DROP TABLE poll_answer
CREATE TABLE poll_answer(
	answer_id int identity(1,1) NOT NULL,
	question_id int NOT NULL,
	position int NOT NULL,
	title nvarchar(255) NOT NULL,
	content ntext NULL,
	is_correct bit default null,
	user_id int NOT NULL,
	num_views int NOT NULL default 0,
	PRIMARY KEY (answer_id)
) 
CREATE INDEX question_id ON poll_answer (question_id);
GO

-- Table structure for table poll_question
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='poll_question' AND TYPE='U')
DROP TABLE poll_question
CREATE TABLE dbo.poll_question(
	question_id int identity(1,1) NOT NULL,
	title nvarchar(255) NOT NULL,
	content nvarchar(255) NULL,
	created_date varchar(19) NOT NULL,
	start_date varchar(19) NOT NULL,
	end_date varchar(19) NULL,
	is_active numeric(3, 0) NOT NULL,
	multiple_options numeric(3, 0) NOT NULL,
	user_id int NOT NULL,
	num_views int NULL,
	PRIMARY KEY (question_id)
) 
GO

-- Table structure for table tag
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='tag' AND TYPE='U')
DROP TABLE tag
CREATE TABLE tag(
	tag_id int identity(1,1) NOT NULL,
	tag_text nvarchar(255) NOT NULL,
	PRIMARY KEY (tag_id)
) 
GO

-- Table structure for table tag_item_assoc
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='tag_item_assoc' AND TYPE='U')
DROP TABLE tag_item_assoc
CREATE TABLE tag_item_assoc(
	tag_id int NOT NULL,
	item_id int NOT NULL,
	item_name nvarchar(30) NOT NULL,
	route_name nvarchar(200) NOT NULL,
	details_route_name nvarchar(200) NOT NULL,
	params nvarchar(255) NULL,
	PRIMARY KEY (tag_id,item_id,item_name)
) 
CREATE INDEX tag_id ON tag_item_assoc (tag_id);
GO
 
-- Table structure for table mail_template
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='mail_template' AND TYPE='U')
DROP TABLE mail_template
CREATE TABLE mail_template(
	template_id int identity(1,1) NOT NULL,
	name nvarchar(100) NOT NULL,
	title nvarchar(200) NOT NULL,
	subject nvarchar(255) NOT NULL,
	body ntext NOT NULL,
	from_mail nvarchar(100) NOT NULL,
	from_name nvarchar(100) NOT NULL,
	reply_to_mail nvarchar(100) NOT NULL,
	reply_to_name nvarchar(100) NOT NULL,
	created_user_id int NOT NULL,
	locked bit default 0,
	PRIMARY KEY (template_id)
) 
CREATE INDEX idx_created_user ON mail_template (created_user_id);
GO

-- Table structure for table mail
IF EXISTS (SELECT NAME FROM SYSOBJECTS WHERE NAME='mail' AND TYPE='U')
DROP TABLE mail
CREATE TABLE mail(
	mail_id int identity(1,1) NOT NULL,
	template_id int NOT NULL,
	subject nvarchar(255) NOT NULL,
	content ntext NULL,
	created_user_id int NULL,
	from_mail nvarchar(100) NOT NULL,	 
	from_name nvarchar(100) NOT NULL,
	reply_to_mail nvarchar(100) default NULL,
	reply_to_name nvarchar(100) default NULL,
	to_mail nvarchar(100) NOT NULL,
	to_name nvarchar(100) NOT NULL,     
	status varchar(6) check(status in ('outbox','sent')) default NULL,	
	created_date varchar(19) NULL,
	sent_date  varchar(19) NULL,
	PRIMARY KEY (mail_id)
) 
CREATE INDEX idx_created_user ON mail (created_user_id);
GO

-- Insert data for table category
INSERT INTO category VALUES ('v2.0.0: Initial Version', 'v200--initial-version', '', 1, 2, 0, null, 1, '2010-08-04 11:44:16', null, 1, 'en_US');
INSERT INTO category VALUES ('v2.0.1: Install Wizard', 'v201--install-wizard', '', 3, 4, 0, null, 1, '2010-08-04 11:44:26', null, 1, 'en_US');
INSERT INTO category VALUES ('v2.0.2: Tag Module', 'v202--tag-module', '', 5, 6, 0, null, 1, '2010-08-04 11:44:57', '2010-08-04 11:46:39', 1, 'en_US');
INSERT INTO category VALUES ('v2.0.3: Improves Hook', 'v203--improves-hook', '', 7, 8, 0, null, 1, '2010-08-04 11:45:12', null, 1, 'en_US');
INSERT INTO category VALUES ('v2.0.4: Improves core, multimedia, news', 'v204--improves-core--multimedia--news', '', 9, 10, 0, null, 1, '2010-08-04 11:45:23', null, 1, 'en_US');
INSERT INTO category VALUES ('v2.0.5: Multiple Databases', 'v205--multiple-databases', '', 11, 12, 0, null, 1, '2010-08-04 11:45:31', null, 1, 'en_US');
INSERT INTO category VALUES ('v2.0.6: Mail Module', 'v206--mail-module', '', 13, 14, 0, null, 1, '2010-08-04 11:45:39', '2010-08-04 11:46:52', 1, 'en_US');
INSERT INTO category VALUES ('v2.0.7: SEO', 'v207--seo', '', 15, 18, 0, null, 1, '2010-08-04 11:45:51', null, 1, 'en_US');
INSERT INTO category VALUES ('v2.0.7.1: Page Module', 'v2071--page-module', '', 16, 17, 8, null, 1, '2010-08-04 11:46:28', null, 1, 'en_US');
GO

-- Insert data for table core_hook
INSERT INTO core_hook VALUES ('news', 'articlelinks', 'Show the categories and most recently articles links. Used to integrate with Link Provider', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_hook VALUES ('page', 'pagelinks', 'Show the pages links. Used to integrate with Link Provider', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
GO

-- Insert data for table core_module
INSERT INTO core_module VALUES ('ad', 'Manage banners', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_module VALUES ('category', 'Manage categories', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_module VALUES ('comment', 'Manage comments', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.1', 'free');
INSERT INTO core_module VALUES ('core', 'Core module. This module will be installed at the first time you install website', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.1', 'free');
INSERT INTO core_module VALUES ('mail', 'Manage mails', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.6', 'free');
INSERT INTO core_module VALUES ('menu', 'Manage menus', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_module VALUES ('multimedia', 'Multimedia module: Manage photos and clips', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_module VALUES ('news', 'Manage articles', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_module VALUES ('page', 'Manage static pages', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_module VALUES ('poll', 'Manage polls', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_module VALUES ('seo', 'Provide utilities which make your site is better for SEO', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_module VALUES ('tag', 'Manage tags', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_module VALUES ('upload', 'Upload file and manage uploaded files', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_module VALUES ('utility', 'Provide utilities. Most of utility widgets belong to this module', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
GO

-- Insert data for table core_page
INSERT INTO core_page VALUES ('core_index_index', 'Homepage', 0);
INSERT INTO core_page VALUES ('news_article_category', 'View articles in category', 6);
INSERT INTO core_page VALUES ('news_article_details', 'View article details', 5);
INSERT INTO core_page VALUES ('news_article_search', 'Search for articles', 4);
INSERT INTO core_page VALUES ('multimedia_file_details', 'View multimedia file details', 3);
INSERT INTO core_page VALUES ('multimedia_set_details', 'View photos set details', 2);
INSERT INTO core_page VALUES ('tag_tag_details', 'List of items tagged by given tag', 1);
INSERT INTO core_page VALUES ('page_page_details', 'View page details', 0);
GO

-- Insert data for table core_privilege
INSERT INTO core_privilege VALUES ('add', 'Create new banner', 'ad', 'banner');
INSERT INTO core_privilege VALUES ('delete', 'Delete a banner', 'ad', 'banner');
INSERT INTO core_privilege VALUES ('edit', 'Edit a banner', 'ad', 'banner');
INSERT INTO core_privilege VALUES ('list', 'View the list of banners', 'ad', 'banner');
INSERT INTO core_privilege VALUES ('add', 'Add new client', 'ad', 'client');
INSERT INTO core_privilege VALUES ('delete', 'Delete client', 'ad', 'client');
INSERT INTO core_privilege VALUES ('edit', 'Edit client information', 'ad', 'client');
INSERT INTO core_privilege VALUES ('list', 'View the list of clients', 'ad', 'client');
INSERT INTO core_privilege VALUES ('add', 'Create new zone', 'ad', 'zone');
INSERT INTO core_privilege VALUES ('delete', 'Delete a zone', 'ad', 'zone');
INSERT INTO core_privilege VALUES ('edit', 'Edit a zone', 'ad', 'zone');
INSERT INTO core_privilege VALUES ('list', 'View the list of zones', 'ad', 'zone');
INSERT INTO core_privilege VALUES ('add', 'Create new category', 'category', 'category');
INSERT INTO core_privilege VALUES ('delete', 'Delete a category', 'category', 'category');
INSERT INTO core_privilege VALUES ('edit', 'Edit a category', 'category', 'category');
INSERT INTO core_privilege VALUES ('list', 'View the list of categories', 'category', 'category');
INSERT INTO core_privilege VALUES ('order', 'Order categories', 'category', 'category');
INSERT INTO core_privilege VALUES ('activate', 'Activate comment', 'comment', 'comment');
INSERT INTO core_privilege VALUES ('add', 'Add/Reply comment', 'comment', 'comment');
INSERT INTO core_privilege VALUES ('delete', 'Delete comment', 'comment', 'comment');
INSERT INTO core_privilege VALUES ('edit', 'Edit comment', 'comment', 'comment');
INSERT INTO core_privilege VALUES ('list', 'View the list of comments', 'comment', 'comment');
INSERT INTO core_privilege VALUES ('thread', 'View the list comments in thread', 'comment', 'comment');
INSERT INTO core_privilege VALUES ('clear', 'Clear all cached data', 'core', 'cache');
INSERT INTO core_privilege VALUES ('config', 'Configure cache', 'core', 'cache');
INSERT INTO core_privilege VALUES ('delete', 'Delete cached item', 'core', 'cache');
INSERT INTO core_privilege VALUES ('list', 'View the list of cached items by id', 'core', 'cache');
INSERT INTO core_privilege VALUES ('app', 'Configure application''s settings', 'core', 'config');
INSERT INTO core_privilege VALUES ('add', 'Add setting', 'core', 'config');
INSERT INTO core_privilege VALUES ('delete', 'Delete settings', 'core', 'config');
INSERT INTO core_privilege VALUES ('edit', 'Edit setting', 'core', 'config');
INSERT INTO core_privilege VALUES ('list', 'View the list of settings', 'core', 'config');
INSERT INTO core_privilege VALUES ('update', 'Update setting', 'core', 'config');
INSERT INTO core_privilege VALUES ('index', 'Administrator Dashboard', 'core', 'dashboard');
INSERT INTO core_privilege VALUES ('config', 'Config hook', 'core', 'hook');
INSERT INTO core_privilege VALUES ('install', 'Install hook', 'core', 'hook');
INSERT INTO core_privilege VALUES ('list', 'View the list of hooks', 'core', 'hook');
INSERT INTO core_privilege VALUES ('uninstall', 'Uninstall hook', 'core', 'hook');
INSERT INTO core_privilege VALUES ('upload', 'Upload new hook', 'core', 'hook');
INSERT INTO core_privilege VALUES ('add', 'Add item to language file', 'core', 'language');
INSERT INTO core_privilege VALUES ('delete', 'Delete item from language file', 'core', 'language');
INSERT INTO core_privilege VALUES ('edit', 'Edit language file', 'core', 'language');
INSERT INTO core_privilege VALUES ('list', 'View the list of language files for module/widget', 'core', 'language');
INSERT INTO core_privilege VALUES ('new', 'Create new language file', 'core', 'language');
INSERT INTO core_privilege VALUES ('update', 'Update item in language file', 'core', 'language');
INSERT INTO core_privilege VALUES ('upload', 'Upload new language package', 'core', 'language');
INSERT INTO core_privilege VALUES ('delete', 'Delete error', 'core', 'log');
INSERT INTO core_privilege VALUES ('list', 'List errors', 'core', 'log');
INSERT INTO core_privilege VALUES ('install', 'Install module', 'core', 'module');
INSERT INTO core_privilege VALUES ('list', 'View the list of modules', 'core', 'module');
INSERT INTO core_privilege VALUES ('uninstall', 'Uninstall module', 'core', 'module');
INSERT INTO core_privilege VALUES ('upload', 'Upload new module', 'core', 'module');
INSERT INTO core_privilege VALUES ('add', 'Create new page', 'core', 'page');
INSERT INTO core_privilege VALUES ('edit', 'Edit page', 'core', 'page');
INSERT INTO core_privilege VALUES ('layout', 'Update layout of page', 'core', 'page');
INSERT INTO core_privilege VALUES ('list', 'View the list of pages', 'core', 'page');
INSERT INTO core_privilege VALUES ('ordering', 'Update order of pages', 'core', 'page');
INSERT INTO core_privilege VALUES ('index', 'Manage permalinks', 'core', 'permalink');
INSERT INTO core_privilege VALUES ('config', 'Config plugin', 'core', 'plugin');
INSERT INTO core_privilege VALUES ('install', 'Install plugin', 'core', 'plugin');
INSERT INTO core_privilege VALUES ('list', 'View the list of plugins', 'core', 'plugin');
INSERT INTO core_privilege VALUES ('ordering', 'Update order of plugins', 'core', 'plugin');
INSERT INTO core_privilege VALUES ('uninstall', 'Uninstall plugin', 'core', 'plugin');
INSERT INTO core_privilege VALUES ('upload', 'Upload new plugin', 'core', 'plugin');
INSERT INTO core_privilege VALUES ('add', 'Add action', 'core', 'privilege');
INSERT INTO core_privilege VALUES ('delete', 'Delete action', 'core', 'privilege');
INSERT INTO core_privilege VALUES ('list', 'View the list of actions', 'core', 'privilege');
INSERT INTO core_privilege VALUES ('add', 'Add resource', 'core', 'resource');
INSERT INTO core_privilege VALUES ('delete', 'Delete resource', 'core', 'resource');
INSERT INTO core_privilege VALUES ('add', 'Add role', 'core', 'role');
INSERT INTO core_privilege VALUES ('delete', 'Delete role', 'core', 'role');
INSERT INTO core_privilege VALUES ('list', 'View the list of roles', 'core', 'role');
INSERT INTO core_privilege VALUES ('lock', 'Lock/unlock role', 'core', 'role');
INSERT INTO core_privilege VALUES ('role', 'Set rules for role', 'core', 'rule');
INSERT INTO core_privilege VALUES ('user', 'Set rules for user', 'core', 'rule');
INSERT INTO core_privilege VALUES ('add', 'Apply hook for target', 'core', 'target');
INSERT INTO core_privilege VALUES ('list', 'View the list of targets', 'core', 'target');
INSERT INTO core_privilege VALUES ('remove', 'Remove hook from target', 'core', 'target');
INSERT INTO core_privilege VALUES ('activate', 'Activate template', 'core', 'template');
INSERT INTO core_privilege VALUES ('editskin', 'Edit skin of template', 'core', 'template');
INSERT INTO core_privilege VALUES ('list', 'View the list of templates', 'core', 'template');
INSERT INTO core_privilege VALUES ('skin', 'Set skin for current template', 'core', 'template');
INSERT INTO core_privilege VALUES ('activate', 'Activate/deactivate an user', 'core', 'user');
INSERT INTO core_privilege VALUES ('add', 'Add user', 'core', 'user');
INSERT INTO core_privilege VALUES ('changepass', 'Update password', 'core', 'user');
INSERT INTO core_privilege VALUES ('edit', 'Update user information', 'core', 'user');
INSERT INTO core_privilege VALUES ('list', 'View the list of users', 'core', 'user');
INSERT INTO core_privilege VALUES ('install', 'Install widget', 'core', 'widget');
INSERT INTO core_privilege VALUES ('list', 'View the list of widgets', 'core', 'widget');
INSERT INTO core_privilege VALUES ('uninstall', 'Uninstall widget', 'core', 'widget');
INSERT INTO core_privilege VALUES ('upload', 'Upload new widget', 'core', 'widget');
INSERT INTO core_privilege VALUES ('server', 'Config mail server', 'mail', 'config');
INSERT INTO core_privilege VALUES ('add', 'Add new mail template', 'mail', 'template');
INSERT INTO core_privilege VALUES ('delete', 'Delete the mail template', 'mail', 'template');
INSERT INTO core_privilege VALUES ('edit', 'Edit the mail template', 'mail', 'template');
INSERT INTO core_privilege VALUES ('list', 'View the list of templates', 'mail', 'template');
INSERT INTO core_privilege VALUES ('list', 'View the list of mails', 'mail', 'mail');
INSERT INTO core_privilege VALUES ('send', 'Send mails', 'mail', 'mail');
INSERT INTO core_privilege VALUES ('build', 'Build new menu', 'menu', 'menu');
INSERT INTO core_privilege VALUES ('edit', 'Edit a menu', 'menu', 'menu');
INSERT INTO core_privilege VALUES ('list', 'View the list of menus', 'menu', 'menu');
INSERT INTO core_privilege VALUES ('activate', 'Activate a file', 'multimedia', 'file');
INSERT INTO core_privilege VALUES ('add', 'Create new file', 'multimedia', 'file');
INSERT INTO core_privilege VALUES ('delete', 'Delete a file', 'multimedia', 'file');
INSERT INTO core_privilege VALUES ('edit', 'Edit given file', 'multimedia', 'file');
INSERT INTO core_privilege VALUES ('editor', 'Image editor', 'multimedia', 'file');
INSERT INTO core_privilege VALUES ('activate', 'Activate a note', 'multimedia', 'note');
INSERT INTO core_privilege VALUES ('delete', 'Delete a note', 'multimedia', 'note');
INSERT INTO core_privilege VALUES ('edit', 'Edit given note', 'multimedia', 'note');
INSERT INTO core_privilege VALUES ('list', 'View list of notes', 'multimedia', 'note');
INSERT INTO core_privilege VALUES ('upload', 'Upload new photo', 'multimedia', 'photo');
INSERT INTO core_privilege VALUES ('activate', 'Activate a set', 'multimedia', 'set');
INSERT INTO core_privilege VALUES ('add', 'Create new set', 'multimedia', 'set');
INSERT INTO core_privilege VALUES ('delete', 'Delete a set', 'multimedia', 'set');
INSERT INTO core_privilege VALUES ('edit', 'Edit given set', 'multimedia', 'set');
INSERT INTO core_privilege VALUES ('activate', 'Activate article', 'news', 'article');
INSERT INTO core_privilege VALUES ('add', 'Add new article', 'news', 'article');
INSERT INTO core_privilege VALUES ('delete', 'Delete article', 'news', 'article');
INSERT INTO core_privilege VALUES ('edit', 'Edit article', 'news', 'article');
INSERT INTO core_privilege VALUES ('emptytrash', 'Empty the trash', 'news', 'article');
INSERT INTO core_privilege VALUES ('hot', 'Manage hot articles', 'news', 'article');
INSERT INTO core_privilege VALUES ('list', 'View the list of articles', 'news', 'article');
INSERT INTO core_privilege VALUES ('preview', 'Preview article', 'news', 'article');
INSERT INTO core_privilege VALUES ('add', 'Add new revision', 'news', 'revision');
INSERT INTO core_privilege VALUES ('delete', 'Delete revision', 'news', 'revision');
INSERT INTO core_privilege VALUES ('list', 'View the list of article revisions', 'news', 'revision');
INSERT INTO core_privilege VALUES ('restore', 'Restore revision', 'news', 'revision');
INSERT INTO core_privilege VALUES ('add', 'Add new page', 'page', 'page');
INSERT INTO core_privilege VALUES ('delete', 'Delete page', 'page', 'page');
INSERT INTO core_privilege VALUES ('edit', 'Edit page', 'page', 'page');
INSERT INTO core_privilege VALUES ('list', 'List pages', 'page', 'page');
INSERT INTO core_privilege VALUES ('order', 'Order pages', 'page', 'page');
INSERT INTO core_privilege VALUES ('activate', 'Activate a poll', 'poll', 'poll');
INSERT INTO core_privilege VALUES ('add', 'Create new poll', 'poll', 'poll');
INSERT INTO core_privilege VALUES ('delete', 'Delete a poll', 'poll', 'poll');
INSERT INTO core_privilege VALUES ('edit', 'Update a poll', 'poll', 'poll');
INSERT INTO core_privilege VALUES ('list', 'View the list of polls', 'poll', 'poll');
INSERT INTO core_privilege VALUES ('index', 'View reports', 'seo', 'ganalytic');
INSERT INTO core_privilege VALUES ('add', 'Add site', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('addsitemap', 'Add sitemap', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('delete', 'Delete site', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('deletesitemap', 'Delete sitemap', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('details', 'View site details', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('list', 'List of sites', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('verify', 'Verify site', 'seo', 'gwebmaster');
INSERT INTO core_privilege VALUES ('add', 'Add new link to sitemap', 'seo', 'sitemap');
INSERT INTO core_privilege VALUES ('delete', 'Remove link from sitemap', 'seo', 'sitemap');
INSERT INTO core_privilege VALUES ('index', 'View sitemap details', 'seo', 'sitemap');
INSERT INTO core_privilege VALUES ('index', 'Get backlinks, indexed pages, rank', 'seo', 'toolkit');
INSERT INTO core_privilege VALUES ('add', 'Create new tag', 'tag', 'tag');
INSERT INTO core_privilege VALUES ('delete', 'Delete a tag', 'tag', 'tag');
INSERT INTO core_privilege VALUES ('list', 'View the list of tags', 'tag', 'tag');
INSERT INTO core_privilege VALUES ('browse', 'Browse uploaded files', 'upload', 'file');
INSERT INTO core_privilege VALUES ('upload', 'Upload file to server', 'upload', 'file');
GO

-- Insert data for table core_resource
INSERT INTO core_resource VALUES ('Manage banners', null, 'ad', 'banner');
INSERT INTO core_resource VALUES ('Manage clients', null, 'ad', 'client');
INSERT INTO core_resource VALUES ('Manage zones', null, 'ad', 'zone');
INSERT INTO core_resource VALUES ('Manage categories', null, 'category', 'category');
INSERT INTO core_resource VALUES ('Manage user comments', null, 'comment', 'comment');
INSERT INTO core_resource VALUES ('Manage cache', null, 'core', 'cache');
INSERT INTO core_resource VALUES ('Manage settings', null, 'core', 'config');
INSERT INTO core_resource VALUES ('Administrator section', null, 'core', 'dashboard');
INSERT INTO core_resource VALUES ('Manage hooks', null, 'core', 'hook');
INSERT INTO core_resource VALUES ('Manage languages', null, 'core', 'language');
INSERT INTO core_resource VALUES ('Errors log', null, 'core', 'log');
INSERT INTO core_resource VALUES ('Manage modules', null, 'core', 'module');
INSERT INTO core_resource VALUES ('Manage pages', null, 'core', 'page');
INSERT INTO core_resource VALUES ('Manage permalinks', null, 'core', 'permalink');
INSERT INTO core_resource VALUES ('Manage plugins', null, 'core', 'plugin');
INSERT INTO core_resource VALUES ('Manage actions to resource', null, 'core', 'privilege');
INSERT INTO core_resource VALUES ('Manage resources', null, 'core', 'resource');
INSERT INTO core_resource VALUES ('Manage roles', null, 'core', 'role');
INSERT INTO core_resource VALUES ('Manage rules', null, 'core', 'rule');
INSERT INTO core_resource VALUES ('Manage hook targets', null, 'core', 'target');
INSERT INTO core_resource VALUES ('Manage templates', null, 'core', 'template');
INSERT INTO core_resource VALUES ('Manage users', null, 'core', 'user');
INSERT INTO core_resource VALUES ('Manage widgets', null, 'core', 'widget');
INSERT INTO core_resource VALUES ('Config mail', null, 'mail', 'config');
INSERT INTO core_resource VALUES ('Manage mail templates', null, 'mail', 'template');
INSERT INTO core_resource VALUES ('Manage mails', null, 'mail', 'mail');
INSERT INTO core_resource VALUES ('Manage menu', null, 'menu', 'menu');
INSERT INTO core_resource VALUES ('Manage files', null, 'multimedia', 'file');
INSERT INTO core_resource VALUES ('Manage notes', null, 'multimedia', 'note');
INSERT INTO core_resource VALUES ('Manage photos', null, 'multimedia', 'photo');
INSERT INTO core_resource VALUES ('Manage sets', null, 'multimedia', 'set');
INSERT INTO core_resource VALUES ('Manage articles', null, 'news', 'article');
INSERT INTO core_resource VALUES ('Manage revisions', null, 'news', 'revision');
INSERT INTO core_resource VALUES ('List pages', null, 'page', 'page');
INSERT INTO core_resource VALUES ('Manage polls', null, 'poll', 'poll');
INSERT INTO core_resource VALUES ('Google Analytic reports', null, 'seo', 'ganalytic');
INSERT INTO core_resource VALUES ('Google Web Master tool', null, 'seo', 'gwebmaster');
INSERT INTO core_resource VALUES ('Sitemap builder', null, 'seo', 'sitemap');
INSERT INTO core_resource VALUES ('SEO Toolkit', null, 'seo', 'toolkit');
INSERT INTO core_resource VALUES ('Manage tags', null, 'tag', 'tag');
INSERT INTO core_resource VALUES ('Manage uploaded files', null, 'upload', 'file');
GO

-- Insert data for table core_role
INSERT INTO core_role VALUES ('admin', 'Administrator', 1);
GO

-- Insert data for table core_rule
INSERT INTO core_rule VALUES (1, 'role', null, 1, null);
GO

-- Insert data for table core_target
INSERT INTO core_target VALUES ('core', 'Core_LinkProvider', 'Link Provider. Show the useful link provided by other modules and use in somewhere else', 'news', 'articlelinks', 'filter');
INSERT INTO core_target VALUES ('core', 'Core_LinkProvider', 'Link Provider. Show the useful link provided by other modules and use in somewhere else', 'page', 'pagelinks', 'filter');
GO

-- Insert data for table core_user
/* 
 *  user name: admin
 *  password: tomato
 */
INSERT INTO core_user VALUES (1, 'admin', '006f87892f47ef9aa60fa5ed01a440fb', 'Administrator', 'admin@email.com', 1, null, null, 0);
GO

-- Insert data for table core_widget
INSERT INTO core_widget VALUES ('zone', 'Banner', 'ad', 'Show the banner at given zone', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('comment', 'Latest comments', 'comment', 'Show the latest comments', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.1', 'free');
INSERT INTO core_widget VALUES ('dashboardcomment', 'Show latest comments', 'comment', 'Show the latest comments in Dashboard', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_widget VALUES ('disqus', 'Disqus comments', 'comment', 'Show comments powered by Disqus', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.3', 'free');
INSERT INTO core_widget VALUES ('dashboardsystem', 'System information', 'core', 'Show system information', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_widget VALUES ('dashboardversion', 'Check whether there is newer version or not', 'core', 'Check new version', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_widget VALUES ('html', 'HTML content', 'core', 'Show HTML content', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('itomato', 'iTomato', 'core', 'User can drag and drop widgets on page', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('login', 'Login', 'core', 'Show the login form', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.6', 'free');
INSERT INTO core_widget VALUES ('skinselector', 'Skin selector', 'core', 'User can change skin of website', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('menu', 'Menu', 'menu', 'Show menu', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_widget VALUES ('filesets', 'File sets', 'multimedia', 'Show list of sets which file belongs to', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_widget VALUES ('latestsets', 'Latest sets', 'multimedia', 'Show latest sets', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_widget VALUES ('player', 'Latest video clips', 'multimedia', 'Show the latest video clips', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('slideshow', 'Slideshow', 'multimedia', 'Slideshow consist of latest photos', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('breadcrumb', 'Breadcrumb', 'news', 'Show breadcrumb to given category', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('categories', 'Show all categories and latest articles', 'news', 'Show all categories and latest articles for each category', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('categorytree', 'Category Tree', 'news', 'List of categories and link to them', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('dashboardarticle', 'Show latest articles', 'news', 'Show the latest articles in Dashboard', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_widget VALUES ('hotest', 'Hotest', 'news', 'Show the hotest articles', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('mostviewed', 'Most viewed', 'news', 'Show the most viewed articles', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('newer', 'Newer articles', 'news', 'Show the articles that are newer than current being viewed article', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('newest', 'Newest articles', 'news', 'Show the newest articles', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('older', 'Older articles', 'news', 'Show the articles that are older than current article', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('searchbox', 'Search Box', 'news', 'News search box', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_widget VALUES ('siblingcategory', 'Sibling category', 'news', 'Show the sibling categories and latest articles from them', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('sticky', 'Sticky articles', 'news', 'Show the sticky articles of given category', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('breadcrumb', 'Breadcrumb', 'page', 'Show breadcrumb to given page', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('pagetree', 'Page Tree', 'page', 'Show list of pages', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_widget VALUES ('vote', 'Poll', 'poll', 'Show a poll', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('dashboardbacklink', 'Show Google backlink', 'seo', 'Show the backlink to your site taken from Google. This widget can be only used in Dashboard', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.7', 'free');
INSERT INTO core_widget VALUES ('googler', 'Welcome Googler', 'seo', 'Show message when user visit website from Google', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.3', 'free');
INSERT INTO core_widget VALUES ('tagcloud', 'Tag Cloud', 'tag', 'Show tag cloud associated with items which have the same type: article, photo, for example', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_widget VALUES ('tags', 'Tags', 'tag', 'Show tags associated with given object', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.2', 'free');
INSERT INTO core_widget VALUES ('countdown', 'Countdown', 'utility', 'Show a countdown to given event', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('feed', 'Feed', 'utility', 'Show entries from RSS channel', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('flickr', 'Flickr photos', 'utility', 'Show the photos from Flickr', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.3', 'free');
INSERT INTO core_widget VALUES ('socialshare', 'Share via social networks', 'utility', 'Share links via some popular social networks', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('textresizer', 'Text Resizer', 'utility', 'Allows user to select the smaller or larger font', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.3', 'free');
INSERT INTO core_widget VALUES ('twitter', 'Update from Twitter', 'utility', 'Show latest updates from Twitter account', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
INSERT INTO core_widget VALUES ('youtubeplayer', 'YouTube clip', 'utility', 'Show the clip from YouTube', '', 'TomatoCMS Core Team', 'core@tomatocms.com', '2.0.0', 'free');
GO

-- Insert data for table mail_template
INSERT INTO mail_template VALUES ('forgot_password', 'Forgot password', 'Link to reset password', 'Hi %user_name%,<br />' + char(13) + char(10) + 'Please click on the link below to reset the password:' + char(13) + char(10) + '<a href="%reset_link%">%reset_link%</a>', 'YourMail@Domain.com', 'Administrator', 'YourMail@Domain.com', 'Administrator', 1, 1);
INSERT INTO mail_template VALUES ('new_password', 'New password', 'New password', 'Hi %user_name%, <br />' + char(13) + char(10) + '' + char(13) + char(10) + 'You can use the following account to access <a href="%link%">our website</a><br />:' + char(13) + char(10) + '- Username: %user_name%<br />' + char(13) + char(10) + '- Password: %new_password%<br />' + char(13) + char(10) + 'Do NOT forget to change the password after logging in the website.', 'YourMail@Domain.com', 'Administrator', 'YourMail@Domain.com', 'Administrator', 1, 1);
GO

-- Insert data for table menu
INSERT INTO menu VALUES ('Top menu', 'The top menu in header', 1, 'admin', '2010-08-04 15:22:19', 'en_US');

-- Insert data for table multimedia_file
INSERT INTO multimedia_file VALUES (0,'Link Provider','link-provider','',NULL,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_square.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_general.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_small.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_crop.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_medium.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_large.png',NULL,0,'2010-08-04 15:25:31',1,'admin',1,0,0,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be.png','',1,'image');
INSERT INTO multimedia_file VALUES (0,'Permalink management','permalink-management','',NULL,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c_square.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c_general.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c_small.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c_crop.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c_medium.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c_large.png',NULL,0,'2010-08-04 15:33:58',1,'admin',1,0,0,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ed6840c.png','',1,'image');
INSERT INTO multimedia_file VALUES (0,'Errors loggings','errors-loggings','',NULL,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901_square.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901_general.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901_small.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901_crop.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901_medium.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901_large.png',NULL,0,'2010-08-04 15:33:58',1,'admin',1,0,0,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925ef5f901.png','',1,'image');
INSERT INTO multimedia_file VALUES (0,'Categories management','categories-management','',NULL,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb_square.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb_general.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb_small.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb_crop.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb_medium.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb_large.png',NULL,0,'2010-08-04 15:33:58',1,'admin',1,0,0,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5925f265dfb.png','',1,'image');
INSERT INTO multimedia_file VALUES (0,'Bulk actions for articles management','bulk-actions-for-articles-management','',NULL,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85_square.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85_general.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85_small.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85_crop.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85_medium.png','http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85_large.png',NULL,0,'2010-08-04 15:38:28',1,'admin',1,0,0,'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c592700f2a85.png','',1,'image');
GO

-- Insert data for table multimedia_file_set_assoc
INSERT INTO multimedia_file_set_assoc VALUES (1, 1);
INSERT INTO multimedia_file_set_assoc VALUES (2, 1);
INSERT INTO multimedia_file_set_assoc VALUES (3, 1);
INSERT INTO multimedia_file_set_assoc VALUES (4, 1);
INSERT INTO multimedia_file_set_assoc VALUES (5, 1);
GO

-- Insert data for table multimedia_set
INSERT INTO multimedia_set VALUES ('tomatocms-2-0-7-1', 'TomatoCMS 2.0.7.1', '', '2010-08-04 15:25:31', null, 1, 'admin', 0, 0, 1, 'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_general.png', 'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_medium.png', 'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_crop.png', 'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_small.png', 'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_square.png', 'http://localhost/tomatocms/upload/multimedia/admin/2010/08/4c5923f5d35be_large.png');

-- Insert data for table news_article 
INSERT INTO news_article VALUES (1,'TomatoCMS 2.0.0 released on 4th, January, 2010','','tomatocms-200-released-on-4th--january--2010','<p>This is the first release TomatoCMS.</p>','<p>This is first release of TomatoCMS on Jan, 4th, 2010. Although we have developed it for 7-8 months ago.</p>','TomatoCMS Core Team','',NULL,NULL,NULL,NULL,NULL,NULL,'active',0,'2010-08-04 11:57:39',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 11:57:47',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (2,'Install Wizard','','install-wizard','<p>TomatoCMS 2.0.1 allows you to install easily using Install Wizard</p>','<p>Now, Install Wizard only take three steps to install TomatoCMS. You can install it in root web directory or its sub-directory.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_large.png','active',0,'2010-08-04 12:01:36',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 12:01:52',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (2,'Supports nested comments','','supports-nested-comments','<p>TomatoCMS 2.0.1 improves comment module. One of important improvements is supports nested comments.</p>','<p>Below is the list of new features in comment module:</p>&nbsp;<p>- Support nested, unlimitted level comments</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_large.png" alt="" /></p>&nbsp;<p>- Shows avatar of commenters</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91eba043_medium.png" alt="" /></p>&nbsp;<p>- Allows users to use some simple HTML tags in comment</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f922b8d9f_large.png" alt="" /></p>&nbsp;<p>- User can reply any comment in thread</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f924ba96a_large.png" alt="" /></p>&nbsp;<p>- Administrator can apply hooks for filtering the content of comments:<br style="padding: 0px; margin: 0px;" />Below is two examples which was built already in TomatoCMS. The first one replaces special characters with emotion icons as follow</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91fc0224_large.png" alt="" /></p>&nbsp;<p>And the second one formats comment in pre-defined programming language style which is very useful for programmers'' blogs:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f920b9ccc_large.png" alt="" /></p>&nbsp;<p>- Prevents spams<br style="padding: 0px; margin: 0px;" />At this version, we made an attempt at preventing spams by using the service provided by Akismet.<br style="padding: 0px; margin: 0px;" />To use this, you have to register an free Akismet API key.</p>','TomatoCMS Core Team','{"image"}','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_large.png','active',1,'2010-08-04 12:25:35',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 12:25:44',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (2,'Update informer','','update-informer','<p>In backend, user will receive the message that informs new version is available if any.</p>','<p>Below is the screenshot:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_large.png','active',1,'2010-08-04 13:05:03',1,'admin','2010-08-04 13:06:28',1,'admin',1,'admin','2010-08-04 13:05:12',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (3,'Tag system','','tag-system','<p>User can tag not only the articles but also photos, sets</p>','<p>(1): Make a suggestion based on user input<br style="padding: 0px; margin: 0px;" />(2): Show list of current selected tags</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_medium.png" alt="" /></p>&nbsp;<p>- Tag managements:</p>&nbsp;<p>(1): Search for tags<br style="padding: 0px; margin: 0px;" />(2): Create new tag<br style="padding: 0px; margin: 0px;" />(3): List of tags. You can click on tag to remove it.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59043a74af3_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>In addition, this version provide two widgets for tagging.<br style="padding: 0px; margin: 0px;" />The first one named&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">tag</span>&nbsp;show list of tags for given item (item here can be article, photo or photo set):</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59043c73bc5_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>and the second one is&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">Tag Cloud</span>&nbsp;which show list of tags:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59043e7307f_medium.png" alt="" /></p>','TomatoCMS Core Team','{"image"}','http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_large.png','active',0,'2010-08-04 13:12:27',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:12:43',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (3,'Front-end for multimedia module','','front-end-for-multimedia-module','<p>In this version, you can view photo, clip on frontend section&nbsp;</p>&nbsp;<p>(Try to click on photo in slideshow on homepage)</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>There are new widgets in this version which are:<br style="padding: 0px; margin: 0px;" />- filesets: Show list of set that current photo/clip belongs to:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590550ab985_medium.png" alt="" /></p>&nbsp;<p>- latestsets: Show the latest sets</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590551ab9bd_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Beside this, we improved backend strongly for more friendly:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59054db68a3_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_large.png','active',0,'2010-08-04 13:16:40',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:16:50',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (3,'Menu builder','','menu-builder','<p>The menu is most important component of website. In this version, we released an initial version for menu module.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_large.png','active',0,'2010-08-04 13:18:29',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:18:40',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (3,'Ability of customizing URLs','','ability-of-customizing-urls','<p>In 2.0.1 version and earlier, the default URL of article is http://site.com/news/article/view/1/1/</p>&nbsp;<p>Now, with 2.0.2 version, developer can customize URLs of article, category, photo pages. This is better for SEO.</p>','<p>In the following figure, I configured article page''s URL to format of&nbsp;<br style="padding: 0px; margin: 0px;" />/news/article/view/<span style="font-weight: bold; padding: 0px; margin: 0px;">ID_Of_Category</span>/<span style="font-weight: bold; padding: 0px; margin: 0px;">ID_Of_Article</span>-<span style="font-weight: bold; padding: 0px; margin: 0px;">Slug_Of_Article</span>.html</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_large.png','active',0,'2010-08-04 13:20:45',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:20:53',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'Uses more Zend Framework components','','uses-more-zend-framework-components','<p>In this 2.0.3 version, we replace some libraries with Zend Framework components.</p>','<p>- Remove PEAR JSON Service from Twitter widget and use Zend_Json<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Use Zend_Paginator and Zend_View_Helper_PaginationControl to render the paginator instead of PEAR_Pager<br style="padding: 0px; margin: 0px;" /></p>&nbsp;<p>- Use Zend_Application to create and bootstrap the application<br style="padding: 0px; margin: 0px;" /></p>&nbsp;<p>- Fixed to work with Zend Framework 1.10.0.<br style="padding: 0px; margin: 0px;" />Also, Zend Framework 1.10.0 is attached in download package.</p>','TomatoCMS Core Team','',NULL,NULL,NULL,NULL,NULL,NULL,'active',0,'2010-08-04 13:23:20',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:23:29',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'Improves core module','','improves-core-module','<p>We improve core module including supporting database prefix, more easy to call translator.</p>','<p>- Support database prefix. You can set it when install TomatoCMS.</p>&nbsp;<p>- Now, it''s more easy to translate language data in modules and widgets without caring about current module, widget.</p>&nbsp;<p>- With 2.0.3 version, you can install and apply hook in module level.<br style="padding: 0px; margin: 0px;" /></p>&nbsp;<p>Install hook:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Apply hook:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59083207931_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_large.png','active',0,'2010-08-04 13:28:16',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:28:27',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'New SEO module','','new-seo-module','<p>This 2.0.3 version built new SEO module which provide SEO utilities.</p>','<p>Currently, it comes with</p>&nbsp;<p><br style="padding: 0px; margin: 0px;" />- New hook named&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">gatracker</span>&nbsp;that allows you to insert Google Analytic tracker code.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d582381_medium.png" alt="" /></p>&nbsp;<p>- New widget named&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">googler</span>&nbsp;that show configurable welcome message if user visit your site from Google''s searching result</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_large.png','active',0,'2010-08-04 13:30:58',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:31:06',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'Supports RTL languages','','supports-rtl-languages','<p>2.0.3 version supports RTL languages as Arabic, Iranian, ... for both front-end and back-end sections</p>','<p>In front-end:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>and back-end:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e5176a6_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_large.png','active',0,'2010-08-04 13:35:20',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:35:33',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'New widgets','','new-widgets','<p>There''re two built-in widgets in this version: Flickr and TextResizer</p>','<p>- The first widget is&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">Flickr</span>&nbsp;that show latest Flickr images from given account.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_medium.png" alt="" /></p>&nbsp;<p>- The second one is&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">TextResizer</span>&nbsp;that allows user to set the smaller or larger font size for pages</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_large.png','active',0,'2010-08-04 13:37:16',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:37:23',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'Offline mode','','offline-mode','<p>This 2.0.3 version allows you to set your website in offline mode</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae6ea4aa_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>You can configure this message when install using Install Wizard or in back-end:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_large.png','active',0,'2010-08-04 13:39:17',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:39:26',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (4,'Session lifetime and debug mode','','session-lifetime-and-debug-mode','<p>2.0.3 version allows user to set the session lifetime and debug mode for the website.</p>','<p>- Session lifetime:<br style="padding: 0px; margin: 0px;" />Now, users don''t have to remove expired session manually. Users can set session lifetime (in seconds) in&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">app.ini</span>&nbsp;(TomatoCMS_Installed_Folder/app/config/app.ini):</p>&nbsp;<p>[web]</p>&nbsp;<p>session_lifetime = "3600"</p>&nbsp;<p>- Debug mode:<br style="padding: 0px; margin: 0px;" />This version allows developer to set the website in debug mode by setting following option in&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">app.ini</span>:</p>&nbsp;<p>[web]<br style="padding: 0px; margin: 0px;" />debug = "true"</p>','TomatoCMS Core Team','',NULL,NULL,NULL,NULL,NULL,NULL,'active',0,'2010-08-04 13:41:27',1,'admin','2010-08-04 13:42:34',1,'admin',1,'admin','2010-08-04 13:41:36',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (5,'Import sample data','','import-sample-data','<p>In a step of Install Wizard, we add feature allowing user to import sample data.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Ability of uploading language package in *.zip which consist of language files for modules, widgets:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590c733414a_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_large.png','active',0,'2010-08-04 13:45:51',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:46:02',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (5,'Water mask and Image Editor','','water-mask-and-image-editor','<p>With TomatoCMS 2.0.4, you can use water masking features and edit your image online.</p>','<p>The uploader provides the ability of inserting water mask:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590d2bf3dec_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Image Editor with some basic actions as crop, rotate:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590d29f31c1_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>When preview a image, you can add note to it, and these notes will be managed at backend:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_large.png','active',0,'2010-08-04 13:49:19',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:49:29',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (5,'Browses and inserts uploaded images','','browses-and-inserts-uploaded-images','<p>TomatoCMS 2.0.4 allows you to browse and insert images that were uploaded before when adding/editing the article.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- You can preview article:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590e116b2e6_medium.png" alt="" /></p>&nbsp;<p>- Create article revision and you can restore or delete it whenever you want :</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590e126e5e9_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_large.png','active',0,'2010-08-04 13:53:54',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:54:00',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (6,'Supports multiple databases','','supports-multiple-databases','<p>TomatoCMS 2.0.5 supports multiple databases.</p>','<p>The list of supported databases are:</p>&nbsp;<p>- MySQL with both native (mysql)&nbsp;and PDO driver (pdo, pdo_mysql)</p>&nbsp;<p>- SQL Server 2005 with sqlsrv driver</p>&nbsp;<p>- PostgreSQL with pgsql driver</p>','TomatoCMS Core Team','',NULL,NULL,NULL,NULL,NULL,NULL,'active',0,'2010-08-04 13:56:41',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:56:50',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (7,'Allows to choose the charset','','allows-to-choose-the-charset','<p>TomatoCMS 2.0.6 allows user to choose the charset at Install Wizard or configure it in back-end</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_large.png','active',0,'2010-08-04 13:59:28',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 13:59:36',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (7,' Shows all available thumbnail images after uploading the image','','shows-all-available-thumbnail-images-after-uploading-the-image','<p>In adding/editing article pages, you can select the thumbnail from available thumbnails after uploading the image</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_large.png','active',0,'2010-08-04 14:02:15',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:02:22',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (7,'Compress CSS and Javascript','','compress-css-and-javascript','<p>To improve page load time, TomatoCMS 2.0.6 allows website to compress CSS, Javascript in both front-end and back-end sections</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5910ceef4ad_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>All CSS, Javascript files will be combined in only one file and this file will be cached on server:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Also,<strong>&nbsp;</strong><span style="font-weight: bold; padding: 0px; margin: 0px;">content of every pages were also compressed</span>:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cd02efd_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>You can clear compress cache files in back-end:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591175025a5_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_large.png','active',0,'2010-08-04 14:07:12',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:07:19',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (7,'Caches entire pages','','caches-entire-pages','<p>TomatoCMS 2.0.6 has the ability of caching entire pages</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Once the page was cached, you will see the cached time by viewing the source of page:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ef1bbd5_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_large.png','active',0,'2010-08-04 14:09:34',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:09:41',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (7,'New mail module','','new-mail-module','<p>This version comes with new built-in module: mail powered by Zend_Mail component</p>','<p>The mail module supports sending mail with PHP mail() function or SMTP server:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>It provides the&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">mail template management system</span>:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591281ec93d_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>You can&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">send emails</span>&nbsp;to all registered users or all users belonging to given group:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591283e065c_medium.png" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>With this version, you can&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">reset the password</span>. The reset password will be sent via email using mail module:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591280eb966_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_large.png','active',0,'2010-08-04 14:13:02',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:13:10',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (7,'Login widget','','login-widget','<p>2.0.6 version provides login widget.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_medium.png" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_square.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_general.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_small.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_crop.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_medium.png','http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_large.png','active',0,'2010-08-04 14:14:50',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:17:04',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (8,'New back-end interface','','new-back-end-interface','<p>Install Wizard and all back-end pages now have new interface</p>','<p>- Install Wizard:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149047cec_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- Login pages:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- In back-end, TomatoCMS uses mega menu and all of core tasks were moved to System menu.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149e40ec4_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>You also can find modules'' tasks under Module menu:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5914933ae8e_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- We also made many pages more friendly and more easy to use.<br style="padding: 0px; margin: 0px;" />For instance, in most of pages coming from core module, you can make a module filter instead of scrolling though the long page like before. Below is just some examples:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />Privileges management:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149b3d768_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Widgets management:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149842caf_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_large.jpg','active',0,'2010-08-04 14:24:27',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:24:35',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (8,'Customize Dashboard','','customize-dashboard','<p>In TomatoCMS 2.0.6 and earlier versions, you can not customize your Dashboard which were used to display all of modules'' tasks. From 2.0.7, the ability of customizing dashboard come true.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160d4a32e_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>We also created some useful Dashboard widgets:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Latest Articles: Show most of recently articles. You can activate or deactivate the article right now if you want</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916095177d_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- Latest Comments: Show most of latest comments. Of course, you can activate or deactivate the comment if you have the permission to do.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160c4aead_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- Google backlinks: Show top of back links taken from Google</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160a4c993_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916a21131a_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916a21131a_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916a21131a_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916a21131a_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916a21131a_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916a21131a_large.jpg','active',0,'2010-08-04 14:27:43',1,'admin','2010-08-04 14:28:46',1,'admin',1,'admin','2010-08-04 14:27:52',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (8,'SEO module: Sitemap management','','seo-module--sitemap-management','<p>TomatoCMS 2.0.7 allows you to manage sitemap easily</p>','<p>You can see the all the link from the sitemap:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f503b43_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Of course, you can remove the links from sitemap as well as add link to sitemap:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_large.jpg','active',0,'2010-08-04 14:30:48',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:30:54',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (8,'SEO module: Toolkit','','seo-module--toolkit','<p>The aim of this toolkit is to get the backlinks, indexed pages and rank from most popular search engines such as Google, Yahoo, and Bing. You can get these data for not only your current site but also any website you want.</p>','<p>Below is the screenshots of using this toolkit to get Google page rank, Alexa rank, its indexed pages:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>and its back links:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591770075ff_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>The toolkit also show the total number of page indexed and back links.</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_large.jpg','active',0,'2010-08-04 14:33:25',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:33:37',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (8,'SEO module: Interactive with Google Web Master','','seo-module--interactive-with-google-web-master','<p>Using your Google account, TomatoCMS 2.0.7 allows you to interactive with Google Web Master easily.</p>','<p>The list of functions include:</p>&nbsp;<p><strong>List of sites you registry with Google Web Master </strong></p>&nbsp;<p>You also can add other site or remove certain site from the list.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59187c7bac3_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p><strong>Sitemap management </strong></p>&nbsp;<p>When viewing the site details, TomatoCMS shows all its sitemaps. The tool allows you to remove or add other sitemap</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59187f686d2_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p><strong>List of keywords </strong></p>&nbsp;<p>You can see the list of keywords which Google found on the site. More than that, you can make a keyword filter: see the internal, external or all keywords.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p><strong>Verify site </strong></p>&nbsp;<p>Final function is verify your site. You can verify by using one of two methods:</p>&nbsp;<p>- Use HTML meta tag</p>&nbsp;<p>- Or create HTML page.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59188268f4e_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_large.jpg','active',0,'2010-08-04 14:39:44',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:39:53',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (8,'SEO module: Show Google Analytic reports','','seo-module--show-google-analytic-reports','<p>From 2.0.7 version, TomatoCMS can show Google Analytic reports. Of course, you have to make authentication using your Google account before using this tool.</p>','<p>You can select the website and the time range you want to get the report data.<br style="padding: 0px; margin: 0px;" />Finally, you can see most popular reports inside TomatoCMS:<br style="padding: 0px; margin: 0px;" />- Visit, Unique visitors, Page views, Time on site and Bounce rate<br style="padding: 0px; margin: 0px;" />- Browser, Operating system and Screen resolution<br style="padding: 0px; margin: 0px;" />- Traffic source and keywords<br style="padding: 0px; margin: 0px;" />- Top viewed pages and top exit pages<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />TomatoCMS gets all data at once time, therefore if you switch between reports, you don''t have to wait for data loading.<br style="padding: 0px; margin: 0px;" />You can move the mouse over the points on chart to see the details data.<br style="padding: 0px; margin: 0px;" />And other interesting point, we used Javascript, not Flash, to render the chart. It is more easy for us to improve and customize the chart later.<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />Here is some screenshots:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Visit report:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>- Time on site report:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5919987429d_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_large.jpg','active',0,'2010-08-04 14:41:55',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:42:02',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Protects from CSRF attack','','protects-from-csrf-attack','<p>TomatoCMS 2.0.7.1 protects your site from CSRF attacks.</p>','<p>Below is the list of TomatoCMS CSRF vulnerabilities and all of them were fixed:</p>&nbsp;<p>- Change Administrator Password</p>&nbsp;<p>- Create Admin User</p>&nbsp;<p>- Deactivate User</p>&nbsp;<p>- Logout The Administrator</p>','TomatoCMS Core Team','',NULL,NULL,NULL,NULL,NULL,NULL,'active',0,'2010-08-04 14:44:28',1,'admin','2010-08-26 08:59:18',1,'admin',1,'admin','2010-08-04 14:44:35',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Install Wizard (core module)','','improvements--install-wizard-core-module','<p>From 2.0.7 and earlier versions, there is no way to select the language at Install Wizard as well as switch to other language in back-end. In this 2.0.7.1 version, at the first step of Install Wizard, you can choose the language from one of built-in languages or upload a new one.</p>','<p>After uploading the package, you can choose uploaded language.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_large.jpg','active',0,'2010-08-04 14:46:16',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:46:24',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Banners management (ad module)','','improvements--banners-management-ad-module','<p>In 2.0.7 and earlier versions, it''s not convenient for user to add banners to certain page. Some users even don''t know how to do next after checking selecting page check boxes.</p>','<p>Also, if you understand how it works, you have to put the page/categories URL manually.&nbsp;<br style="padding: 0px; margin: 0px;" />Well, it was not good and not friendly.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591b1f8f5a7_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>With TomatoCMS 2.0.7.1, the interface is more clear, more friendly and it''s easy to select the page and zone for banner by using the Link Provider:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_large.jpg','active',0,'2010-08-04 14:49:00',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:49:09',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Categories management (category module)','','improvements--categories-management-category-module','<p>In category module, most of users don''t understand what "Include child category" mean while editing the category. Also, if you want to change the order of categories tree, you have to perform updating each categories.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591bd1c6169_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>With 2.0.7.1, we remove the "Include child category" option and also, you can drag and drop categories (and its children) to other position. Beside, it allows you to collapse or expand the category like a tree node!</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_large.jpg','active',0,'2010-08-04 14:51:19',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:51:25',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Menu builder (menu module)','','improvements--menu-builder-menu-module','<p>That''s what we think the most of user are waiting for.&nbsp;</p>','<p>The current menu builder is not good:</p>&nbsp;<p>- The interface is not easy to understand and use</p>&nbsp;<p>- We have to input the link of menu item manually !!!</p>&nbsp;<p>- The source code is dirty with many Javascript classes and probably will be difficult to maintain</p>&nbsp;<p>In other words, there are many questions you will have if you use this before:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5a177a3_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>&nbsp;</p>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Well, the inconvenient things above will be come the past!</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">With next 2.0.7.1, the Menu builder allows you to:</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- Input the link easily with Link Provider (again,the Link Provider is powerful)</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- The user interface is clear and friendly. You can drag/drop, collapse/expand menu items.</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- The label and link are editable in place</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- If you look at the source code (from SVN), it''s very clear. Many Javascripts classes used before are removed including the treeview, interface''s javascript libraries.</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Ok, let''s take a look at it:</div>&nbsp;<p>Well, the inconvenient things above will be come the past!<br />With next 2.0.7.1, the Menu builder allows you to:</p>&nbsp;<p>- Input the link easily with Link Provider (again, the Link Provider is powerful)</p>&nbsp;<p>- The user interface is clear and friendly. You can drag/drop, collapse/expand menu items.</p>&nbsp;<p>- The label and link are editable in place</p>&nbsp;<p>- If you look at the source code (from SVN), it''s very clear. Many Javascripts classes used before are removed including the treeview, interface''s javascript libraries.</p>&nbsp;<p><br />Ok, let''s take a look at it:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_large.jpg','active',0,'2010-08-04 14:54:24',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:54:31',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Support both Client and AuthSub token authentication methods','','improvements--support-both-client-and-authsub-token-authentication-methods','<p>TomatoCMS 2.0.7 allows you to interactive with Google Web Master and see the Google Analytic reports. But each time you use these feature, you have to make Google authentication using your Google account and grant the application permission to get the data from Google.&nbsp;</p>','<p>Technically, it uses AuthSub token authentication method.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_medium.jpg" alt="" /></p>&nbsp;<p>2.0.7.1 version allows you to use one more authentication method: ClientLogin.</p>&nbsp;<p>Just open the SEO module''s configuration file which you can find at&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">/application/modules/seo/config/config.ini</span>&nbsp;and add the following section to the top of this file:</p>&nbsp;<p>[google]<br style="padding: 0px; margin: 0px;" />username = "Your_Google_Account"<br style="padding: 0px; margin: 0px;" />password = "Yout_Google_Password"</p>&nbsp;<p>&nbsp;</p>&nbsp;<p>That''s all. Now you can use SEO module''s features without logging in to Google.</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_large.jpg','active',0,'2010-08-04 14:57:09',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 14:57:17',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Pages management (core module)','','improvements--pages-management-core-module','<p>If you are using TomatoCMS 2.0.7 or older version, creating pages is not easy task.</p>','<p>For instance, if you are not programmer, maybe you don''t understand what the page name and its URL mean, especially in case the URL contains the regular expression:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db56e1d8_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>But, it''s not the final challenge. It''s not easy for you to create a new page because at this page, you face with unfamiliar concept such as parameters, their names and orders:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db76de62_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>We also know about your problem. So, in next 2.0.7.1 version, creating page is very simple:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db3723ce_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Listing of pages also make you comfortable. You can edit, delete or edit layout if you want:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_large.jpg','active',1,'2010-08-04 14:59:51',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 15:00:10',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Errors logging (core module)','','errors-logging-core-module','<p>In 2.0.7.1 version, all the errors will be logged and you can see them in back-end</p>','<p>The current TomatoCMS version has ability of handling errors.&nbsp;We redesign the page that shows the error:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6812dc1_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>Also, if you create new template for TomatoCMS, you don''t have to create error page for your template.<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />In 2.0.7.1 version, all the errors will be logged and you can see them in back-end:<br style="padding: 0px; margin: 0px;" />You can delete one or many log items at the same time.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_large.jpg','active',0,'2010-08-04 15:02:16',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 15:02:23',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Static pages (page module)','','static-pages-page-module','<p>TomatoCMS 2.0.7.1 provides new module named "page" for this purpose. You can create static page with HTML content.</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591ece1e89c_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>If you have more than one page, you can drag/drop page (and its children) to other one. The pages listing works like a tree that allows you to collapse/expand the page.</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_large.jpg','active',0,'2010-08-04 15:04:08',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 15:04:15',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Improvements: Bulk actions for articles management (news module)','','improvements--bulk-actions-for-articles-management-news-module','<p>In 2.0.7, you can filter the list of articles by its status: All, Activated, Not activated, Draft or Trash. With 2.0.7.1, it allows you to perform same action to multiple articles at the same time</p>','<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_medium.jpg" alt="" /></p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_large.jpg','active',0,'2010-08-04 15:05:46',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 15:05:59',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Link Provider (core module)','','link-provider-core-module','<p>Link Provider is the machine that allows user to integrate the links and use it anywhere in the back-end.</p>','<p>Before 2.0.7.1, there are many cases we have to input the link manually:<br style="padding: 0px; margin: 0px;" />- Set the link for banners (ad module)<br style="padding: 0px; margin: 0px;" />- Set the link for menu items (menu module)<br style="padding: 0px; margin: 0px;" />- Set the link for sitemap item (seo module)</p>&nbsp;<p>&nbsp;</p>&nbsp;<p>It was very bad if you have to create a menu with more than 10 items, for example.</p>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Well, in next 2.0.7.1, all tasks above are easy. Just click on the link provided by Link Provider and that''s all. All links are organized by different groups.</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Also, you can integrate your link provided by your own module to this machine easily.</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Below is the sample when I add the link to menu item:</div>&nbsp;<p>It was very bad if you have to create a menu with more than 10 items, for example.Well, in next 2.0.7.1, all tasks above are easy. Just click on the link provided by Link Provider and that''s all. All links are organized by different groups.</p>&nbsp;<p>Also, you can integrate your link provided by your own module to this machine easily.</p>&nbsp;<p>Below is the sample when I add the link to menu item:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_large.jpg','active',0,'2010-08-04 15:08:13',1,'admin',NULL,NULL,NULL,1,'admin','2010-08-04 15:08:20',0,0,0,0,NULL,0,'en_US');INSERT INTO news_article VALUES (9,'Permalink management (core module)','','permalink-management-core-module','<p>TomatoCMS supports beautiful, friendly URLs that are good for SEO. But by default the URL has the format of /module/action/id/another_id which should be more descriptive. Now, this dream are coming true because in 2.0.7.1, you can configure the URL for all front-end URLs easily.</p>','<p>Below is the screenshot that give you idea how it look like:</p>&nbsp;<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_medium.jpg" alt="" /></p>&nbsp;<p>&nbsp;</p>&nbsp;<p>&nbsp;</p>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">For each page, you can use the default URL, one of pre-define URLs or customize by yourself.</div>&nbsp;<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">In case you use the custom URL, you don''t have to worry about wrong parameters because the tool suggests the valid parameter based on your input characters.</div>&nbsp;<p>For each page, you can use the default URL, one of pre-define URLs or customize by yourself. In case you use the custom URL, you don''t have to worry about wrong parameters because the tool suggests the valid parameter based on your input characters.</p>','TomatoCMS Core Team','','http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_square.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_general.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_small.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_crop.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_medium.jpg','http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_large.jpg','active',1,'2010-08-04 15:09:47',1,'admin','2010-08-26 09:26:20',1,'admin',1,'admin','2010-08-04 15:10:01',0,0,0,0,NULL,0,'en_US');;
-- Insert data for table news_article_category_assoc 
INSERT INTO news_article_category_assoc VALUES (1, 1);
INSERT INTO news_article_category_assoc VALUES (2, 2);
INSERT INTO news_article_category_assoc VALUES (3, 2);
INSERT INTO news_article_category_assoc VALUES (4, 2);
INSERT INTO news_article_category_assoc VALUES (5, 3);
INSERT INTO news_article_category_assoc VALUES (6, 3);
INSERT INTO news_article_category_assoc VALUES (7, 3);
INSERT INTO news_article_category_assoc VALUES (8, 3);
INSERT INTO news_article_category_assoc VALUES (9, 4);
INSERT INTO news_article_category_assoc VALUES (10, 4);
INSERT INTO news_article_category_assoc VALUES (11, 4);
INSERT INTO news_article_category_assoc VALUES (12, 4);
INSERT INTO news_article_category_assoc VALUES (13, 4);
INSERT INTO news_article_category_assoc VALUES (14, 4);
INSERT INTO news_article_category_assoc VALUES (15, 4);
INSERT INTO news_article_category_assoc VALUES (16, 5);
INSERT INTO news_article_category_assoc VALUES (17, 5);
INSERT INTO news_article_category_assoc VALUES (18, 5);
INSERT INTO news_article_category_assoc VALUES (19, 6);
INSERT INTO news_article_category_assoc VALUES (20, 7);
INSERT INTO news_article_category_assoc VALUES (21, 7);
INSERT INTO news_article_category_assoc VALUES (22, 7);
INSERT INTO news_article_category_assoc VALUES (23, 7);
INSERT INTO news_article_category_assoc VALUES (24, 7);
INSERT INTO news_article_category_assoc VALUES (25, 7);
INSERT INTO news_article_category_assoc VALUES (26, 8);
INSERT INTO news_article_category_assoc VALUES (27, 8);
INSERT INTO news_article_category_assoc VALUES (28, 8);
INSERT INTO news_article_category_assoc VALUES (29, 8);
INSERT INTO news_article_category_assoc VALUES (30, 8);
INSERT INTO news_article_category_assoc VALUES (31, 8);
INSERT INTO news_article_category_assoc VALUES (32, 9);
INSERT INTO news_article_category_assoc VALUES (33, 9);
INSERT INTO news_article_category_assoc VALUES (34, 9);
INSERT INTO news_article_category_assoc VALUES (35, 9);
INSERT INTO news_article_category_assoc VALUES (36, 9);
INSERT INTO news_article_category_assoc VALUES (37, 9);
INSERT INTO news_article_category_assoc VALUES (38, 9);
INSERT INTO news_article_category_assoc VALUES (39, 9);
INSERT INTO news_article_category_assoc VALUES (40, 9);
INSERT INTO news_article_category_assoc VALUES (41, 9);
INSERT INTO news_article_category_assoc VALUES (42, 9);
INSERT INTO news_article_category_assoc VALUES (43, 9);
GO

-- Insert data for table news_article_hot
INSERT INTO news_article_hot VALUES (36, '2010-08-04 14:54:24', 1000);
INSERT INTO news_article_hot VALUES (38, '2010-08-04 14:59:51', 1000);
INSERT INTO news_article_hot VALUES (40, '2010-08-04 15:04:08', 1000);
INSERT INTO news_article_hot VALUES (42, '2010-08-04 15:08:13', 1000);
INSERT INTO news_article_hot VALUES (43, '2010-08-04 15:09:47', 1000);
GO

-- Insert data for table tag
INSERT INTO tag VALUES ('v2.0.0');
INSERT INTO tag VALUES ('v2.0.1');
INSERT INTO tag VALUES ('v2.0.2');
INSERT INTO tag VALUES ('v2.0.3');
INSERT INTO tag VALUES ('v2.0.4');
INSERT INTO tag VALUES ('v2.0.5');
INSERT INTO tag VALUES ('v2.0.6');
INSERT INTO tag VALUES ('v2.0.7');
INSERT INTO tag VALUES ('v2.0.7.1');
INSERT INTO tag VALUES ('install-wizard');
INSERT INTO tag VALUES ('comment-module');
INSERT INTO tag VALUES ('update');
INSERT INTO tag VALUES ('tag-module');
INSERT INTO tag VALUES ('multimedia-module');
INSERT INTO tag VALUES ('menu-module');
INSERT INTO tag VALUES ('seo');
INSERT INTO tag VALUES ('permalink');
INSERT INTO tag VALUES ('hook');
INSERT INTO tag VALUES ('widget');
INSERT INTO tag VALUES ('seo-module');
INSERT INTO tag VALUES ('rtl');
INSERT INTO tag VALUES ('news-module');
INSERT INTO tag VALUES ('upload-module');
INSERT INTO tag VALUES ('core-module');
INSERT INTO tag VALUES ('cache');
INSERT INTO tag VALUES ('mail-module');
INSERT INTO tag VALUES ('dashboard');
INSERT INTO tag VALUES ('dashboard-widget');
INSERT INTO tag VALUES ('security');
INSERT INTO tag VALUES ('ad-module');
INSERT INTO tag VALUES ('link-provider');
INSERT INTO tag VALUES ('category-module');
INSERT INTO tag VALUES ('page-module');
GO

-- Insert data for table tag_item_assoc
INSERT INTO tag_item_assoc VALUES (1, 1, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:1');
INSERT INTO tag_item_assoc VALUES (2, 2, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:2');
INSERT INTO tag_item_assoc VALUES (2, 3, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:3');
INSERT INTO tag_item_assoc VALUES (2, 4, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:4');
INSERT INTO tag_item_assoc VALUES (3, 5, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:5');
INSERT INTO tag_item_assoc VALUES (3, 6, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:6');
INSERT INTO tag_item_assoc VALUES (3, 7, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:7');
INSERT INTO tag_item_assoc VALUES (3, 8, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:8');
INSERT INTO tag_item_assoc VALUES (4, 9, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:9');
INSERT INTO tag_item_assoc VALUES (4, 10, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:10');
INSERT INTO tag_item_assoc VALUES (4, 11, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:11');
INSERT INTO tag_item_assoc VALUES (4, 12, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:12');
INSERT INTO tag_item_assoc VALUES (4, 13, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:13');
INSERT INTO tag_item_assoc VALUES (4, 14, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:14');
INSERT INTO tag_item_assoc VALUES (4, 15, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:15');
INSERT INTO tag_item_assoc VALUES (5, 16, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:16');
INSERT INTO tag_item_assoc VALUES (5, 17, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:17');
INSERT INTO tag_item_assoc VALUES (5, 18, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:18');
INSERT INTO tag_item_assoc VALUES (6, 19, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:19');
INSERT INTO tag_item_assoc VALUES (7, 20, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:20');
INSERT INTO tag_item_assoc VALUES (7, 21, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:21');
INSERT INTO tag_item_assoc VALUES (7, 22, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:22');
INSERT INTO tag_item_assoc VALUES (7, 23, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:23');
INSERT INTO tag_item_assoc VALUES (7, 24, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:24');
INSERT INTO tag_item_assoc VALUES (7, 25, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:25');
INSERT INTO tag_item_assoc VALUES (8, 26, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:26');
INSERT INTO tag_item_assoc VALUES (8, 27, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:27');
INSERT INTO tag_item_assoc VALUES (8, 28, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:28');
INSERT INTO tag_item_assoc VALUES (8, 29, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:29');
INSERT INTO tag_item_assoc VALUES (8, 30, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:30');
INSERT INTO tag_item_assoc VALUES (8, 31, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:31');
INSERT INTO tag_item_assoc VALUES (9, 1, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:1');
INSERT INTO tag_item_assoc VALUES (9, 1, 'set_id', 'multimedia_set_details', 'multimedia_tag_set', 'set_id:1');
INSERT INTO tag_item_assoc VALUES (9, 2, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:2');
INSERT INTO tag_item_assoc VALUES (9, 3, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:3');
INSERT INTO tag_item_assoc VALUES (9, 4, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:4');
INSERT INTO tag_item_assoc VALUES (9, 5, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:5');
INSERT INTO tag_item_assoc VALUES (9, 32, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:32');
INSERT INTO tag_item_assoc VALUES (9, 33, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:33');
INSERT INTO tag_item_assoc VALUES (9, 34, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:34');
INSERT INTO tag_item_assoc VALUES (9, 35, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:35');
INSERT INTO tag_item_assoc VALUES (9, 36, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:36');
INSERT INTO tag_item_assoc VALUES (9, 37, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:37');
INSERT INTO tag_item_assoc VALUES (9, 38, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:38');
INSERT INTO tag_item_assoc VALUES (9, 39, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:39');
INSERT INTO tag_item_assoc VALUES (9, 40, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:40');
INSERT INTO tag_item_assoc VALUES (9, 41, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:41');
INSERT INTO tag_item_assoc VALUES (9, 42, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:42');
INSERT INTO tag_item_assoc VALUES (9, 43, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:43');
INSERT INTO tag_item_assoc VALUES (10, 2, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:2');
INSERT INTO tag_item_assoc VALUES (10, 20, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:20');
INSERT INTO tag_item_assoc VALUES (10, 33, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:33');
INSERT INTO tag_item_assoc VALUES (11, 3, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:3');
INSERT INTO tag_item_assoc VALUES (12, 4, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:4');
INSERT INTO tag_item_assoc VALUES (13, 5, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:5');
INSERT INTO tag_item_assoc VALUES (14, 6, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:6');
INSERT INTO tag_item_assoc VALUES (14, 17, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:17');
INSERT INTO tag_item_assoc VALUES (15, 7, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:7');
INSERT INTO tag_item_assoc VALUES (15, 36, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:36');
INSERT INTO tag_item_assoc VALUES (16, 1, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:1');
INSERT INTO tag_item_assoc VALUES (16, 2, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:2');
INSERT INTO tag_item_assoc VALUES (16, 8, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:8');
INSERT INTO tag_item_assoc VALUES (16, 1, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:11');
INSERT INTO tag_item_assoc VALUES (16, 28, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:28');
INSERT INTO tag_item_assoc VALUES (16, 29, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:29');
INSERT INTO tag_item_assoc VALUES (16, 30, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:30');
INSERT INTO tag_item_assoc VALUES (16, 31, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:31');
INSERT INTO tag_item_assoc VALUES (16, 37, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:37');
INSERT INTO tag_item_assoc VALUES (17, 2, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:2');
INSERT INTO tag_item_assoc VALUES (17, 8, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:8');
INSERT INTO tag_item_assoc VALUES (17, 43, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:43');
INSERT INTO tag_item_assoc VALUES (18, 10, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:10');
INSERT INTO tag_item_assoc VALUES (18, 11, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:11');
INSERT INTO tag_item_assoc VALUES (19, 11, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:11');
INSERT INTO tag_item_assoc VALUES (19, 13, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:13');
INSERT INTO tag_item_assoc VALUES (19, 25, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:25');
INSERT INTO tag_item_assoc VALUES (19, 27, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:27');
INSERT INTO tag_item_assoc VALUES (20, 11, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:11');
INSERT INTO tag_item_assoc VALUES (20, 28, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:28');
INSERT INTO tag_item_assoc VALUES (20, 29, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:29');
INSERT INTO tag_item_assoc VALUES (20, 30, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:30');
INSERT INTO tag_item_assoc VALUES (20, 31, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:31');
INSERT INTO tag_item_assoc VALUES (20, 37, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:37');
INSERT INTO tag_item_assoc VALUES (21, 12, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:12');
INSERT INTO tag_item_assoc VALUES (22, 5, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:5');
INSERT INTO tag_item_assoc VALUES (22, 18, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:18');
INSERT INTO tag_item_assoc VALUES (22, 41, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:41');
INSERT INTO tag_item_assoc VALUES (23, 18, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:18');
INSERT INTO tag_item_assoc VALUES (23, 21, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:21');
INSERT INTO tag_item_assoc VALUES (24, 1, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:1');
INSERT INTO tag_item_assoc VALUES (24, 2, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:2');
INSERT INTO tag_item_assoc VALUES (24, 3, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:3');
INSERT INTO tag_item_assoc VALUES (24, 20, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:20');
INSERT INTO tag_item_assoc VALUES (24, 22, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:22');
INSERT INTO tag_item_assoc VALUES (24, 23, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:23');
INSERT INTO tag_item_assoc VALUES (24, 25, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:25');
INSERT INTO tag_item_assoc VALUES (24, 32, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:32');
INSERT INTO tag_item_assoc VALUES (24, 33, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:33');
INSERT INTO tag_item_assoc VALUES (24, 38, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:38');
INSERT INTO tag_item_assoc VALUES (24, 39, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:39');
INSERT INTO tag_item_assoc VALUES (24, 42, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:42');
INSERT INTO tag_item_assoc VALUES (24, 43, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:43');
INSERT INTO tag_item_assoc VALUES (25, 22, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:22');
INSERT INTO tag_item_assoc VALUES (25, 23, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:23');
INSERT INTO tag_item_assoc VALUES (26, 24, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:24');
INSERT INTO tag_item_assoc VALUES (27, 27, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:27');
INSERT INTO tag_item_assoc VALUES (28, 27, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:27');
INSERT INTO tag_item_assoc VALUES (29, 32, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:32');
INSERT INTO tag_item_assoc VALUES (30, 34, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:34');
INSERT INTO tag_item_assoc VALUES (31, 1, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:1');
INSERT INTO tag_item_assoc VALUES (31, 34, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:34');
INSERT INTO tag_item_assoc VALUES (31, 36, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:36');
INSERT INTO tag_item_assoc VALUES (31, 42, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:42');
INSERT INTO tag_item_assoc VALUES (32, 4, 'file_id', 'multimedia_file_details', 'multimedia_tag_file', 'file_id:4');
INSERT INTO tag_item_assoc VALUES (32, 35, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:35');
INSERT INTO tag_item_assoc VALUES (33, 40, 'article_id', 'news_article_details', 'news_tag_article', 'article_id:40');
GO

-- Insert data for table core_dashboard
INSERT INTO core_dashboard VALUES (1, 'admin', '{"isRoot": 1, "cols": 12, "containers": [{"isRoot": 0, "cols": 12, "containers": [{"isRoot": 0, "cols": 8, "containers": [], "widgets": [{"cls": "Tomato.Core.Layout.Widget", "module": "news", "name": "dashboardarticle", "title": "Show latest articles", "resources": {"css": [], "javascript": []}, "params": {"limit": {"value": "10", "type": ""}, "___cacheLifetime": {"value": "", "type": ""}, "___loadAjax": {"value": "", "type": ""}}}], "position": "first"}, {"isRoot": 0, "cols": 4, "containers": [], "widgets": [{"cls": "Tomato.Core.Layout.Widget", "module": "comment", "name": "dashboardcomment", "title": "Show latest comments", "resources": {"css": [], "javascript": []}, "params": {"limit": {"value": "5", "type": ""}, "___cacheLifetime": {"value": "", "type": ""}, "___loadAjax": {"value": "", "type": ""}}}, {"cls": "Tomato.Core.Layout.Widget", "module": "core", "name": "dashboardsystem", "title": "System information", "resources": {"css": [], "javascript": []}, "params": {"___cacheLifetime": {"value": "", "type": ""}, "___loadAjax": {"value": "", "type": ""}}}], "position": "last"}], "widgets": []}], "widgets": []}', 0);

-- Insert data for table menu_item
INSERT INTO menu_item VALUES (1, 1, 'Homepage', '/tomatocms/index.php/', 1, 2, 0);
INSERT INTO menu_item VALUES (2, 1, 'Versions', '#', 3, 22, 0);
INSERT INTO menu_item VALUES (3, 1, 'v2.0.0: Initial Version', '/tomatocms/index.php/news/category/view/1/', 4, 5, 2);
INSERT INTO menu_item VALUES (4, 1, 'v2.0.1: Install Wizard', '/tomatocms/index.php/news/category/view/2/', 6, 7, 2);
INSERT INTO menu_item VALUES (5, 1, 'v2.0.2: Tag Module', '/tomatocms/index.php/news/category/view/3/', 8, 9, 2);
INSERT INTO menu_item VALUES (6, 1, 'v2.0.3: Improves Hook', '/tomatocms/index.php/news/category/view/4/', 10, 11, 2);
INSERT INTO menu_item VALUES (7, 1, 'v2.0.4: Improves core, multimedia, news', '/tomatocms/index.php/news/category/view/5/', 12, 13, 2);
INSERT INTO menu_item VALUES (8, 1, 'v2.0.5: Multiple Databases', '/tomatocms/index.php/news/category/view/6/', 14, 15, 2);
INSERT INTO menu_item VALUES (9, 1, 'v2.0.6: Mail Module', '/tomatocms/index.php/news/category/view/7/', 16, 17, 2);
INSERT INTO menu_item VALUES (10, 1, 'v2.0.7: SEO', '/tomatocms/index.php/news/category/view/8/', '18', 21, 2);
INSERT INTO menu_item VALUES (11, 1, 'v2.0.7.1: Page Module', '/tomatocms/index.php/news/category/view/9/', 19, 20, 10);
INSERT INTO menu_item VALUES (12, 1, 'About', '/tomatocms/index.php/page/details/1', 23, 24, 0);
INSERT INTO menu_item VALUES (13, 1, 'Contact', '/tomatocms/index.php/page/details/2', 25, 26, 0);

-- Insert data for table news_article_revision
INSERT INTO news_article_revision VALUES (1, 1, 'TomatoCMS 2.0.0 released on 4th, January, 2010', '', 'tomatocms-200-released-on-4th--january--2010', '<p>This is the first release TomatoCMS.</p>', '<p>This is first release of TomatoCMS on Jan, 4th, 2010. Although we have developed it for 7-8 months ago.</p>', 'TomatoCMS Core Team', '', '2010-08-04 11:57:39', 1, 'admin');
INSERT INTO news_article_revision VALUES (2, 2, 'Install Wizard', '', 'install-wizard', '<p>TomatoCMS 2.0.1 allows you to install easily using Install Wizard</p>', '<p>Now, Install Wizard only take three steps to install TomatoCMS. You can install it in root web directory or its sub-directory.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f3e9a1c92_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 12:01:36', 1, 'admin');
INSERT INTO news_article_revision VALUES (3, 2, 'Supports nested comments', '', 'supports-nested-comments', '<p>TomatoCMS 2.0.1 improves comment module. One of important improvements is supports nested comments.</p>', '<p>Below is the list of new features in comment module:</p>' + char(13) + char(10) + '<p>- Support nested, unlimitted level comments</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91cce7f5_large.png" alt="" /></p>' + char(13) + char(10) + '<p>- Shows avatar of commenters</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91eba043_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>- Allows users to use some simple HTML tags in comment</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f922b8d9f_large.png" alt="" /></p>' + char(13) + char(10) + '<p>- User can reply any comment in thread</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f924ba96a_large.png" alt="" /></p>' + char(13) + char(10) + '<p>- Administrator can apply hooks for filtering the content of comments:<br style="padding: 0px; margin: 0px;" />Below is two examples which was built already in TomatoCMS. The first one replaces special characters with emotion icons as follow</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f91fc0224_large.png" alt="" /></p>' + char(13) + char(10) + '<p>And the second one formats comment in pre-defined programming language style which is very useful for programmers'' blogs:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c58f920b9ccc_large.png" alt="" /></p>' + char(13) + char(10) + '<p>- Prevents spams<br style="padding: 0px; margin: 0px;" />At this version, we made an attempt at preventing spams by using the service provided by Akismet.<br style="padding: 0px; margin: 0px;" />To use this, you have to register an free Akismet API key.</p>', 'TomatoCMS Core Team', '{"image"}', '2010-08-04 12:25:35', 1, 'admin');
INSERT INTO news_article_revision VALUES (4, 2, 'Update informer', '', 'update-informer', '<p>In backend, user will receive the message that informs new version is available if any.</p>', '<p>Below is the screenshot:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_large.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:05:03', 1, 'admin');
INSERT INTO news_article_revision VALUES (4, 2, 'Update informer', '', 'update-informer', '<p>In backend, user will receive the message that informs new version is available if any.</p>', '<p>Below is the screenshot:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5902e4af7a9_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:06:28', 1, 'admin');
INSERT INTO news_article_revision VALUES (5, 3, 'Tag system', '', 'tag-system', '<p>User can tag not only the articles but also photos, sets</p>', '<p>(1): Make a suggestion based on user input<br style="padding: 0px; margin: 0px;" />(2): Show list of current selected tags</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5904387fa4b_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>- Tag managements:</p>' + char(13) + char(10) + '<p>(1): Search for tags<br style="padding: 0px; margin: 0px;" />(2): Create new tag<br style="padding: 0px; margin: 0px;" />(3): List of tags. You can click on tag to remove it.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59043a74af3_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>In addition, this version provide two widgets for tagging.<br style="padding: 0px; margin: 0px;" />The first one named&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">tag</span>&nbsp;show list of tags for given item (item here can be article, photo or photo set):</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59043c73bc5_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>and the second one is&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">Tag Cloud</span>&nbsp;which show list of tags:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59043e7307f_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '{"image"}', '2010-08-04 13:12:27', 1, 'admin');
INSERT INTO news_article_revision VALUES (6, 3, 'Front-end for multimedia module', '', 'front-end-for-multimedia-module', '<p>In this version, you can view photo, clip on frontend section&nbsp;</p>' + char(13) + char(10) + '<p>(Try to click on photo in slideshow on homepage)</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590552aae3e_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>There are new widgets in this version which are:<br style="padding: 0px; margin: 0px;" />- filesets: Show list of set that current photo/clip belongs to:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590550ab985_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>- latestsets: Show the latest sets</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590551ab9bd_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Beside this, we improved backend strongly for more friendly:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59054db68a3_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:16:40', 1, 'admin');
INSERT INTO news_article_revision VALUES (7, 3, 'Menu builder', '', 'menu-builder', '<p>The menu is most important component of website. In this version, we released an initial version for menu module.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59060a370b8_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:18:29', 1, 'admin');
INSERT INTO news_article_revision VALUES (8, 3, 'Ability of customizing URLs', '', 'ability-of-customizing-urls', '<p>In 2.0.1 version and earlier, the default URL of article is http://site.com/news/article/view/1/1/</p>' + char(13) + char(10) + '<p>Now, with 2.0.2 version, developer can customize URLs of article, category, photo pages. This is better for SEO.</p>', '<p>In the following figure, I configured article page''s URL to format of&nbsp;<br style="padding: 0px; margin: 0px;" />/news/article/view/<span style="font-weight: bold; padding: 0px; margin: 0px;">ID_Of_Category</span>/<span style="font-weight: bold; padding: 0px; margin: 0px;">ID_Of_Article</span>-<span style="font-weight: bold; padding: 0px; margin: 0px;">Slug_Of_Article</span>.html</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59068c4a708_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:20:45', 1, 'admin');
INSERT INTO news_article_revision VALUES (9, 4, 'Uses more Zend Framework components', '', 'uses-more-zend-framework-components', '<p>In this 2.0.3 version, we replace some libraries with Zend Framework components.</p>', '<p>- Remove PEAR JSON Service from Twitter widget and use Zend_Json<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Use Zend_Paginator and Zend_View_Helper_PaginationControl to render the paginator instead of PEAR_Pager<br style="padding: 0px; margin: 0px;" /></p>' + char(13) + char(10) + '<p>- Use Zend_Application to create and bootstrap the application<br style="padding: 0px; margin: 0px;" /></p>' + char(13) + char(10) + '<p>- Fixed to work with Zend Framework 1.10.0.<br style="padding: 0px; margin: 0px;" />Also, Zend Framework 1.10.0 is attached in download package.</p>', 'TomatoCMS Core Team', '', '2010-08-04 13:23:20', 1, 'admin');
INSERT INTO news_article_revision VALUES (10, 4, 'Improves core module', '', 'improves-core-module', '<p>We improve core module including supporting database prefix, more easy to call translator.</p>', '<p>- Support database prefix. You can set it when install TomatoCMS.</p>' + char(13) + char(10) + '<p>- Now, it''s more easy to translate language data in modules and widgets without caring about current module, widget.</p>' + char(13) + char(10) + '<p>- With 2.0.3 version, you can install and apply hook in module level.<br style="padding: 0px; margin: 0px;" /></p>' + char(13) + char(10) + '<p>Install hook:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59083304e72_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Apply hook:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59083207931_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:28:16', 1, 'admin');
INSERT INTO news_article_revision VALUES (11, 4, 'New SEO module', '', 'new-seo-module', '<p>This 2.0.3 version built new SEO module which provide SEO utilities.</p>', '<p>Currently, it comes with</p>' + char(13) + char(10) + '<p><br style="padding: 0px; margin: 0px;" />- New hook named&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">gatracker</span>&nbsp;that allows you to insert Google Analytic tracker code.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d582381_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>- New widget named&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">googler</span>&nbsp;that show configurable welcome message if user visit your site from Google''s searching result</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5908d68b444_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>', 'TomatoCMS Core Team', '', '2010-08-04 13:30:58', 1, 'admin');
INSERT INTO news_article_revision VALUES (12, 4, 'Supports RTL languages', '', 'supports-rtl-languages', '<p>2.0.3 version supports RTL languages as Arabic, Iranian, ... for both front-end and back-end sections</p>', '<p>In front-end:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e70e68e_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>and back-end:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5909e5176a6_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:35:20', 1, 'admin');
INSERT INTO news_article_revision VALUES (13, 4, 'New widgets', '', 'new-widgets', '<p>There''re two built-in widgets in this version: Flickr and TextResizer</p>', '<p>- The first widget is&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">Flickr</span>&nbsp;that show latest Flickr images from given account.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590a7e5f616_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>- The second one is&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">TextResizer</span>&nbsp;that allows user to set the smaller or larger font size for pages</p>', 'TomatoCMS Core Team', '', '2010-08-04 13:37:16', 1, 'admin');
INSERT INTO news_article_revision VALUES (14, 4, 'Offline mode', '', 'offline-mode', '<p>This 2.0.3 version allows you to set your website in offline mode</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae6ea4aa_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>You can configure this message when install using Install Wizard or in back-end:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590ae5eeac1_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:39:17', 1, 'admin');
INSERT INTO news_article_revision VALUES (15, 4, 'Session lifetime and debug mode', '', 'session-lifetime-and-debug-mode', '', '<p>- Session lifetime:<br style="padding: 0px; margin: 0px;" />Now, users don''t have to remove expired session manually. Users can set session lifetime (in seconds) in&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">app.ini</span>&nbsp;(TomatoCMS_Installed_Folder/app/config/app.ini):</p>' + char(13) + char(10) + '<p>[web]</p>' + char(13) + char(10) + '<p>session_lifetime = "3600"</p>' + char(13) + char(10) + '<p>- Debug mode:<br style="padding: 0px; margin: 0px;" />This version allows developer to set the website in debug mode by setting following option in&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">app.ini</span>:</p>' + char(13) + char(10) + '<p>[web]<br style="padding: 0px; margin: 0px;" />debug = "true"</p>', 'TomatoCMS Core Team', '', '2010-08-04 13:41:27', 1, 'admin');
INSERT INTO news_article_revision VALUES (15, 4, 'Session lifetime and debug mode', '', 'session-lifetime-and-debug-mode', '<p>2.0.3 version allows user to set the session lifetime and debug mode for the website.</p>', '<p>- Session lifetime:<br style="padding: 0px; margin: 0px;" />Now, users don''t have to remove expired session manually. Users can set session lifetime (in seconds) in&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">app.ini</span>&nbsp;(TomatoCMS_Installed_Folder/app/config/app.ini):</p>' + char(13) + char(10) + '<p>[web]</p>' + char(13) + char(10) + '<p>session_lifetime = "3600"</p>' + char(13) + char(10) + '<p>- Debug mode:<br style="padding: 0px; margin: 0px;" />This version allows developer to set the website in debug mode by setting following option in&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">app.ini</span>:</p>' + char(13) + char(10) + '<p>[web]<br style="padding: 0px; margin: 0px;" />debug = "true"</p>', 'TomatoCMS Core Team', '', '2010-08-04 13:42:34', 1, 'admin');
INSERT INTO news_article_revision VALUES (16, 5, 'Import sample data', '', 'import-sample-data', '<p>In a step of Install Wizard, we add feature allowing user to import sample data.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590c7233d2a_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Ability of uploading language package in *.zip which consist of language files for modules, widgets:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590c733414a_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:45:51', 1, 'admin');
INSERT INTO news_article_revision VALUES (17, 5, 'Water mask and Image Editor', '', 'water-mask-and-image-editor', '<p>With TomatoCMS 2.0.4, you can use water masking features and edit your image online.</p>', '<p>The uploader provides the ability of inserting water mask:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590d2bf3dec_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Image Editor with some basic actions as crop, rotate:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590d29f31c1_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>When preview a image, you can add note to it, and these notes will be managed at backend:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590d281334d_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:49:19', 1, 'admin');
INSERT INTO news_article_revision VALUES (18, 5, 'Browses and inserts uploaded images', '', 'browses-and-inserts-uploaded-images', '<p>TomatoCMS 2.0.4 allows you to browse and insert images that were uploaded before when adding/editing the article.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590e0e7852f_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- You can preview article:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590e116b2e6_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>- Create article revision and you can restore or delete it whenever you want :</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590e126e5e9_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:53:54', 1, 'admin');
INSERT INTO news_article_revision VALUES (19, 6, 'Supports multiple databases', '', 'supports-multiple-databases', '<p>TomatoCMS 2.0.5 supports multiple databases.</p>', '<p>The list of supported databases are:</p>' + char(13) + char(10) + '<p>- MySQL with both native (mysql)&nbsp;and PDO driver (pdo, pdo_mysql)</p>' + char(13) + char(10) + '<p>- SQL Server 2005 with sqlsrv driver</p>' + char(13) + char(10) + '<p>- PostgreSQL with pgsql driver</p>', 'TomatoCMS Core Team', '', '2010-08-04 13:56:41', 1, 'admin');
INSERT INTO news_article_revision VALUES (20, 7, 'Allows to choose the charset', '', 'allows-to-choose-the-charset', '<p>TomatoCMS 2.0.6 allows user to choose the charset at Install Wizard or configure it in back-end</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c590f9d25c18_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 13:59:28', 1, 'admin');
INSERT INTO news_article_revision VALUES (21, 7, ' Shows all available thumbnail images after uploading the image', '', 'shows-all-available-thumbnail-images-after-uploading-the-image', '<p>In adding/editing article pages, you can select the thumbnail from available thumbnails after uploading the image</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59105305a0b_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:02:15', 1, 'admin');
INSERT INTO news_article_revision VALUES (22, 7, 'Compress CSS and Javascript', '', 'compress-css-and-javascript', '<p>To improve page load time, TomatoCMS 2.0.6 allows website to compress CSS, Javascript in both front-end and back-end sections</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5910ceef4ad_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>All CSS, Javascript files will be combined in only one file and this file will be cached on server:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cb1576f_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Also,<strong>&nbsp;</strong><span style="font-weight: bold; padding: 0px; margin: 0px;">content of every pages were also compressed</span>:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5910cd02efd_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>You can clear compress cache files in back-end:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591175025a5_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:07:12', 1, 'admin');
INSERT INTO news_article_revision VALUES (23, 7, 'Caches entire pages', '', 'caches-entire-pages', '<p>TomatoCMS 2.0.6 has the ability of caching entire pages</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ed1d2d3_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Once the page was cached, you will see the cached time by viewing the source of page:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5911ef1bbd5_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:09:34', 1, 'admin');
INSERT INTO news_article_revision VALUES (24, 7, 'New mail module', '', 'new-mail-module', '<p>This version comes with new built-in module: mail powered by Zend_Mail component</p>', '<p>The mail module supports sending mail with PHP mail() function or SMTP server:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59127ee91e1_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>It provides the&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">mail template management system</span>:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591281ec93d_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>You can&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">send emails</span>&nbsp;to all registered users or all users belonging to given group:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591283e065c_medium.png" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>With this version, you can&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">reset the password</span>. The reset password will be sent via email using mail module:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591280eb966_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:13:02', 1, 'admin');
INSERT INTO news_article_revision VALUES (25, 7, 'Login widget', '', 'login-widget', '<p>2.0.6 version provides login widget.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59134836c68_medium.png" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:14:50', 1, 'admin');
INSERT INTO news_article_revision VALUES (26, 8, 'New back-end interface', '', 'new-back-end-interface', '<p>Install Wizard and all back-end pages now have new interface</p>', '<p>- Install Wizard:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149047cec_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- Login pages:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149540108_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- In back-end, TomatoCMS uses mega menu and all of core tasks were moved to System menu.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149e40ec4_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>You also can find modules'' tasks under Module menu:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5914933ae8e_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- We also made many pages more friendly and more easy to use.<br style="padding: 0px; margin: 0px;" />For instance, in most of pages coming from core module, you can make a module filter instead of scrolling though the long page like before. Below is just some examples:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />Privileges management:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149b3d768_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Widgets management:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59149842caf_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:24:27', 1, 'admin');
INSERT INTO news_article_revision VALUES (27, 8, 'Customize Dashboard', '', 'customize-dashboard', '<p>In TomatoCMS 2.0.6 and earlier versions, you can not customize your Dashboard which were used to display all of modules'' tasks. From 2.0.7, the ability of customizing dashboard come true.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160d4a32e_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>We also created some useful Dashboard widgets:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Latest Articles: Show most of recently articles. You can activate or deactivate the article right now if you want</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916095177d_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- Latest Comments: Show most of latest comments. Of course, you can activate or deactivate the comment if you have the permission to do.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160c4aead_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- Google backlinks: Show top of back links taken from Google</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160a4c993_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:27:43', 1, 'admin');
INSERT INTO news_article_revision VALUES (27, 8, 'Customize Dashboard', '', 'customize-dashboard', '<p>In TomatoCMS 2.0.6 and earlier versions, you can not customize your Dashboard which were used to display all of modules'' tasks. From 2.0.7, the ability of customizing dashboard come true.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160d4a32e_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>We also created some useful Dashboard widgets:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Latest Articles: Show most of recently articles. You can activate or deactivate the article right now if you want</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916095177d_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- Latest Comments: Show most of latest comments. Of course, you can activate or deactivate the comment if you have the permission to do.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160c4aead_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- Google backlinks: Show top of back links taken from Google</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59160a4c993_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:28:46', 1, 'admin');
INSERT INTO news_article_revision VALUES (28, 8, 'SEO module: Sitemap management', '', 'seo-module--sitemap-management', '<p>TomatoCMS 2.0.7 allows you to manage sitemap easily</p>', '<p>You can see the all the link from the sitemap:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f503b43_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Of course, you can remove the links from sitemap as well as add link to sitemap:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5916f20dac2_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:30:48', 1, 'admin');
INSERT INTO news_article_revision VALUES (29, 8, 'SEO module: Toolkit', '', 'seo-module--toolkit', '<p>The aim of this toolkit is to get the backlinks, indexed pages and rank from most popular search engines such as Google, Yahoo, and Bing. You can get these data for not only your current site but also any website you want.</p>', '<p>Below is the screenshots of using this toolkit to get Google page rank, Alexa rank, its indexed pages:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591788067d3_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>and its back links:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591770075ff_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>The toolkit also show the total number of page indexed and back links.</p>', 'TomatoCMS Core Team', '', '2010-08-04 14:33:25', 1, 'admin');
INSERT INTO news_article_revision VALUES (30, 8, 'SEO module: Interactive with Google Web Master', '', 'seo-module--interactive-with-google-web-master', '<p>Using your Google account, TomatoCMS 2.0.7 allows you to interactive with Google Web Master easily.</p>', '<p>The list of functions include:</p>' + char(13) + char(10) + '<p><strong>List of sites you registry with Google Web Master </strong></p>' + char(13) + char(10) + '<p>You also can add other site or remove certain site from the list.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59187c7bac3_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Sitemap management </strong></p>' + char(13) + char(10) + '<p>When viewing the site details, TomatoCMS shows all its sitemaps. The tool allows you to remove or add other sitemap</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59187f686d2_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>List of keywords </strong></p>' + char(13) + char(10) + '<p>You can see the list of keywords which Google found on the site. More than that, you can make a keyword filter: see the internal, external or all keywords.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591885693e0_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Verify site </strong></p>' + char(13) + char(10) + '<p>Final function is verify your site. You can verify by using one of two methods:</p>' + char(13) + char(10) + '<p>- Use HTML meta tag</p>' + char(13) + char(10) + '<p>- Or create HTML page.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c59188268f4e_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:39:44', 1, 'admin');
INSERT INTO news_article_revision VALUES (31, 8, 'SEO module: Show Google Analytic reports', '', 'seo-module--show-google-analytic-reports', '<p>From 2.0.7 version, TomatoCMS can show Google Analytic reports. Of course, you have to make authentication using your Google account before using this tool.</p>', '<p>You can select the website and the time range you want to get the report data.<br style="padding: 0px; margin: 0px;" />Finally, you can see most popular reports inside TomatoCMS:<br style="padding: 0px; margin: 0px;" />- Visit, Unique visitors, Page views, Time on site and Bounce rate<br style="padding: 0px; margin: 0px;" />- Browser, Operating system and Screen resolution<br style="padding: 0px; margin: 0px;" />- Traffic source and keywords<br style="padding: 0px; margin: 0px;" />- Top viewed pages and top exit pages<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />TomatoCMS gets all data at once time, therefore if you switch between reports, you don''t have to wait for data loading.<br style="padding: 0px; margin: 0px;" />You can move the mouse over the points on chart to see the details data.<br style="padding: 0px; margin: 0px;" />And other interesting point, we used Javascript, not Flash, to render the chart. It is more easy for us to improve and customize the chart later.<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />Here is some screenshots:<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />- Visit report:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591995749c1_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>- Time on site report:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c5919987429d_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:41:56', 1, 'admin');
INSERT INTO news_article_revision VALUES (32, 9, 'Protects from CSRF attack', '', 'protects-from-csrf-attack', '<p>TomatoCMS 2.0.7.1 protects your site from CSRF attacks.</p>', '<p>Below is the list of TomatoCMS CSRF vulnerabilities and all of them were fixed:</p>' + char(13) + char(10) + '<p>- Change Administrator Password</p>' + char(13) + char(10) + '<p>- Create Admin User</p>' + char(13) + char(10) + '<p>- Deactivate User</p>' + char(13) + char(10) + '<p>- Logout The Administrator</p>', 'TomatoCMS Core Team', '', '2010-08-04 14:44:28', 1, 'admin');
INSERT INTO news_article_revision VALUES (33, 9, 'Improvements: Install Wizard (core module)', '', 'improvements--install-wizard-core-module', '<p>From 2.0.7 and earlier versions, there is no way to select the language at Install Wizard as well as switch to other language in back-end. In this 2.0.7.1 version, at the first step of Install Wizard, you can choose the language from one of built-in languages or upload a new one.</p>', '<p>After uploading the package, you can choose uploaded language.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591aa1c2934_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:46:16', 1, 'admin');
INSERT INTO news_article_revision VALUES (34, 9, 'Improvements: Banners management (ad module)', '', 'improvements--banners-management-ad-module', '<p>In 2.0.7 and earlier versions, it''s not convenient for user to add banners to certain page. Some users even don''t know how to do next after checking selecting page check boxes.</p>', '<p>Also, if you understand how it works, you have to put the page/categories URL manually.&nbsp;<br style="padding: 0px; margin: 0px;" />Well, it was not good and not friendly.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591b1f8f5a7_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>With TomatoCMS 2.0.7.1, the interface is more clear, more friendly and it''s easy to select the page and zone for banner by using the Link Provider:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591b218fa02_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:49:00', 1, 'admin');
INSERT INTO news_article_revision VALUES (35, 9, 'Improvements: Categories management (category module)', '', 'improvements--categories-management-category-module', '<p>In category module, most of users don''t understand what "Include child category" mean while editing the category. Also, if you want to change the order of categories tree, you have to perform updating each categories.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591bd1c6169_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>With 2.0.7.1, we remove the "Include child category" option and also, you can drag and drop categories (and its children) to other position. Beside, it allows you to collapse or expand the category like a tree node!</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591bcfc553e_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:51:19', 1, 'admin');
INSERT INTO news_article_revision VALUES (36, 9, 'Improvements: Menu builder (menu module)', '', 'improvements--menu-builder-menu-module', '<p>Thats what we think the most of user are waiting for.&nbsp;</p>', '<p>The current menu builder is not good:</p>' + char(13) + char(10) + '<p>- The interface is not easy to understand and use</p>' + char(13) + char(10) + '<p>- We have to input the link of menu item manually !!!</p>' + char(13) + char(10) + '<p>- The source code is dirty with many Javascript classes and probably will be difficult to maintain</p>' + char(13) + char(10) + '<p>In other words, there are many questions you will have if you use this before:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5a177a3_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Well, the inconvenient things above will be come the past!</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">With next 2.0.7.1, the Menu builder allows you to:</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- Input the link easily with Link Provider (again,the Link Provider is powerful)</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- The user interface is clear and friendly. You can drag/drop, collapse/expand menu items.</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- The label and link are editable in place</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">- If you look at the source code (from SVN), it''s very clear. Many Javascripts classes used before are removed including the treeview, interface''s javascript libraries.</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Ok, let''s take a look at it:</div>' + char(13) + char(10) + '<p>Well, the inconvenient things above will be come the past!<br />With next 2.0.7.1, the Menu builder allows you to:</p>' + char(13) + char(10) + '<p>- Input the link easily with Link Provider (again, the Link Provider is powerful)</p>' + char(13) + char(10) + '<p>- The user interface is clear and friendly. You can drag/drop, collapse/expand menu items.</p>' + char(13) + char(10) + '<p>- The label and link are editable in place</p>' + char(13) + char(10) + '<p>- If you look at the source code (from SVN), it''s very clear. Many Javascripts classes used before are removed including the treeview, interface''s javascript libraries.</p>' + char(13) + char(10) + '<p><br />Ok, let''s take a look at it:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591c5c15cbe_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>', 'TomatoCMS Core Team', '', '2010-08-04 14:54:24', 1, 'admin');
INSERT INTO news_article_revision VALUES (37, 9, 'Improvements: Support both Client and AuthSub token authentication methods', '', 'improvements--support-both-client-and-authsub-token-authentication-methods', '<p>TomatoCMS 2.0.7 allows you to interactive with Google Web Master and see the Google Analytic reports. But each time you use these feature, you have to make Google authentication using your Google account and grant the application permission to get the data from Google.&nbsp;</p>', '<p>Technically, it uses AuthSub token authentication method.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591d39b756c_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>2.0.7.1 version allows you to use one more authentication method: ClientLogin.</p>' + char(13) + char(10) + '<p>Just open the SEO module''s configuration file which you can find at&nbsp;<span style="font-weight: bold; padding: 0px; margin: 0px;">/application/modules/seo/config/config.ini</span>&nbsp;and add the following section to the top of this file:</p>' + char(13) + char(10) + '<p>[google]<br style="padding: 0px; margin: 0px;" />username = "Your_Google_Account"<br style="padding: 0px; margin: 0px;" />password = "Yout_Google_Password"</p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>That''s all. Now you can use SEO module''s features without logging in to Google.</p>', 'TomatoCMS Core Team', '', '2010-08-04 14:57:09', 1, 'admin');
INSERT INTO news_article_revision VALUES (38, 9, 'Improvements: Pages management (core module)', '', 'improvements--pages-management-core-module', '<p>If you are using TomatoCMS 2.0.7 or older version, creating pages is not easy task.</p>', '<p>For instance, if you are not programmer, maybe you don''t understand what the page name and its URL mean, especially in case the URL contains the regular expression:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db56e1d8_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>But, it''s not the final challenge. It''s not easy for you to create a new page because at this page, you face with unfamiliar concept such as parameters, their names and orders:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db76de62_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>We also know about your problem. So, in next 2.0.7.1 version, creating page is very simple:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db3723ce_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Listing of pages also make you comfortable. You can edit, delete or edit layout if you want:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591db96bf94_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 14:59:51', 1, 'admin');
INSERT INTO news_article_revision VALUES (39, 9, 'Errors logging (core module)', '', 'errors-logging-core-module', '<p>In 2.0.7.1 version, all the errors will be logged and you can see them in back-end</p>', '<p>The current TomatoCMS version has ability of handling errors.&nbsp;We redesign the page that shows the error:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6812dc1_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>Also, if you create new template for TomatoCMS, you don''t have to create error page for your template.<br style="padding: 0px; margin: 0px;" /><br style="padding: 0px; margin: 0px;" />In 2.0.7.1 version, all the errors will be logged and you can see them in back-end:<br style="padding: 0px; margin: 0px;" />You can delete one or many log items at the same time.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591e6a0c4b9_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 15:02:16', 1, 'admin');
INSERT INTO news_article_revision VALUES (40, 9, 'Static pages (page module)', '', 'static-pages-page-module', '<p>TomatoCMS 2.0.7.1 provides new module named "page" for this purpose. You can create static page with HTML content.</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591ece1e89c_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>If you have more than one page, you can drag/drop page (and its children) to other one. The pages listing works like a tree that allows you to collapse/expand the page.</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591ecc29024_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 15:04:08', 1, 'admin');
INSERT INTO news_article_revision VALUES (41, 9, 'Improvements: Bulk actions for articles management (news module)', '', 'improvements--bulk-actions-for-articles-management-news-module', '<p>In 2.0.7, you can filter the list of articles by its status: All, Activated, Not activated, Draft or Trash. With 2.0.7.1, it allows you to perform same action to multiple articles at the same time</p>', '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591f2a6141b_medium.jpg" alt="" /></p>', 'TomatoCMS Core Team', '', '2010-08-04 15:05:46', 1, 'admin');
INSERT INTO news_article_revision VALUES (42, 9, 'Link Provider (core module)', '', 'link-provider-core-module', '<p>Link Provider is the machine that allows user to integrate the links and use it anywhere in the back-end.</p>', '<p>Before 2.0.7.1, there are many cases we have to input the link manually:<br style="padding: 0px; margin: 0px;" />- Set the link for banners (ad module)<br style="padding: 0px; margin: 0px;" />- Set the link for menu items (menu module)<br style="padding: 0px; margin: 0px;" />- Set the link for sitemap item (seo module)</p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>It was very bad if you have to create a menu with more than 10 items, for example.</p>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Well, in next 2.0.7.1, all tasks above are easy. Just click on the link provided by Link Provider and that''s all. All links are organized by different groups.</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Also, you can integrate your link provided by your own module to this machine easily.</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">Below is the sample when I add the link to menu item:</div>' + char(13) + char(10) + '<p>It was very bad if you have to create a menu with more than 10 items, for example.Well, in next 2.0.7.1, all tasks above are easy. Just click on the link provided by Link Provider and that''s all. All links are organized by different groups.</p>' + char(13) + char(10) + '<p>Also, you can integrate your link provided by your own module to this machine easily.</p>' + char(13) + char(10) + '<p>Below is the sample when I add the link to menu item:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c591fc5ae9ee_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>', 'TomatoCMS Core Team', '', '2010-08-04 15:08:13', 1, 'admin');
INSERT INTO news_article_revision VALUES (43, 9, 'Permalink management (core module)', '', 'permalink-management-core-module', '<p>TomatoCMS supports beautiful, friendly URLs that are good for SEO. But by default the URL has the format of /module/action/id/another_id which should be more descriptive. Now, this dream are coming true because in 2.0.7.1, you can configure the URL for all front-end URLs easily.</p>', '<p>Below is the screenshot that give you idea how it look like:</p>' + char(13) + char(10) + '<p><img src="http://localhost/tomatocms/upload/news/admin/2010/08/4c592029cd50d_medium.jpg" alt="" /></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">For each page, you can use the default URL, one of pre-define URLs or customize by yourself.</div>' + char(13) + char(10) + '<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow-x: hidden; overflow-y: hidden;">In case you use the custom URL, you don''t have to worry about wrong parameters because the tool suggests the valid parameter based on your input characters.</div>' + char(13) + char(10) + '<p>For each page, you can use the default URL, one of pre-define URLs or customize by yourself. In case you use the custom URL, you don''t have to worry about wrong parameters because the tool suggests the valid parameter based on your input characters.</p>' + char(13) + char(10) + '<p>&nbsp;</p>', 'TomatoCMS Core Team', '', '2010-08-04 15:09:47', 1, 'admin');
GO

-- Insert data for table page
INSERT INTO page VALUES ('About', 'about', '<p>TomatoCMS is an impressive, powerful Content Management System. It''s free and open source licensed under GNU GPL.</p>', '<p>TomatoCMS considers each web page made up of many different elements called widgets. You can easily create, customize the layout of your site like never before through a visual tool called Layout Editor very easy and convenient.</p>' + char(13) + char(10) + '<p>Layout Editor allows you to not only drag, drop but also configure the widgets as well as preview the layout of the site. TomatoCMS has a lot of built-in widgets, and developers can easily create new widgets.</p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p style="text-align:center;"><img src="http://localhost/tomatocms/upload/page/admin/2010/08/4c593364e7936_medium.png" alt="" /></p>', 1, 2, 0, 0, '2010-08-04 16:27:57', '2010-08-04 16:33:10', 1, 'en_US');
INSERT INTO page VALUES ('Contact', 'contact', '<p>Feel free to send us your comments, suggestions to make TomatoCMS better and more popular.</p>', '<p><strong>Official website</strong></p>' + char(13) + char(10) + '<p><a href="http://www.tomatocms.com">http://www.tomatocms.com</a></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Online Demo</strong></p>' + char(13) + char(10) + '<p><a href="http://demo.tomatocms.com">http://demo.tomatocms.com</a></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Documentation</strong></p>' + char(13) + char(10) + '<p><a href="http://docs.tomatocms.com">http://docs.tomatocms.com</a></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Forum</strong></p>' + char(13) + char(10) + '<p><a href="http://forum.tomatocms.com">http://forum.tomatocms.com</a></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Bugs Tracking</strong></p>' + char(13) + char(10) + '<p><a href="http://bugs.tomatocms.com">http://bugs.tomatocms.com</a></p>' + char(13) + char(10) + '<p>&nbsp;</p>' + char(13) + char(10) + '<p><strong>Twitter</strong></p>' + char(13) + char(10) + '<p><a href="http://twitter.com/tomatocms">http://twitter.com/tomatocms</a></p>', 3, 4, 0, 0, '2010-08-04 16:39:21', '2010-08-04 16:40:55', 1, 'en_US');

-- Insert data for table page
INSERT INTO core_session VALUES ('03f62vdb18b41alt2dthg54tk5','Zend_Auth|a:1:{s:7:"storage";O:16:"Core_Models_User":1:{s:14:"\0*\0_properties";a:11:{s:7:"user_id";s:1:"1";s:9:"user_name";s:5:"admin";s:8:"password";s:32:"535e5211955b0fdf5771b787ac76e39c";s:9:"full_name";s:13:"Administrator";s:5:"email";s:15:"admin@email.com";s:9:"is_active";s:1:"1";s:12:"created_date";N;s:14:"logged_in_date";N;s:9:"is_online";s:1:"0";s:7:"role_id";s:1:"1";s:9:"role_name";s:5:"admin";}}}Tomato_Controller_Action_Helper_Csrfsaltcsrf|a:1:{s:5:"token";s:32:"e0721fa81073c10c69fc6a09bef94aca";}',1282641514,36000);
INSERT INTO core_session VALUES ('882h70n211ukc98pb698arin81','Zend_Auth|a:1:{s:7:"storage";O:16:"Core_Models_User":1:{s:14:"\0*\0_properties";a:11:{s:7:"user_id";s:1:"1";s:9:"user_name";s:5:"admin";s:8:"password";s:32:"e28fb14b042b58b4c4137a8ff0f7b0d1";s:9:"full_name";s:13:"Administrator";s:5:"email";s:15:"admin@email.com";s:9:"is_active";s:1:"1";s:12:"created_date";N;s:14:"logged_in_date";N;s:9:"is_online";s:1:"0";s:7:"role_id";s:1:"1";s:9:"role_name";s:5:"admin";}}}Tomato_Controller_Action_Helper_Csrfsaltcsrf|a:1:{s:5:"token";s:32:"b22ac514f82570011398e465e9836b11";}',1280914941,360000);

-- Insert data for core_translation
INSERT INTO core_translation VALUES (1,'Category_Models_Category',1,'en_US',NULL);
INSERT INTO core_translation VALUES (2,'Category_Models_Category',2,'en_US',NULL);
INSERT INTO core_translation VALUES (3,'Category_Models_Category',3,'en_US',NULL);
INSERT INTO core_translation VALUES (4,'Category_Models_Category',4,'en_US',NULL);
INSERT INTO core_translation VALUES (5,'Category_Models_Category',5,'en_US',NULL);
INSERT INTO core_translation VALUES (6,'Category_Models_Category',6,'en_US',NULL);
INSERT INTO core_translation VALUES (7,'Category_Models_Category',7,'en_US',NULL);
INSERT INTO core_translation VALUES (8,'Category_Models_Category',8,'en_US',NULL);
INSERT INTO core_translation VALUES (9,'Category_Models_Category',9,'en_US',NULL);
INSERT INTO core_translation VALUES (1,'News_Models_Article',1,'en_US',NULL);
INSERT INTO core_translation VALUES (2,'News_Models_Article',2,'en_US',NULL);
INSERT INTO core_translation VALUES (3,'News_Models_Article',3,'en_US',NULL);
INSERT INTO core_translation VALUES (4,'News_Models_Article',4,'en_US',NULL);
INSERT INTO core_translation VALUES (5,'News_Models_Article',5,'en_US',NULL);
INSERT INTO core_translation VALUES (6,'News_Models_Article',6,'en_US',NULL);
INSERT INTO core_translation VALUES (7,'News_Models_Article',7,'en_US',NULL);
INSERT INTO core_translation VALUES (8,'News_Models_Article',8,'en_US',NULL);
INSERT INTO core_translation VALUES (9,'News_Models_Article',9,'en_US',NULL);
INSERT INTO core_translation VALUES (10,'News_Models_Article',10,'en_US',NULL);
INSERT INTO core_translation VALUES (11,'News_Models_Article',11,'en_US',NULL);
INSERT INTO core_translation VALUES (12,'News_Models_Article',12,'en_US',NULL);
INSERT INTO core_translation VALUES (13,'News_Models_Article',13,'en_US',NULL);
INSERT INTO core_translation VALUES (14,'News_Models_Article',14,'en_US',NULL);
INSERT INTO core_translation VALUES (15,'News_Models_Article',15,'en_US',NULL);
INSERT INTO core_translation VALUES (16,'News_Models_Article',16,'en_US',NULL);
INSERT INTO core_translation VALUES (17,'News_Models_Article',17,'en_US',NULL);
INSERT INTO core_translation VALUES (18,'News_Models_Article',18,'en_US',NULL);
INSERT INTO core_translation VALUES (19,'News_Models_Article',19,'en_US',NULL);
INSERT INTO core_translation VALUES (20,'News_Models_Article',20,'en_US',NULL);
INSERT INTO core_translation VALUES (21,'News_Models_Article',21,'en_US',NULL);
INSERT INTO core_translation VALUES (22,'News_Models_Article',22,'en_US',NULL);
INSERT INTO core_translation VALUES (23,'News_Models_Article',23,'en_US',NULL);
INSERT INTO core_translation VALUES (24,'News_Models_Article',24,'en_US',NULL);
INSERT INTO core_translation VALUES (25,'News_Models_Article',25,'en_US',NULL);
INSERT INTO core_translation VALUES (26,'News_Models_Article',26,'en_US',NULL);
INSERT INTO core_translation VALUES (27,'News_Models_Article',27,'en_US',NULL);
INSERT INTO core_translation VALUES (28,'News_Models_Article',28,'en_US',NULL);
INSERT INTO core_translation VALUES (29,'News_Models_Article',29,'en_US',NULL);
INSERT INTO core_translation VALUES (30,'News_Models_Article',30,'en_US',NULL);
INSERT INTO core_translation VALUES (31,'News_Models_Article',31,'en_US',NULL);
INSERT INTO core_translation VALUES (32,'News_Models_Article',32,'en_US',NULL);
INSERT INTO core_translation VALUES (33,'News_Models_Article',33,'en_US',NULL);
INSERT INTO core_translation VALUES (34,'News_Models_Article',34,'en_US',NULL);
INSERT INTO core_translation VALUES (35,'News_Models_Article',35,'en_US',NULL);
INSERT INTO core_translation VALUES (36,'News_Models_Article',36,'en_US',NULL);
INSERT INTO core_translation VALUES (37,'News_Models_Article',37,'en_US',NULL);
INSERT INTO core_translation VALUES (38,'News_Models_Article',38,'en_US',NULL);
INSERT INTO core_translation VALUES (39,'News_Models_Article',39,'en_US',NULL);
INSERT INTO core_translation VALUES (40,'News_Models_Article',40,'en_US',NULL);
INSERT INTO core_translation VALUES (41,'News_Models_Article',41,'en_US',NULL);
INSERT INTO core_translation VALUES (42,'News_Models_Article',42,'en_US',NULL);
INSERT INTO core_translation VALUES (43,'News_Models_Article',43,'en_US',NULL);
INSERT INTO core_translation VALUES (1,'Menu_Models_Menu',1,'en_US',NULL);
INSERT INTO core_translation VALUES (1,'Page_Models_Page',1,'en_US',NULL);
INSERT INTO core_translation VALUES (2,'Page_Models_Page',2,'en_US',NULL);