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
 * @version 	$Id: Widget.php 5478 2010-09-20 09:59:05Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a widget
 */
class Core_Models_Widget extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'widget_id'   => null,		/** Id of widget */
		'name' 		  => null,		/** Name of widget */
		'title' 	  => null,		/** Title of widget */
		'module' 	  => null,		/** Module of widget */
		'description' => null,		/** Description of widget */
		'thumbnail'   => null,		/** URL of thumbnail image that represents widget */
		'author' 	  => null,		/** Author of widget */
		'email' 	  => null,		/** Email address of author */
		'version' 	  => null,		/** Version of widget */
		'license' 	  => null,		/** Widget license information */
	);
}
