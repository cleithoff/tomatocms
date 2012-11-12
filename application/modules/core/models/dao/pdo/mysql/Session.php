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
 * @version 	$Id: Session.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Session extends Tomato_Model_Dao
	implements Core_Models_Interface_Session
{
	public function convert($entity)
	{
		return new Core_Models_Session($entity);
	}	
	
	public function delete($id)
	{
		$this->_conn->delete($this->_prefix . 'core_session', 
							array(
								'session_id = ?' => $id,
							));
		return true;
	}

	public function destroy($time)
	{
		$this->_conn->delete($this->_prefix . 'core_session', 
							array(
								'modified + lifetime < ?' => $time,
							));		
	}
	
	public function getById($id)
	{
		$row = $this->_conn->select()
					->from(array('s' => $this->_prefix . 'core_session'))
					->where('s.session_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Session($row);
	}
	
	public function insert($session)
	{
		return $this->_conn->insert($this->_prefix . 'core_session', 
									array(
										'session_id' => $session->session_id,
										'data' 		 => $session->data,
										'modified' 	 => time(),
										'lifetime' 	 => $session->lifetime,
									));
	}
	
	public function update($session)
	{
		return $this->_conn->update($this->_prefix . 'core_session', 
									array(
										'data' 	   => $session->data,
										'modified' => time(),
										'lifetime' => $session->lifetime,
									), 
									array(
										'session_id = ?' => $session->session_id,
									));
	}
}
