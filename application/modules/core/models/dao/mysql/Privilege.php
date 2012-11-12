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
 * @version 	$Id: Privilege.php 5029 2010-08-28 17:02:10Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Privilege extends Tomato_Model_Dao
	implements Core_Models_Interface_Privilege
{
	public function convert($entity)
	{
		return new Core_Models_Privilege($entity); 
	}
	
	public function getPrivileges()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_privilege";
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
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_privilege WHERE privilege_id = '%s' LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Privilege(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($privilege) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_privilege (name, description, module_name, controller_name) 
						VALUES ('%s', '%s', '%s', '%s')",
						mysql_real_escape_string($privilege->name),
						mysql_real_escape_string($privilege->description),
						mysql_real_escape_string($privilege->module_name),
						mysql_real_escape_string($privilege->controller_name));
		mysql_query($sql);
		return mysql_insert_id();		
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_privilege WHERE privilege_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getByRole($resource, $roleId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		
		$sql = sprintf("SELECT p.privilege_id, name, description, r.allow
						FROM " . $this->_prefix . "core_privilege AS p
						LEFT JOIN " . $this->_prefix . "core_rule AS r 
							ON r.obj_type = 'role' 
							AND r.obj_id = '%s' 
							AND ((r.privilege_id IS NULL AND r.resource_name IS NULL)
								OR (r.privilege_id IS NULL AND (r.resource_name = '%s'))
								OR ((r.resource_name = '%s')
								AND (r.privilege_id = p.privilege_id)))
						WHERE p.module_name = '%s' AND p.controller_name = '%s'",
						mysql_real_escape_string($roleId),
						mysql_real_escape_string($module . ':' . $controller),
						mysql_real_escape_string($module . ':' . $controller),
						mysql_real_escape_string($module),
						mysql_real_escape_string($controller));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getByUser($resource, $userId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		
		$sql = sprintf("SELECT p.privilege_id, name, description, r.allow
						FROM " . $this->_prefix . "core_privilege AS p
						LEFT JOIN " . $this->_prefix . "core_rule AS r 
							ON r.obj_type = 'user' 
							AND r.obj_id = '%s' 
							AND ((r.privilege_id IS NULL AND r.resource_name IS NULL)
								OR (r.privilege_id IS NULL AND (r.resource_name = '%s'))
								OR ((r.resource_name = '%s')
								AND (r.privilege_id = p.privilege_id)))
						WHERE p.module_name = '%s' AND p.controller_name = '%s'",
						mysql_real_escape_string($userId),
						mysql_real_escape_string($module . ':' . $controller),
						mysql_real_escape_string($module . ':' . $controller),
						mysql_real_escape_string($module),
						mysql_real_escape_string($controller));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
