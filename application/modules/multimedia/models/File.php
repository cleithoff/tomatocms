<?php 
/**
 * TomatoCMS
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tomatocms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 TIG Corporation (http://www.tig.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: File.php 5487 2010-09-20 14:00:21Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a multimedia file
 */
class Multimedia_Models_File extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'file_id' 			=> null,	/** Id of file */
		'category_id' 		=> null,	/** Id of category */
		'title' 			=> null,	/** Title of file */
		'slug' 				=> null,	/** Slug of file */
		'description' 		=> null,	/** Description of file */
		'content' 			=> null,	/** Content of file */
	
		/**
		 * Thumbnails URLs
		 */
		'image_square' 		=> null,	/** Thumbnail image in square size */
		'image_thumbnail' 	=> null,	/** Thumbnail size */
		'image_small' 		=> null,	/** Small size */
		'image_crop' 		=> null,	/** Crop size */
		'image_medium' 		=> null,	/** Medium size */
		'image_large' 		=> null,	/** Large size */
	
		'num_views' 		=> null,	/** Number of views */
		'num_comments' 		=> null,	/** Number of comments */
		'allow_comment' 	=> null,	/** Allows user to comment or not */
		'created_date' 		=> null,	/** File's creation date */
		'created_user' 		=> null,	/** Id of user who create file */
		'created_user_name' => null,	/** Username of user who create file */
		'ordering' 			=> null,	/** Ordering index of file */
		'url' 				=> null,	/** URL of file */
		'html_code' 		=> null,	/** HTML code (embed code) */ 
		'is_active' 		=> null,	/** File's status */
		'file_type' 		=> null,	/** File's type. Can be image or video */
	);
}
