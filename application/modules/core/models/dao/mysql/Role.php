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
 * @version 	$Id: Role.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Role extends Tomato_Model_Dao
	implements Core_Models_Interface_Role
{
	public function convert($entity)
	{
		return new Core_Models_Role($entity); 
	}
	
	public function getAclRoles()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_role AS r
				LEFT JOIN " . $this->_prefix . "core_role_inheritance AS i
					ON r.role_id = i.child_id
				ORDER BY role_id DESC, ordering DESC";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getRoles()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_role
				ORDER BY role_id DESC";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_role
						WHERE role_id = '%s' LIMIT 1",
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Role(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($role) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_role (name, description, locked)
						VALUES ('%s', '%s', '%s')",
						mysql_real_escape_string($role->name),
						mysql_real_escape_string($role->description),
						mysql_real_escape_string($role->locked));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function toggleLock($id) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_role 
						SET locked = 1 - locked 
						WHERE role_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_role 
						WHERE role_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getRolesIncludeUser()
	{
		$sql  = "SELECT r.*, u2.num_users
				FROM " . $this->_prefix . "core_role AS r
				LEFT JOIN
				(
					SELECT role_id, COUNT(*) AS num_users
					FROM " . $this->_prefix . "core_user AS u
					WHERE role_id IN (SELECT role_id FROM " . $this->_prefix . "core_role)
					GROUP BY role_id
				) AS u2 
					ON r.role_id = u2.role_id";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countUsers($roleId)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_users FROM " . $this->_prefix . "core_user 
						WHERE role_id = '%s' LIMIT 1", 
						mysql_real_escape_string($roleId));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_users;
	}
}
