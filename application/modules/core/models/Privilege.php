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
 * @version 	$Id: Privilege.php 5476 2010-09-20 09:03:39Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents user's privilege
 */
class Core_Models_Privilege extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'privilege_id' 	  => null,		/** Id of privilege */
		'name' 			  => null,		/** Name of privilege. It is name of controller action */
		'description' 	  => null,		/** Description of privilege */
		'module_name' 	  => null,		/** Name of module */
		'controller_name' => null,		/** Name of controller */
	);
}
