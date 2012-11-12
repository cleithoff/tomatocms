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
 * @version 	$Id: Privilege.php 4537 2010-08-12 09:53:42Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Privilege
{
	/**
	 * Get all privileges
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getPrivileges();
	
	/**
	 * Get privilege by Id
	 * 
	 * @param int $id Id of privilege
	 * @return Core_Models_Privilege
	 */
	public function getById($id);

	/**
	 * Add new privilege
	 * 
	 * @param Core_Models_Privilege $privilege
	 * @return int
	 */
	public function add($privilege);
	
	/**
	 * Delete privilege by given Id
	 * 
	 * @param int $id Id of privilege
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * For view helper
	 * List all role privileges belonging to given resource 
	 * 
	 * @param Core_Models_Resource $resource
	 * @param int $roleId Id of role
	 * @return Tomato_Model_RecordSet
	 */
	public function getByRole($resource, $roleId);
	
	/**
	 * For view helper
	 * List all user privileges belonging to given resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @param int $userId User's Id
	 * @return Tomato_Model_RecordSet
	 */
	public function getByUser($resource, $userId);
}
