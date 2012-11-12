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
 * @version 	$Id: Session.php 5461 2010-09-20 04:43:06Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Session extends Tomato_Model_Dao
	implements Core_Models_Interface_Session
{
	public function convert($entity)
	{
		return new Core_Models_Session($entity);
	}	
	
	public function delete($id)
	{
		pg_delete($this->_conn, $this->_prefix . 'core_session', 
							array(
								'session_id' => $id,
							));
		return true;
	}

	public function destroy($time)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_session WHERE modified + lifetime < %s",
						pg_escape_string($time));
		pg_query($sql);
	}
	
	public function getById($id)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_session WHERE session_id = '%s' LIMIT 1",
						pg_escape_string($id));
		$rs  = pg_query($sql);
		if (0 == pg_num_rows($rs)) {
			return null;
		}
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return new Core_Models_Session(array(
					'session_id' => $row->session_id,
					'data' 		 => stripslashes($row->data),
					'modified' 	 => $row->modified,
					'lifetime' 	 => $row->lifetime,
				));
	}
	
	public function insert($session)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_session (session_id, data, modified, lifetime) 
						VALUES ('%s', '%s', '%s', '%s')",
						pg_escape_string($session->session_id), 
						pg_escape_string(addslashes($session->data)),
						time(),
						pg_escape_string($session->lifetime));
						
		$rs  = pg_query($sql);
		return pg_affected_rows($rs);
	}
	
	public function update($session)
	{
		return pg_update($this->_conn, $this->_prefix . 'core_session', 
						array(
							'data' 	   => addslashes($session->data),
							'modified' => time(),
							'lifetime' => $session->lifetime,
						), 
						array(
							'session_id' => $session->session_id,
						));
	}
}
