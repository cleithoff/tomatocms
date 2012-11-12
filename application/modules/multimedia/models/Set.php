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
 * @version 	$Id: Set.php 5487 2010-09-20 14:00:21Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a set
 */
class Multimedia_Models_Set extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'set_id' 			=> null,	/** Id of set */
		'title' 			=> null,	/** Title of set */
		'slug' 				=> null,	/** Slug of set */
		'description' 		=> null,	/** Description of set */
	
		/**
		 * Thumbnails of cover
		 */
		'image_square' 		=> null,	/** Thumbnail image in square size */
		'image_thumbnail' 	=> null,	/** Thumbnail size */
		'image_small' 		=> null,	/** Small size */
		'image_crop' 		=> null,	/** Crop size */
		'image_medium' 		=> null,	/** Medium size */
		'image_large' 		=> null,	/** Large size */
	
		'num_views' 		=> null,	/** Number of views */
		'num_comments' 		=> null,	/** Number of comments */
		'created_date' 		=> null,	/** Set's creation date */
		'created_user_id' 	=> null,	/** Id of user who create set */
		'created_user_name' => null,	/** Username of user who create set */
		'is_active' 		=> null,	/** Set's activation status */
	);
}
