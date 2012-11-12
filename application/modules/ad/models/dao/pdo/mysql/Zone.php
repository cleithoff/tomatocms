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
 * @version 	$Id: Zone.php 5333 2010-09-07 07:23:41Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pdo_Mysql_Zone extends Tomato_Model_Dao
	implements Ad_Models_Interface_Zone
{
	public function convert($entity) 
	{
		return new Ad_Models_Zone($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('z' => $this->_prefix . 'ad_zone'))
					->where('z.zone_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Ad_Models_Zone($row);
	}
	
	public function getZones() 
	{
		$rs = $this->_conn
					->select()
					->from(array('z' => $this->_prefix . 'ad_zone'))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'ad_zone', 
									array(
										'zone_id = ?' => $id,
									));
	}
	
	public function update($zone) 
	{
		return $this->_conn->update($this->_prefix . 'ad_zone', 
									array(
										'name' 		  => $zone->name,
										'description' => $zone->description,
										'width' 	  => $zone->width,
										'height' 	  => $zone->height,
									), 
									array(
										'zone_id = ?' => $zone->zone_id,
									));
	}
	
	public function add($zone) 
	{
		$this->_conn->insert($this->_prefix . 'ad_zone', 
							array(
								'name' 		  => $zone->name,
								'description' => $zone->description,
								'width' 	  => $zone->width,
								'height' 	  => $zone->height,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'ad_zone');
	}
	
	public function exist($name)
	{
		$numZones = $this->_conn
						->select()
						->from(array('z' => $this->_prefix . 'ad_zone'), array('num_zones' => 'COUNT(*)'))
						->where('z.name = ?', $name)
					   	->limit(1)
					   	->query()
					   	->fetch()
					   	->num_zones;
		return ($numZones == 0) ? false : true;
	}
}
