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
 * @version 	$Id: Menu.php 5480 2010-09-20 09:59:14Z huuphuoc $
 * @since		2.0.2
 */

/**
 * Represents a menu
 */
class Menu_Models_Menu extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'menu_id' 	   => null,		/** Id of menu */
		'name' 		   => null,		/** Name of menu */
		'description'  => null,		/** Description of menu */
		'user_id' 	   => null,		/** Id of user who create the menu */
		'user_name'    => null,		/** Username of user who create the menu */
		'created_date' => null,		/** Menu's creation date */
		'language'     => null,		/** Language of menu (@since 2.0.8) */
	);
}
