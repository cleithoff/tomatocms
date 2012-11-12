-- Run the following queries to make the sample data 
-- work in virtual host environment

UPDATE ad_banner
SET image_url = REPLACE(image_url, 'http://localhost/tomatocms/', '/');

UPDATE menu_item
SET
link = REPLACE(link, '/tomatocms/index.php/', '/');

UPDATE multimedia_file 
SET
image_square = REPLACE(image_square, 'http://localhost/tomatocms/', '/'), 
image_small = REPLACE(image_small, 'http://localhost/tomatocms/', '/'), 
image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/tomatocms/', '/'), 
image_crop = REPLACE(image_crop, 'http://localhost/tomatocms/', '/'), 
image_medium = REPLACE(image_medium, 'http://localhost/tomatocms/', '/'), 
image_large = REPLACE(image_large, 'http://localhost/tomatocms/', '/');

UPDATE multimedia_set
SET
image_square = REPLACE(image_square, 'http://localhost/tomatocms/', '/'), 
image_small = REPLACE(image_small, 'http://localhost/tomatocms/', '/'), 
image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/tomatocms/', '/'), 
image_crop = REPLACE(image_crop, 'http://localhost/tomatocms/', '/'), 
image_medium = REPLACE(image_medium, 'http://localhost/tomatocms/', '/'), 
image_large = REPLACE(image_large, 'http://localhost/tomatocms/', '/');

UPDATE news_article 
SET
image_square = REPLACE(image_square, 'http://localhost/tomatocms/', '/'), 
image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/tomatocms/', '/'), 
image_small = REPLACE(image_small, 'http://localhost/tomatocms/', '/'), 
image_crop = REPLACE(image_crop, 'http://localhost/tomatocms/', '/'), 
image_medium = REPLACE(image_medium, 'http://localhost/tomatocms/', '/'), 
image_large = REPLACE(image_large, 'http://localhost/tomatocms/', '/'),
content = REPLACE(content, 'http://localhost/tomatocms/', '/');