-- core module
UPDATE core_resource SET controller_name = 'question' WHERE module_name = 'poll' AND controller_name = 'poll';

-- multimedia module
ALTER TABLE `multimedia_file` CHANGE `image_general` `image_thumbnail` text DEFAULT NULL;
ALTER TABLE `multimedia_set` CHANGE `image_general` `image_thumbnail` text DEFAULT NULL;

-- news module
UPDATE `news_article` SET image_thumbnail = image_general;
ALTER TABLE `news_article` DROP COLUMN `image_general`;
