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
 * @version 	$Id: Zone.php 5277 2010-09-02 04:01:38Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Mysql_Zone extends Tomato_Model_Dao
	implements Ad_Models_Interface_Zone
{
	public function convert($entity) 
	{
		return new Ad_Models_Zone($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "ad_zone 
						WHERE zone_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Ad_Models_Zone(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function getZones() 
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "ad_zone";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "ad_zone WHERE zone_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($zone) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "ad_zone
						SET name = '%s', description = '%s', width = '%s', height = '%s'
						WHERE zone_id = '%s'",
						mysql_real_escape_string($zone->name),
						mysql_real_escape_string($zone->description),
						mysql_real_escape_string($zone->width),
						mysql_real_escape_string($zone->height),
						mysql_real_escape_string($zone->zone_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function add($zone) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_zone (name, description, width, height)
						VALUES ('%s', '%s', '%s', '%s')",
						mysql_real_escape_string($zone->name),
						mysql_real_escape_string($zone->description),
						mysql_real_escape_string($zone->width),
						mysql_real_escape_string($zone->height));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function exist($name)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_zones FROM " . $this->_prefix . "ad_zone
						WHERE name = '%s'
						LIMIT 1",
						mysql_real_escape_string($name));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return ($row->num_zones == 0) ? false : true;
	}
}
