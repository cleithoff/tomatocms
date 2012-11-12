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
 * @version 	$Id: Client.php 4513 2010-08-12 09:31:43Z huuphuoc $
 * @since		2.0.5
 */

interface Ad_Models_Interface_Client
{
	/**
	 * Get client by given Id
	 * 
	 * @param int $id Id of client
	 * @return Ad_Models_Client
	 */
	public function getById($id);

	/**
	 * Get all clients
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getClients();
	
	/**
	 * Delete client by given Id
	 * 
	 * @param int $id Id of client
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Update client
	 * 
	 * @param Ad_Models_Client $client
	 * @return int
	 */
	public function update($client);
	
	/**
	 * Add new client
	 * 
	 * @param Ad_Models_Client $client
	 * @return int
	 */
	public function add($client);
	
	/**
	 * Search for all clients by given conditions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * - name
	 * - email
	 * - address
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset, $count, $exp = null);
	
	/**
	 * Count number of clients who satisfy searching conditions
	 * 
	 * @param array $exp Search expression (@see find)
	 * @return int
	 */
	public function count($exp = null);
}
