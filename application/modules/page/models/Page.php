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
 * @version 	$Id: Page.php 5482 2010-09-20 09:59:22Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Represents a page
 */
class Page_Models_Page extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'page_id' 	    => null,	/** Id of page */
		'name' 			=> null,	/** Name of page */	
		'slug' 			=> null,	/** Slug of page. Should it be unique? */
		'description'	=> null,	/** Description of page */
		'content'		=> null,	/** Content of page */
	
		/**
		 * The left and right indeces.
		 * They are generated automatically
		 */
		'left_id' 		=> null,
		'right_id' 		=> null,
	
		'parent_id' 	=> 0,		/** Id of parent page */
		'num_views'     => 0,		/** Number of views */
		'created_date' 	=> null,	/** Page's creation date */
		'modified_date' => null,	/** Page's modification date */
		'user_id' 		=> null,	/** Id of user who create page */
		'language'		=> null,	/** Language of page (@since 2.0.8) */
	);
}
