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
 * @version 	$Id: Role.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Role extends Tomato_Model_Dao
	implements Core_Models_Interface_Role
{
	public function convert($entity)
	{
		return new Core_Models_Role($entity); 
	}
	
	public function getAclRoles()
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_role AS r
				LEFT JOIN ' . $this->_prefix . 'core_role_inheritance AS i
					ON r.role_id = i.child_id
				ORDER BY role_id DESC, ordering DESC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getRoles()
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_role
				ORDER BY role_id DESC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_role WHERE role_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Core_Models_Role($row);
	}
	
	public function add($role) 
	{
		$this->_conn->insert($this->_prefix . 'core_role', array(
			'name' 		  => $role->name,
			'description' => $role->description,
			'locked' 	  => $role->locked,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_role');
	}
	
	public function toggleLock($id) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_role 
				SET locked = 1 - locked 
				WHERE role_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_role WHERE role_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getRolesIncludeUser()
	{
		$sql  = 'SELECT r.*, u2.num_users
				FROM ' . $this->_prefix . 'core_role AS r
				LEFT JOIN
				(
					SELECT role_id, COUNT(*) AS num_users
					FROM ' . $this->_prefix . 'core_user AS u
					WHERE role_id IN (SELECT role_id FROM ' . $this->_prefix . 'core_role)
					GROUP BY role_id
				) AS u2 
					ON r.role_id = u2.role_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function countUsers($roleId)
	{
		$sql  = 'SELECT COUNT(*) AS num_users FROM ' . $this->_prefix . 'core_user 
				WHERE role_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($roleId));
		$numUsers = $stmt->fetch()->num_users;
		$stmt->closeCursor();
		return $numUsers;
	}
}
