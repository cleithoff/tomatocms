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
 * @version 	$Id: Module.php 5474 2010-09-20 08:53:39Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a module
 */
class Core_Models_Module extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'module_id'   => null,		/** Id of module */
		'name' 		  => null,		/** Name of module */
		'description' => null,		/** Description of module */
		'thumbnail'   => null,		/** URL of thumbnail image that represents module */
		'author' 	  => null,		/** Author of module */
		'email' 	  => null,		/** Email address of author */
		'version' 	  => null,		/** Version of module */
		'license' 	  => null,		/** Module license information */
	);
}
