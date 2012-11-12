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
 * @version 	$Id: Rule.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Rule
{
	/**
	 * For ACL
	 * Get all rules
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getAclRules();
	
	/**
	 * Remove all rules associated with given privilege
	 * 
	 * @param int $privilegeId Id of privilege
	 * @return int
	 */
	public function removeRules($privilegeId);
	
	/**
	 * Add new rule
	 * 
	 * @param Core_Models_Rule $rule
	 * @return int
	 */
	public function add($rule);
	
	/**
	 * Remove all rules from given user
	 * 
	 * @param int $userId
	 * @return int
	 */
	public function removeFromUser($userId);
	
	/**
	 * Remove all rules from given role
	 * 
	 * @param int $roleId
	 * @return int
	 */
	public function removeFromRole($roleId);
	
	/**
	 * Remove all rules associated with given resource
	 * 
	 * @param string $name Name of resource
	 */
	public function removeByResource($name);
}
