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
 * @version 	$Id: Item.php 5480 2010-09-20 09:59:14Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Represents a menu item
 */
class Menu_Models_Item extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'menu_item_id' => null,		/** Auto-increment Id */
		'item_id' 	   => null,		/** Id of item */
		'label' 	   => null,		/** Label of item */
		'link' 		   => null,		/** URL of target */
		'menu_id'	   => null,		/** Id of menu */
	
		/**
		 * The left and right indeces.
		 * They are generated automatically.
		 */
		'left_id'	   => null,
		'right_id'     => null,
	
		'parent_id'    => null,		/** Id of parent item */	
	);
}
