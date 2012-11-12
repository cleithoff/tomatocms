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
 * @version 	$Id: Resource.php 5428 2010-09-14 08:54:07Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Resource extends Tomato_Model_Dao
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
			$sql .= sprintf(" WHERE module_name = '%s'", pg_escape_string($module));
		}
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_resource
						WHERE resource_id = %s LIMIT 1",
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Core_Models_Resource(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($resource) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_resource (description, parent_id, module_name, controller_name)
						VALUES ('%s', %s, '%s', '%s')
						RETURNING resource_id",
						pg_escape_string($resource->description),
						($resource->parent_id) ? "'".pg_escape_string($resource->parent_id)."'" : 'NULL',
						pg_escape_string($resource->module_name),
						pg_escape_string($resource->controller_name));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->resource_id;
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_resource', 
						array(
							'resource_id' => $id,
						));
	}	
}
