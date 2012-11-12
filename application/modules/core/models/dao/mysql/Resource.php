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
 * @version 	$Id: Resource.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Resource extends Tomato_Model_Dao
	implements Core_Models_Interface_Resource
{
	public function convert($entity)
	{
		return new Core_Models_Resource($entity); 
	}
	
	public function getResources($module = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "core_resource";
		if ($module) {
			$sql .= sprintf(" WHERE module_name = '%s'", mysql_real_escape_string($module));
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_resource
						WHERE resource_id = '%s' LIMIT 1",
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Resource(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($resource) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_resource (description, parent_id, module_name, controller_name)
						VALUES ('%s', '%s', '%s', '%s')",
						mysql_real_escape_string($resource->description),
						mysql_real_escape_string($resource->parent_id),
						mysql_real_escape_string($resource->module_name),
						mysql_real_escape_string($resource->controller_name));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_resource
						WHERE resource_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows(); 
	}	
}
