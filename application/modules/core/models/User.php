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
 * @version 	$Id: User.php 5478 2010-09-20 09:59:05Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents an user
 */
class Core_Models_User extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'user_id' 		 => null,	/** Id of user */
		'user_name' 	 => null,	/** Username of user */
		'password' 		 => null,	/** Password of user */
		'full_name' 	 => null,	/** Full name of user */
		'email' 		 => null,	/** Email address of user */
		'is_active' 	 => null,	/** Defines user's activation status. Can be 0 (not activated) or 1 (activated) */
		'created_date' 	 => null,	/** User's creation date */
		'logged_in_date' => null,	/** The last logged in date */
		'is_online' 	 => null,	/** Online status. Can be 0 (offline) or 1 (online) */
		'role_id' 		 => null,	/** Id of role that user belong to */
	);
}
