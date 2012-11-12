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
 * @version 	$Id: GlobalKey.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_GlobalKey 
{
	/**
	 * If you do not want to log the HTTP request (by Core_Controllers_Plugin_RequestLogger plugin), 
	 * set this key to false in your controller action 
	 */
	const LOG_REQUEST  = 'Tomato_GlobalKey_Log_Request';
	
	/**
	 * Use to set/get application template.
	 */
	const APP_TEMPLATE = 'Tomato_GlobalKey_App_Template';
	
	/**
	 * Use to set/get application theme.
	 */
	const APP_THEME    = 'Tomato_GlobalKey_App_Theme';
}
