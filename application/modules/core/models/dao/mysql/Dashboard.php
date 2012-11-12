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
 * @version 	$Id: Dashboard.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.7
 */

class Core_Models_Dao_Mysql_Dashboard extends Tomato_Model_Dao
	implements Core_Models_Interface_Dashboard
{
	public function convert($entity)
	{
		return new Core_Models_Dashboard($entity); 
	}
	
	public function getDefault()
	{
		$sql = "SELECT d.* FROM " . $this->_prefix . "core_dashboard AS d WHERE d.is_default = 1 LIMIT 1";
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Dashboard(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function getByUser($userId)
	{
		$sql = sprintf("SELECT d.* FROM " . $this->_prefix . "core_dashboard AS d WHERE d.user_id = '%s' LIMIT 1", 
						mysql_real_escape_string($userId));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Dashboard(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;		
	}
	
	public function create($dashboard)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_dashboard (user_id, user_name, layout, is_default)
		    			VALUES ('%s', '%s', '%s', 0)", 
						mysql_real_escape_string($dashboard->user_id),
						mysql_real_escape_string($dashboard->user_name),
						mysql_real_escape_string($dashboard->layout));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($dashboard)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_dashboard 
						SET layout = '%s' 
						WHERE dashboard_id = %s",
						mysql_real_escape_string($dashboard->layout),
						mysql_real_escape_string($dashboard->dashboard_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
