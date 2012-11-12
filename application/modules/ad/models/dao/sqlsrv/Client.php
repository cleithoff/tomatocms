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
 * @version 	$Id: Client.php 4951 2010-08-25 17:59:29Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Sqlsrv_Client extends Tomato_Model_Dao
	implements Ad_Models_Interface_Client
{
	public function convert($entity) 
	{
		return new Ad_Models_Client($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'ad_client 
				WHERE client_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$return = (null == $row) ? null : new Ad_Models_Client($row);
		$stmt->closeCursor();
		return $return;
	}
	
	public function getClients() 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'ad_client';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'ad_client WHERE client_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $numRows;
	}
	
	public function update($client) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'ad_client
				SET name = ?, email = ?, telephone = ?, address = ?
				WHERE client_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($client->name, $client->email, $client->telephone, $client->address, $client->client_id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function add($client) 
	{
		$this->_conn->insert($this->_prefix . 'ad_client', array(
			'name' 		   => $client->name, 
			'email' 	   => $client->email,
			'telephone'    => $client->telephone,
			'address' 	   => $client->address,
			'created_date' => $client->created_date,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'ad_client');
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
		$sql .= ' ORDER BY client_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor(); 
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
		$this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$numClients = $stmt->fetch()->num_clients;
		$stmt->closeCursor(); 
		return $numClients;
	}
}
