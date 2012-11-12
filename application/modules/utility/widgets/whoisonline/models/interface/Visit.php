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
 * @version 	$Id: Visit.php 5394 2010-09-12 16:05:40Z huuphuoc $
 * @since		2.0.9
 */

interface Utility_Widgets_WhoIsOnline_Models_Interface_Visit
{
	/**
	 * Check whether the visitor is already online
	 * 
	 * @param string $ip
	 * @return bool
	 */
	public function isOnline($ip);
	
	/**
	 * Add new visitor
	 * 
	 * @param Utility_Widgets_WhoIsOnline_Models_Visit $visit
	 * @return int
	 */
	public function add($visit);
	
	/**
	 * Update the visit time
	 * 
	 * @param Utility_Widgets_WhoIsOnline_Models_Visit $visit
	 * @return int
	 */
	public function update($visit);
	
	/**
	 * Delete all visits which are not updated in certain time
	 * 
	 * @param int $time The time in seconds
	 * @return int
	 */
	public function deleteByTime($time);
	
	/**
	 * Get total number of online visitors
	 * 
	 * @param bool $isRegistered TRUE if we want to get the number of online registered users
	 * @return int
	 */
	public function count($isRegistered);
	
	/**
	 * Gets the online users 
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getOnlineUsers();
}
