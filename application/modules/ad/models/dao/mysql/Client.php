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
 * @version 	$Id: Client.php 5277 2010-09-02 04:01:38Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Mysql_Client extends Tomato_Model_Dao
	implements Ad_Models_Interface_Client
{
	public function convert($entity) 
	{
		return new Ad_Models_Client($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "ad_client 
						WHERE client_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
		$rs = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Ad_Models_Client(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function getClients() 
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "ad_client";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "ad_client WHERE client_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($client) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "ad_client
						SET name = '%s', email = '%s', telephone = '%s', address = '%s'
						WHERE client_id = '%s'",
						mysql_real_escape_string($client->name),
						mysql_real_escape_string($client->email),
						mysql_real_escape_string($client->telephone),
						mysql_real_escape_string($client->address),
						mysql_real_escape_string($client->client_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function add($client) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_client (name, email, telephone, address, created_date)
						VALUES ('%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($client->name),
						mysql_real_escape_string($client->email),
						mysql_real_escape_string($client->telephone),
						mysql_real_escape_string($client->address),
						mysql_real_escape_string($client->created_date));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = "SELECT * FROM " . $this->_prefix . "ad_client AS c";
		if ($exp) {
			$where = array();
			
			if (isset($exp['name'])) {
				$where[] = "c.name LIKE '%" . addslashes($exp['name']) . "%'";
			}
			if (isset($exp['email'])) {
				$where[] = "c.email LIKE '%" . addslashes($exp['email']) . "%'";
			}
			if (isset($exp['address'])) {
				$where[] = "c.address LIKE '%" . addslashes($exp['address']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY c.client_id DESC";
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
	
	public function count($exp = null) 
	{
		$sql = "SELECT COUNT(*) AS num_clients FROM " . $this->_prefix . "ad_client AS c";
		if ($exp) {
			$where = array();
			
			if (isset($exp['name'])) {
				$where[] = "c.name LIKE '%" . addslashes($exp['name']) . "%'";
			}
			if (isset($exp['email'])) {
				$where[] = "c.email LIKE '%" . addslashes($exp['email']) . "%'";
			}
			if (isset($exp['address'])) {
				$where[] = "c.address LIKE '%" . addslashes($exp['address']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_clients;
	}
}
