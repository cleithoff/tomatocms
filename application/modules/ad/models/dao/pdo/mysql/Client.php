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
 * @version 	$Id: Client.php 5333 2010-09-07 07:23:41Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pdo_Mysql_Client extends Tomato_Model_Dao
	implements Ad_Models_Interface_Client
{
	public function convert($entity) 
	{
		return new Ad_Models_Client($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'ad_client'))
					->where('c.client_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Ad_Models_Client($row);
	}
	
	public function getClients() 
	{
		$rs = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'ad_client'))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'ad_client', 
									array(
										'client_id = ?' => $id,
									));
	}
	
	public function update($client) 
	{
		return $this->_conn->update($this->_prefix . 'ad_client', 
									array(
										'name' 		=> $client->name,
										'email' 	=> $client->email,
										'telephone' => $client->telephone,
										'address' 	=> $client->address,
									), 
									array(
										'client_id = ?' => $client->client_id,
									));
	}
	
	public function add($client) 
	{
		$this->_conn->insert($this->_prefix . 'ad_client', 
							array(
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
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix . 'ad_client'));
		if ($exp) {
			if (isset($exp['name'])) {
				$select->where("c.name LIKE '%" . addslashes($exp['name']) . "%'");
			}
			if (isset($exp['email'])) {
				$select->where("c.email LIKE '%" . addslashes($exp['email']) . "%'");
			}
			if (isset($exp['address'])) {
				$select->where("c.address LIKE '%" . addslashes($exp['address']) . "%'");
			}
		}
		$rs = $select->order('c.client_id DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix . 'ad_client'), array('num_clients' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['name'])) {
				$select->where("c.name LIKE '%" . addslashes($exp['name']) . "%'");
			}
			if (isset($exp['email'])) {
				$select->where("c.email LIKE '%" . addslashes($exp['email']). " %'");
			}
			if (isset($exp['address'])) {
				$select->where("c.address LIKE '%" . addslashes($exp['address']) . "%'");
			}
		}
		return $select->query()->fetch()->num_clients;
	}
}
