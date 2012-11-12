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
 * @version 	$Id: User.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_User extends Tomato_Model_Dao 
	implements Core_Models_Interface_User
{
	public function convert($entity)
	{
		return new Core_Models_User($entity); 
	}
	
	public function authenticate($username, $password)
	{
		$sql = sprintf("SELECT u.*, r.name AS role_name FROM " . $this->_prefix . "core_user AS u
						LEFT JOIN " . $this->_prefix . "core_role AS r
							ON u.role_id = r.role_id
						WHERE u.user_name = '%s' AND u.password = '%s'
						LIMIT 1",
						mysql_real_escape_string($username), 
						mysql_real_escape_string($password));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_User(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;	
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT u.* FROM " . $this->_prefix . "core_user AS u WHERE u.user_id = '%s' LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_User(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function toggleStatus($id) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_user SET is_active = 1 - is_active WHERE user_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function add($user) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_user (user_name, password, full_name, email, is_active, created_date, logged_in_date, is_online, role_id)
		    			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', NULL, '%s', '%s')", 
						mysql_real_escape_string($user->user_name),
						mysql_real_escape_string(md5($user->password)),
						mysql_real_escape_string($user->full_name),
						mysql_real_escape_string($user->email),
						mysql_real_escape_string($user->is_active),
						mysql_real_escape_string($user->created_date),
						mysql_real_escape_string($user->is_online),
						mysql_real_escape_string($user->role_id));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($user) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_user 
						SET user_name = '%s', full_name = '%s', email = '%s', role_id = '%s'",
						mysql_real_escape_string($user->user_name), 
						mysql_real_escape_string($user->full_name), 
						mysql_real_escape_string($user->email), 
						mysql_real_escape_string($user->role_id));						
		if (null != $user->password && $user->password != '') {
			$sql .= sprintf(", password = '?'", mysql_real_escape_string(md5($user->password)));
		}
		$sql .= sprintf(" WHERE user_id = ?", mysql_real_escape_string($user->user_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function updatePassword($user) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_user SET password = '%s' WHERE user_id = %s",
						 md5($user->password),
						 mysql_real_escape_string($user->user_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function updatePasswordFor($username, $password)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_user SET password = '%s' WHERE user_name = '%s'",
						 md5($password),
						 mysql_real_escape_string($username));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "core_user AS u";
		if ($exp) {
			$where = array();
			
			if (isset($exp['username'])) {
				$where[] = sprintf("u.user_name = '%s'", mysql_real_escape_string($exp['username']));
			}
			if (isset($exp['email'])) {
				$where[] = sprintf("u.email = '%s'", mysql_real_escape_string($exp['email']));
			}
			if (isset($exp['role']) && $exp['role'] != '') {
				$where[] = sprintf("u.role_id = '%s'", mysql_real_escape_string($exp['role']));
			}
			if (isset($exp['status']) && ($exp['status'] == '0' || $exp['status'] == 1)) {
				$where[] = sprintf("u.is_active = '%s'", mysql_real_escape_string($exp['status']));
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp)
	{
		$sql = "SELECT COUNT(*) AS num_users FROM " . $this->_prefix . "core_user AS u";
		if ($exp) {
			$where = array();
			
			if (isset($exp['username'])) {
				$where[] = sprintf("u.user_name = '%s'", mysql_real_escape_string($exp['username']));
			}
			if (isset($exp['email'])) {
				$where[] = sprintf("u.email = '%s'", mysql_real_escape_string($exp['email']));
			}
			if (isset($exp['role']) && $exp['role'] != '') {
				$where[] = sprintf("u.role_id = '%s'", mysql_real_escape_string($exp['role']));
			}
			if (isset($exp['status']) && ($exp['status'] == '0' || $exp['status'] == 1)) {
				$where[] = sprintf("u.is_active = '%s'", mysql_real_escape_string($exp['status']));
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_users;
	}
	
	public function exist($checkFor, $value)
	{
		$sql = "SELECT COUNT(*) AS num_users FROM " . $this->_prefix . "core_user AS u";
		switch ($checkFor) {
			case 'username':
				$sql .= sprintf(" WHERE u.user_name = '%s'", mysql_real_escape_string($value)); 
				break;
			case 'email':
				$sql .= sprintf(" WHERE u.email = '%s'", mysql_real_escape_string($value));
				break;
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return ($row->num_users == 0) ? false : true;
	}
}
