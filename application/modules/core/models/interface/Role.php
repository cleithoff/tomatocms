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
 * @version 	$Id: Role.php 4519 2010-08-12 09:36:17Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Role
{
	/**
	 * For ACL
	 * Get all roles
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getAclRoles();
	
	/**
	 * Get all roles
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getRoles();
	
	/**
	 * Get role by given id
	 * 
	 * @param int $id Id of role
	 * @return Core_Models_Role
	 */
	public function getById($id);
	
	/**
	 * Add new role
	 * 
	 * @param Core_Models_Role $role
	 * @return int
	 */
	public function add($role);
	
	/**
	 * Toggle locking status of role
	 * 
	 * @param int $id Id of role
	 * @return int
	 */
	public function toggleLock($id);
	
	/**
	 * Delete role by given Id
	 * 
	 * @param int $id Id of role
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * For view helper
	 * List all roles including number of users in each role
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getRolesIncludeUser();
	
	/**
	 * Count the number of users who have given role
	 * 
	 * @param int $roleId Id of role
	 * @return int
	 */
	public function countUsers($roleId);
}
