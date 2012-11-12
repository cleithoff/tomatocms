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
 * @version 	$Id: Session.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Session extends Tomato_Model_Dao
	implements Core_Models_Interface_Session
{
	public function convert($entity)
	{
		return new Core_Models_Session($entity);
	}	
	
	public function delete($id)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_session WHERE session_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$stmt->closeCursor();
		return true;
	}

	public function destroy($time)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_session WHERE modified + lifetime < ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($time));
		$stmt->closeCursor();
	}
	
	public function getById($id)
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_session WHERE session_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Core_Models_Session($row);		
	}
	
	public function insert($session)
	{
		return $this->_conn->insert($this->_prefix . 'core_session', array(
			'session_id' => $session->session_id, 
			'data' 		 => $session->data, 
			'modified' 	 => time(), 
			'lifetime' 	 => $session->lifetime,
		));
	}
	
	public function update($session)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_session 
				SET data = ?, modified = ?, lifetime = ?
				WHERE session_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($session->data, time(), $session->lifetime, $session->session_id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
}
