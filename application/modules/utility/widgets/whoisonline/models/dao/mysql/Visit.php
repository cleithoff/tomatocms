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
 * @version 	$Id: Visit.php 5396 2010-09-12 17:03:28Z huuphuoc $
 * @since		2.0.9
 */

class Utility_Widgets_WhoIsOnline_Models_Dao_Mysql_Visit extends Tomato_Model_Dao
	implements Utility_Widgets_WhoIsOnline_Models_Interface_Visit
{
	
	public function convert($entity)
	{
		return new Utility_Widgets_WhoIsOnline_Models_Visit($entity);
	}
	
	public function isOnline($ip)
	{
		$sql = sprintf("SELECT COUNT(visit_id) AS num_visits FROM " . $this->_prefix . "utility_whoisonline_visit
						WHERE ip = '%s'
						LIMIT 1", mysql_real_escape_string($ip));
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_visits;
	}
	
	public function add($visit)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "utility_whoisonline_visit (ip, access_time, country, country_code, user_id, user_name)
		    			VALUES ('%s', '%s', '%s', '%s', %s, %s)", 
						mysql_real_escape_string($visit->ip),
						mysql_real_escape_string($visit->access_time),
						mysql_real_escape_string($visit->country),
						mysql_real_escape_string($visit->country_code),
						($visit->user_id == null) ? null : "'" . mysql_real_escape_string($visit->user_id)  ."'",
						($visit->user_name == null) ? null : "'" . mysql_real_escape_string($visit->user_name)  ."'");
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($visit)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "utility_whoisonline_visit 
						SET access_time = %s, user_id = %s, user_name = %s 
						WHERE ip = '%s'",
						'NOW()',
						($visit->user_id == null) ? null : "'" . mysql_real_escape_string($visit->user_id)  ."'",
						($visit->user_name == null) ? null : "'" . mysql_real_escape_string($visit->user_name)  ."'",
						$visit->ip);
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function deleteByTime($time)
	{
		if (is_int($time)) {
			$sql = sprintf("DELETE FROM " . $this->_prefix . "utility_whoisonline_visit
							WHERE access_time < '%s'",
							date('Y-m-d H:i:s', strtotime('-' . $time . 'seconds')));
			mysql_query($sql);
			return mysql_affected_rows();
		}
		return 0;
	}
	
	public function count($isRegistered)
	{
		$sql  = "SELECT COUNT(visit_id) AS num_visits FROM " . $this->_prefix . "utility_whoisonline_visit";
		if ($isRegistered === true) {
			$sql .= " WHERE user_id IS NOT NULL";
		} elseif ($isRegistered === false) {
			$sql .= " WHERE user_id IS NULL";
		}
		$sql .= " LIMIT 1";
		
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_visits;
	}
	
	public function getOnlineUsers()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "utility_whoisonline_visit
				WHERE user_id IS NOT NULL";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
