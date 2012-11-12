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
 * @version 	$Id: Session.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Session extends Tomato_Model_Dao
	implements Core_Models_Interface_Session
{
	public function convert($entity)
	{
		return new Core_Models_Session($entity);
	}	
	
	public function delete($id)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_session WHERE session_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return true;
	}

	public function destroy($time)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_session WHERE modified + lifetime < %s",
						mysql_real_escape_string($time));
		mysql_query($sql);
	}
	
	public function getById($id)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_session WHERE session_id = '%s' LIMIT 1",
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Session(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function insert($session)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_session (session_id, data, modified, lifetime) 
						VALUES ('%s', '%s', '%s', '%s')",
						mysql_real_escape_string($session->session_id), 
						mysql_real_escape_string($session->data),
						time(),
						mysql_real_escape_string($session->lifetime));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($session)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_session 
						SET data = '%s', modified = '%s', lifetime = '%s'
						WHERE session_id = '%s'",
						mysql_real_escape_string($session->data),
						time(),
						mysql_real_escape_string($session->lifetime),
						mysql_real_escape_string($session->session_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
