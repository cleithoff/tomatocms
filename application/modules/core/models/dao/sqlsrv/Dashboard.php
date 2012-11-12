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
 * @version 	$Id: Dashboard.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.7
 */

class Core_Models_Dao_Sqlsrv_Dashboard extends Tomato_Model_Dao
	implements Core_Models_Interface_Dashboard
{
	public function convert($entity)
	{
		return new Core_Models_Dashboard($entity); 
	}
	
	public function getDefault()
	{	
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'core_dashboard as d WHERE d.is_default = 1';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Core_Models_Dashboard($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function getByUser($userId)
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'core_dashboard as d WHERE d.user_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($userId));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Core_Models_Dashboard($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function create($dashboard)
	{		
		$this->_conn->insert($this->_prefix . 'core_hook', array(
			'user_id' 	  => $dashboard->user_id,
			'user_name'   => (string)$dashboard->user_name,
			'layout' 	  => (string)$dashboard->layout,
			'is_default'  => 0,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_dashboard');
	}
	
	public function update($dashboard)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_dashboard
				SET layout = ? WHERE dashboard_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$dashboard->layout,
			$dashboard->dashboard_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
}
