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
 * @version 	$Id: Log.php 5474 2010-09-20 08:53:39Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Represents an error log
 */
class Core_Models_Log extends Tomato_Model_Entity
{
	protected $_properties = array(
		'log_id'       => null,		/** Id of log */
		'created_date' => null,		/** Creation date of log */
		'uri'		   => null,		/** URI of page that causes log */
		'module'       => null,		/** The name of module that causes log */
		'controller'   => null,		/** The name of controller that causes log */
		'action' 	   => null,		/** The name of action that causes log */
		'class' 	   => null,		/** The name of class that causes log */
		'file'		   => null,		/** The name of file that causes log */
		'line'		   => null,		/** The line in file that causes log */
		'message' 	   => null,		/** The error message */
		'trace'		   => null,		/** The log trace */
	);
}
