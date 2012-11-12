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
 * @version 	$Id: Hook.php 5474 2010-09-20 08:53:39Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a hook
 */
class Core_Models_Hook extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'hook_id' 	  => null,		/** Id of hook */
		'module' 	  => null,		/** Module of hook */
		'name' 		  => null,		/** Name of hook */
		'description' => null,		/** Description of hook */
		'thumbnail'   => null,		/** URL of thumbnail image that represents hook */
		'author' 	  => null,		/** Author of hook */
		'email' 	  => null,		/** Email address of author */
		'version' 	  => null,		/** Version of hook */
		'license'	  => null,		/** Hook license information */
	);
}
