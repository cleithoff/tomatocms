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
 * @version 	$Id: Zone.php 5415 2010-09-14 03:43:07Z leha $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pgsql_Zone extends Tomato_Model_Dao
	implements Ad_Models_Interface_Zone
{
	public function convert($entity) 
	{
		return new Ad_Models_Zone($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "ad_zone 
						WHERE zone_id = %s
						LIMIT 1", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Ad_Models_Zone(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function getZones() 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'ad_zone';
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'ad_zone', 
						array(
							'zone_id' => $id,
						));
	}
	
	public function update($zone) 
	{
		return pg_update($this->_conn, $this->_prefix . 'ad_zone', 
						array(
							'name' 		  => $zone->name,
							'description' => $zone->description,
							'width' 	  => $zone->width,
							'height' 	  => $zone->height,
						), 
						array(
							'zone_id'	  => $zone->zone_id,
						));
	}
	
	public function add($zone) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_zone (name, description, width, height)
						VALUES ('%s', '%s', %s, %s) 
						RETURNING zone_id",
						pg_escape_string($zone->name),
						pg_escape_string($zone->description),
						pg_escape_string($zone->width),
						pg_escape_string($zone->height));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->zone_id;
	}
	
	public function exist($name)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_zones FROM " . $this->_prefix . "ad_zone
						WHERE name = '%s'
						LIMIT 1",
						pg_escape_string($name));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return ($row->num_zones == 0) ? false : true;
	}
}
