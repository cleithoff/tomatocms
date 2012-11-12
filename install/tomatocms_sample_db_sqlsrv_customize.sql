 	UPDATE ad_banner
    SET image_url = REPLACE(image_url, 'http://localhost/tomatocms/', '/path/to/cms/');
 
    UPDATE menu_item
    SET
    link = REPLACE(link, '/tomatocms/index.php/', '/path/to/cms/');
 
    UPDATE multimedia_file
    SET
    image_square = REPLACE(image_square, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_small = REPLACE(image_small, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_crop = REPLACE(image_crop, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_medium = REPLACE(image_medium, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_large = REPLACE(image_large, 'http://localhost/tomatocms/', '/path/to/cms/');
 
    UPDATE multimedia_set
    SET
    image_square = REPLACE(image_square, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_small = REPLACE(image_small, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_crop = REPLACE(image_crop, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_medium = REPLACE(image_medium, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_large = REPLACE(image_large, 'http://localhost/tomatocms/', '/path/to/cms/');
 
    UPDATE news_article
    SET
    image_square = REPLACE(image_square, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_small = REPLACE(image_small, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_thumbnail = REPLACE(image_thumbnail, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_crop = REPLACE(image_crop, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_medium = REPLACE(image_medium, 'http://localhost/tomatocms/', '/path/to/cms/'),
    image_large = REPLACE(image_large, 'http://localhost/tomatocms/', '/path/to/cms/');