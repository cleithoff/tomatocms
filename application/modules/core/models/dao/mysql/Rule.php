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
 * @version 	$Id: Rule.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Rule extends Tomato_Model_Dao
	implements Core_Models_Interface_Rule
{
	public function convert($entity)
	{
		return new Core_Models_Rule($entity); 
	}
	
	public function getAclRules()
	{
		$sql  = "SELECT CONCAT(ru.obj_type, '_', ru.obj_id) AS role_name, ru.allow, ru.resource_name AS resource_name_2, NULL AS privilege_name
				FROM " . $this->_prefix . "core_rule AS ru
				WHERE ru.privilege_id IS NULL
				
				UNION 
				
				SELECT CONCAT(ru.obj_type, '_', ru.obj_id) AS role_name, ru.allow, CONCAT(p.module_name, ':', p.controller_name) AS resource_name_2, p.name AS privilege_name 
				FROM " . $this->_prefix . "core_rule AS ru
				INNER JOIN " . $this->_prefix . "core_privilege AS p 
					ON ru.privilege_id = p.privilege_id";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function removeRules($privilegeId)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_rule
						WHERE privilege_id = '%s'", 
						mysql_real_escape_string($privilegeId));
		mysql_query($sql);
		return mysql_affected_rows();		
	}
	
	public function add($rule)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_rule (obj_id, obj_type, privilege_id, allow, resource_name)
						VALUES ('%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($rule->obj_id),
						mysql_real_escape_string($rule->obj_type),
						mysql_real_escape_string($rule->privilege_id),
						mysql_real_escape_string($rule->allow),
						mysql_real_escape_string($rule->resource_name));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function removeFromUser($userId)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_rule
						WHERE obj_id = '%s' AND obj_type = 'user'", 
						mysql_real_escape_string($userId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function removeFromRole($roleId)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_rule
						WHERE obj_id = '%s' AND obj_type = 'role'",
						mysql_real_escape_string($roleId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function removeByResource($name)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_rule
						WHERE resource_name = '%s'",
						mysql_real_escape_string($name));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
