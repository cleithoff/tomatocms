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
 * @version 	$Id: Privilege.php 5427 2010-09-14 08:53:50Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Privilege extends Tomato_Model_Dao
	implements Core_Models_Interface_Privilege
{
	public function convert($entity)
	{
		return new Core_Models_Privilege($entity); 
	}
	
	public function getPrivileges()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_privilege";
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
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_privilege WHERE privilege_id = %s LIMIT 1", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Core_Models_Privilege(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($privilege) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_privilege (name, description, module_name, controller_name) 
						VALUES ('%s', '%s', '%s', '%s') 
						RETURNING privilege_id",
						pg_escape_string($privilege->name),
						pg_escape_string($privilege->description),
						pg_escape_string($privilege->module_name),
						pg_escape_string($privilege->controller_name));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		return $row->privilege_id;		
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_privilege', 
						array(
							'privilege_id' => $id,
						));
	}
	
	public function getByRole($resource, $roleId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		
		$sql = sprintf("SELECT p.privilege_id, name, description, r.allow
						FROM " . $this->_prefix . "core_privilege AS p
						LEFT JOIN " . $this->_prefix . "core_rule AS r ON r.obj_type = 'role' 
							AND r.obj_id = %s 
							AND ((r.privilege_id IS NULL AND r.resource_name IS NULL)
								OR (r.privilege_id IS NULL AND (r.resource_name = '%s'))
								OR ((r.resource_name = '%s')
								AND (r.privilege_id = p.privilege_id)))
						WHERE p.module_name = '%s' AND p.controller_name = '%s'",
						pg_escape_string($roleId),
						pg_escape_string($module . ':' . $controller),
						pg_escape_string($module . ':' . $controller),
						pg_escape_string($module),
						pg_escape_string($controller));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getByUser($resource, $userId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		
		$sql = sprintf("SELECT p.privilege_id, name, description, r.allow
						FROM " . $this->_prefix . "core_privilege AS p
						LEFT JOIN " . $this->_prefix . "core_rule AS r ON r.obj_type = 'user' 
							AND r.obj_id = %s 
							AND ((r.privilege_id IS NULL AND r.resource_name IS NULL)
								OR (r.privilege_id IS NULL AND (r.resource_name = '%s'))
								OR ((r.resource_name = '%s')
								AND (r.privilege_id = p.privilege_id)))
						WHERE p.module_name = '%s' AND p.controller_name = '%s'",
						pg_escape_string($userId),
						pg_escape_string($module . ':' . $controller),
						pg_escape_string($module . ':' . $controller),
						pg_escape_string($module),
						pg_escape_string($controller));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
