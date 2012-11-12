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
 * @version 	$Id: Dashboard.php 5474 2010-09-20 08:53:39Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Represents a dashboard
 */
class Core_Models_Dashboard extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'dashboard_id' => null,		/** Id of dashboard */
		'user_id' 	   => null,		/** Id of user */
		'user_name'    => null,		/** Username of user */
		'layout' 	   => null,		/** The dashboard data in JSON format */
	
		/** 
		 * Defines the dashboard as the default or not. It can be 0 or 1.
		 * If the user dashboard is empty, the back-end will show the default dashboard
		 */
		'is_default'   => 0,		
	);
}
