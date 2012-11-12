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
 * @version 	$Id: Dashboard.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.7
 */

class Core_Models_Dao_Pdo_Mysql_Dashboard extends Tomato_Model_Dao
	implements Core_Models_Interface_Dashboard
{
	public function convert($entity)
	{
		return new Core_Models_Dashboard($entity); 
	}
	
	public function getDefault()
	{
		$row = $this->_conn
					->select()
					->from(array('d' => $this->_prefix . 'core_dashboard'))
					->where('d.is_default = ?', 1)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Dashboard($row);
	}
	
	public function getByUser($userId)
	{
		$row = $this->_conn
					->select()
					->from(array('d' => $this->_prefix . 'core_dashboard'))
					->where('d.user_id = ?', $userId)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Dashboard($row);
	}
	
	public function create($dashboard)
	{
		$this->_conn->insert($this->_prefix . 'core_dashboard', 
							array(
								'user_id' 	 => $dashboard->user_id,
								'user_name'  => $dashboard->user_name,
								'layout'	 => $dashboard->layout,
								'is_default' => 0,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_dashboard');
	}
	
	public function update($dashboard)
	{
		return $this->_conn->update($this->_prefix . 'core_dashboard', 
									array(
										'layout' => $dashboard->layout,
									), 
									array(
										'dashboard_id = ?' => $dashboard->dashboard_id,
									));
	}
}
