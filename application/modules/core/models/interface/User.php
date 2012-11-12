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
 * @version 	$Id: User.php 4521 2010-08-12 09:37:29Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_User
{
	/**
	 * Get user instance by username and password
	 * 
	 * @param string $username User's username
	 * @param string $password User's password
	 * @return Core_Models_User
	 */
	public function authenticate($username, $password);
	
	/**
	 * Get user by given Id 
	 * 
	 * @param int $id User's Id
	 * @return Core_Models_User
	 */
	public function getById($id);
	
	/**
	 * Toggle user's activated status
	 * 
	 * @param int $id User's Id
	 * @return int
	 */
	public function toggleStatus($id);
	
	/**
	 * Add new user
	 * 
	 * @param Core_Models_User $user
	 * @return int
	 */
	public function add($user);
	
	/**
	 * Update user information
	 * 
	 * @param Core_Models_User $user
	 * @return int
	 */
	public function update($user);
	
	/**
	 * Update password
	 * 
	 * @param Core_Models_User $user
	 * @return int
	 */
	public function updatePassword($user);
	
	/**
	 * Update password for user
	 * 
	 * @param string $username User's username
	 * @param string $password New user's password
	 */
	public function updatePasswordFor($username, $password);
	
	/**
	 * List all users who satisfy searching conditions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Searching conditions, includes key:
	 * - username
	 * - email
	 * - role: Id of role
	 * - status
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset = null, $count = null, $exp = null);
	
	/**
	 * Count the number of users who satisfy searching conditions
	 * 
	 * @param array $exp Searching conditions (@see find)
	 * @return int
	 */
	public function count($exp);
	
	/**
	 * Check existence of user 
	 * 
	 * @param string $checkFor Field to check. Can be username or email
	 * @param string $value
	 * @return bool
	 */
	public function exist($checkFor, $value);	
}
