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
 * @version 	$Id: Rule.php 5430 2010-09-14 08:54:53Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Rule extends Tomato_Model_Dao
	implements Core_Models_Interface_Rule
{
	public function convert($entity)
	{
		return new Core_Models_Rule($entity); 
	}
	
	public function getAclRules()
	{
		$sql = "SELECT (ru.obj_type || '_' || ru.obj_id) AS role_name, ru.allow, ru.resource_name AS resource_name_2, NULL AS privilege_name
				FROM " . $this->_prefix . "core_rule AS ru
				WHERE ru.privilege_id IS NULL
				
				UNION 
				
				SELECT (ru.obj_type || '_' || ru.obj_id) AS role_name, ru.allow, (p.module_name || ':' || p.controller_name) AS resource_name_2, p.name AS privilege_name 
				FROM " . $this->_prefix . "core_rule AS ru
				INNER JOIN " . $this->_prefix . "core_privilege AS p ON ru.privilege_id = p.privilege_id";
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function removeRules($privilegeId)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_rule', 
									array(
										'privilege_id' => $privilegeId,
									));		
	}
	
	public function add($rule)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_rule (obj_id, obj_type, privilege_id, allow, resource_name)
						VALUES (%s, '%s', %s, '%s', '%s')
						RETURNING rule_id",
						pg_escape_string($rule->obj_id),
						pg_escape_string($rule->obj_type),
						pg_escape_string($rule->privilege_id),
						pg_escape_string($rule->allow),
						pg_escape_string($rule->resource_name));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->rule_id;
	}
	
	public function removeFromUser($userId)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_rule', 
						array(
							'obj_id'   => $userId,
							'obj_type' => 'user',
						));
	}
	
	public function removeFromRole($roleId)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_rule', 
						array(
							'obj_id'   => $roleId,
							'obj_type' => 'role',
						));
	}
	
	public function removeByResource($name)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_rule', 
									array(
										'resource_name' => $name,
									));
	}
}
