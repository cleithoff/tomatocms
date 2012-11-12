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
 * @version 	$Id: Client.php 5418 2010-09-14 03:57:17Z leha $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pgsql_Client extends Tomato_Model_Dao
	implements Ad_Models_Interface_Client
{
	public function convert($entity) 
	{
		return new Ad_Models_Client($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "ad_client 
						WHERE client_id = %s
						LIMIT 1", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Ad_Models_Client(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function getClients() 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'ad_client';
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'ad_client', 
						array(
							'client_id' => $id,
						));
	}
	
	public function update($client) 
	{
		return pg_update($this->_conn, $this->_prefix . 'ad_client', 
						array(
							'name' 		=> $client->name,
							'email' 	=> $client->email,
							'telephone' => $client->telephone,
							'address' 	=> $client->address,
						), 
						array(
							'client_id' => $client->client_id,
						));
	}
	
	public function add($client) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_client (name, email, telephone, address, created_date)
						VALUES ('%s', '%s', '%s', '%s', '%s')
						RETURNING client_id",
						pg_escape_string($client->name),
						pg_escape_string($client->email),
						pg_escape_string($client->telephone),
						pg_escape_string($client->address),
						pg_escape_string($client->created_date));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);	
		return $row->client_id;
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = 'SELECT * FROM ' . $this->_prefix . 'ad_client AS c';
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
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' ORDER BY c.client_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(' LIMIT %s OFFSET %s', $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql = 'SELECT COUNT(*) AS num_clients FROM ' . $this->_prefix . 'ad_client AS c';
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
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' LIMIT 1';
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_clients;
	}
}
