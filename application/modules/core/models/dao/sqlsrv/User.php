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
 * @version 	$Id: User.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_User extends Tomato_Model_Dao 
	implements Core_Models_Interface_User
{
	public function convert($entity)
	{
		return new Core_Models_User($entity); 
	}
	
	public function authenticate($username, $password)
	{
		$sql  = 'SELECT u.*, r.name AS role_name FROM ' . $this->_prefix . 'core_user AS u
				LEFT JOIN ' . $this->_prefix . 'core_role AS r
					ON u.role_id = r.role_id
				WHERE u.user_name = ?
				AND u.password = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($username, $password));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Core_Models_User($row);	
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_user WHERE user_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Core_Models_User($row);
	}
	
	public function toggleStatus($id) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_user 
				SET is_active = 1 - is_active 
				WHERE user_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function add($user) 
	{
		$this->_conn->insert($this->_prefix . 'core_user', array(
			'user_name' 	 => $user->user_name,
			'password' 		 => md5($user->password),
			'full_name' 	 => $user->full_name,
			'email' 		 => $user->email,
			'is_active' 	 => $user->is_active,
			'created_date' 	 => $user->created_date,
			'logged_in_date' => $user->logged_in_date,
			'is_online' 	 => $user->is_online,
			'role_id' 		 => $user->role_id,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_user');
	}
	
	public function update($user) 
	{
		$sql    = 'UPDATE ' . $this->_prefix . 'core_user SET user_name = ?, full_name = ?, email = ?, role_id = ?';
		$params = array(
			$user->user_name, 
			$user->full_name, 
			$user->email, 
			$user->role_id,
		);	
		if (null != $user->password && $user->password != '') {
			$sql 	 .= ', password = ?';
			$params[] = md5($user->password);
		}
		$sql 	 .= ' WHERE user_id = ?';
		$params[] = $user->user_id;
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function updatePassword($user) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_user SET password = ? WHERE user_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(md5($user->password), $user->user_id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function updatePasswordFor($username, $password)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_user SET password = ? WHERE user_name = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(md5($password), $username));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$sql    = 'SELECT * FROM ' . $this->_prefix . 'core_user AS u';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['username'])) {
				$where[]  = 'u.user_name = ?';
				$params[] = $exp['username'];
			}
			if (isset($exp['email'])) {
				$where[]  = 'u.email = ?';
				$params[] = $exp['email'];
			}
			if (isset($exp['role']) && $exp['role'] != '') {
				$where[]  = 'u.role_id = ?';
				$params[] = $exp['role'];
			}
			if (isset($exp['status']) && ($exp['status'] == '0' || $exp['status'] == 1)) {
				$where[]  = 'u.is_active = ?';
				$params[] = $exp['status'];
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp)
	{
		$sql    = 'SELECT COUNT(*) AS num_users FROM ' . $this->_prefix . 'core_user AS u';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['username'])) {
				$where[]  = 'u.user_name = ?';
				$params[] = $exp['username'];
			}
			if (isset($exp['email'])) {
				$where[]  = 'u.email = ?';
				$params[] = $exp['email'];
			}
			if (isset($exp['role']) && $exp['role'] != '') {
				$where[]  = 'u.role_id = ?';
				$params[] = $exp['role'];
			}
			if (isset($exp['status']) && ($exp['status'] == '0' || $exp['status'] == 1)) {
				$where[]  = 'u.is_active = ?';
				$params[] = $exp['status'];
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$numUsers = $stmt->fetch()->num_users;
		$stmt->closeCursor();
		return $numUsers;
	}
	
	public function exist($checkFor, $value)
	{
		$sql = 'SELECT COUNT(*) AS num_users FROM ' . $this->_prefix . 'core_user AS u';
		$params = array($value);
		switch ($checkFor) {
			case 'username':
				$sql .= ' WHERE u.user_name = ?';
				break;
			case 'email':
				$sql .= ' WHERE u.email = ?';
				break;
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$numUsers = $stmt->fetch()->num_users;
		$stmt->closeCursor();
		return ($numUsers == 0) ? false : true;
	}
}
