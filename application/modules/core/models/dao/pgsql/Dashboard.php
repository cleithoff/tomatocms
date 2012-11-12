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
 * @version 	$Id: Dashboard.php 5421 2010-09-14 08:38:28Z leha $
 * @since		2.0.7
 */

class Core_Models_Dao_Pgsql_Dashboard extends Tomato_Model_Dao
	implements Core_Models_Interface_Dashboard
{
	public function convert($entity)
	{
		return new Core_Models_Dashboard($entity); 
	}
	
	public function getDefault()
	{
		$sql = "SELECT d.* FROM " . $this->_prefix . "core_dashboard AS d WHERE d.is_default = 1 LIMIT 1";
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Core_Models_Dashboard(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function getByUser($userId)
	{
		$sql = sprintf("SELECT d.* FROM " . $this->_prefix . "core_dashboard AS d WHERE d.user_id = %s LIMIT 1", 
						pg_escape_string($userId));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Core_Models_Dashboard(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function create($dashboard)
	{
		$sql = "INSERT INTO " . $this->_prefix . "core_dashboard (user_id, user_name, layout, is_default)
		    	VALUES (%s, '%s', '%s', 0)
		    	RETURNING dashboard_id";
		$sql = sprintf($sql, pg_escape_string($dashboard->user_id),
							pg_escape_string($dashboard->user_name),
							pg_escape_string($dashboard->layout));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->dashboard_id;
	}
	
	public function update($dashboard)
	{
		return pg_update($this->_conn, $this->_prefix . 'core_dashboard', 
						array(
							'layout' => $dashboard->layout,
						), 
						array(
							'dashboard_id' => $dashboard->dashboard_id,
						));	
	}
}
