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
 * @version 	$Id: Category.php 5472 2010-09-20 08:36:34Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a category
 */
class Category_Models_Category extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'category_id' 	=> null,	/** Id of category */
		'name' 			=> null,	/** Name of category */
		'slug' 			=> null,	/** Slug of category. Should we make it unique? */
		'meta' 			=> null,	/** Meta keyword of category */
		'is_active' 	=> 0,		/** Active status. Can be 0 (activated) or 1 (not activated) */
	
		/**
		 * The left and right indicis that will be generated automatically
		 */
		'left_id' 		=> null,
		'right_id' 		=> null,
	
		'parent_id' 	=> 0,		/** Id of parent category. It will take value of 0 if the category is root one */	
		'created_date' 	=> null,	/** Category creation date */
		'modified_date' => null,	/** Category modification date */
		'user_id' 		=> null,	/** Id of user who create category */
		'language'      => null,	/** Language of category (@since 2.0.8) */
	);
}
