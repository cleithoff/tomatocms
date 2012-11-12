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
 * @version 	$Id: Zone.php 4951 2010-08-25 17:59:29Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Sqlsrv_Zone extends Tomato_Model_Dao
	implements Ad_Models_Interface_Zone
{
	public function convert($entity) 
	{
		return new Ad_Models_Zone($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'ad_zone 
				WHERE zone_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Ad_Models_Zone($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function getZones() 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'ad_zone';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'ad_zone WHERE zone_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $numRows;
	}
	
	public function update($zone) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'ad_zone
				SET name = ?, description = ?, width = ?, height = ?
				WHERE zone_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$zone->name,
			$zone->description,
			$zone->width,
			$zone->height,
			$zone->zone_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function add($zone) 
	{
		$this->_conn->insert($this->_prefix . 'ad_zone', array(
			'name' 		  => $zone->name,
			'description' => $zone->description,
			'width' 	  => $zone->width,
			'height' 	  => $zone->height,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'ad_zone');
	}
	
	public function exist($name)
	{
		$sql  = 'SELECT COUNT(*) AS num_zones FROM ' . $this->_prefix . 'ad_zone
				WHERE name = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($name));
		$row = $stmt->fetch()->num_zones;
		$stmt->closeCursor();
		return ($row == 0) ? false : true;
	}
}
