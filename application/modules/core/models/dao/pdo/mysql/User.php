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
 * @version 	$Id: User.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_User extends Tomato_Model_Dao
	implements Core_Models_Interface_User
{
	public function convert($entity)
	{
		return new Core_Models_User($entity); 
	}
	
	public function authenticate($username, $password)
	{
		$row = $this->_conn
					->select()
					->from(array('u' => $this->_prefix . 'core_user'))
					->joinLeft(array('r' => $this->_prefix . 'core_role'), 'u.role_id = r.role_id', array('role_name' => 'name'))
					->where('u.user_name = ?', $username)
					->where('u.password = ?', $password)
					->limit(1)
    				->query()
    				->fetch();
    	return (null == $row) ? null : new Core_Models_User($row);
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('u' => $this->_prefix . 'core_user'))
					->where('u.user_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
    	return (null == $row) ? null : new Core_Models_User($row);
	}
	
	public function toggleStatus($id) 
	{
		return $this->_conn->update($this->_prefix . 'core_user', 
									array(
										'is_active' => new Zend_Db_Expr('1 - is_active'),
									), 
									array(
										'user_id = ?' => $id,
									));
	}
	
	public function add($user) 
	{
		$this->_conn->insert($this->_prefix . 'core_user', 
							array(
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
		$data = array(
			'user_name' => $user->user_name,
			'full_name' => $user->full_name,
			'email' 	=> $user->email,
			'role_id' 	=> $user->role_id,
		);
		if (null != $user->password && $user->password != '') {
			$data['password'] = md5($user->password);
		} 
		return $this->_conn->update($this->_prefix . 'core_user', 
									$data, 
									array(
										'user_id = ?' => $user->user_id,
									));
	}
	
	public function updatePassword($user) 
	{
		return $this->_conn->update($this->_prefix . 'core_user', 
									array(
										'password' => md5($user->password),
									),
									array(
										'user_id = ?' => $user->user_id,
									));
	}
	
	public function updatePasswordFor($username, $password)
	{
		return $this->_conn->update($this->_prefix . 'core_user', 
									array(
										'password' => md5($password),
									), 
									array(
										'user_name = ?' => $username,
									));
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$select = $this->_conn
						->select()
						->from(array('u' => $this->_prefix . 'core_user'));
		if ($exp) {
			if (isset($exp['username'])) {
				$select->where('u.user_name = ?', $exp['username']);
			}
			if (isset($exp['email'])) {
				$select->where('u.email = ?', $exp['email']);
			}
			if (isset($exp['role']) && $exp['role'] != '') {
				$select->where('u.role_id = ?', $exp['role']);
			}
			if (isset($exp['status']) && ($exp['status'] == '0' || $exp['status'] == 1)) {
				$select->where('u.is_active = ?', $exp['status']);
			}
		}
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp)
	{
		$select = $this->_conn
						->select()
						->from(array('u' => $this->_prefix . 'core_user'), array('num_users' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['username'])) {
				$select->where('u.user_name = ?', $exp['username']);
			}
			if (isset($exp['email'])) {
				$select->where('u.email = ?', $exp['email']);
			}
			if (isset($exp['role']) && $exp['role'] != '') {
				$select->where('u.role_id = ?', $exp['role']);
			}
			if (isset($exp['status']) && ($exp['status'] == '0' || $exp['status'] == 1)) {
				$select->where('u.is_active = ?', $exp['status']);
			}
		}
		return $select->limit(1)->query()->fetch()->num_users;
	}
	
	public function exist($checkFor, $value)
	{
		$select = $this->_conn
					->select()
					->from(array('u' => $this->_prefix . 'core_user'), array('num_users' => 'COUNT(*)'));
		switch ($checkFor) {
			case 'username':
				$select->where('u.user_name = ?', $value);
				break;
			case 'email':
				$select->where('u.email = ?', $value);
				break;
		}
		$numUsers = $select->limit(1)->query()->fetch()->num_users;
		return ($numUsers == 0) ? false : true;
	}
}
