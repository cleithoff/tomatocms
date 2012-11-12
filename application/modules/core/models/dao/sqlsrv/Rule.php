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
 * @version 	$Id: Rule.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Rule extends Tomato_Model_Dao
	implements Core_Models_Interface_Rule
{
	public function convert($entity)
	{
		return new Core_Models_Rule($entity); 
	}
	
	public function getAclRules()
	{
		$sql  = "SELECT (ru.obj_type + '_' + CONVERT(VARCHAR(10), ru.obj_id)) AS role_name, ru.allow, ru.resource_name AS resource_name_2, NULL AS privilege_name
				FROM " . $this->_prefix . "core_rule AS ru
				WHERE ru.privilege_id IS NULL
				
				UNION 
				
				SELECT (ru.obj_type + '_' + CONVERT(VARCHAR(10), ru.obj_id)) AS role_name, ru.allow, (p.module_name + ':' + p.controller_name) AS resource_name_2, p.name AS privilege_name 
				FROM " . $this->_prefix . "core_rule AS ru
				INNER JOIN " . $this->_prefix . "core_privilege AS p 
					ON ru.privilege_id = p.privilege_id";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function removeRules($privilegeId)
	{
		$sql  = "DELETE FROM " . $this->_prefix . "core_rule WHERE privilege_id = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($privilegeId));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;		
	}
	
	public function add($rule)
	{
		$this->_conn->insert($this->_prefix . 'core_rule', array(
			'obj_id' 		=> $rule->obj_id,
			'obj_type' 		=> $rule->obj_type,
			'privilege_id' 	=> $rule->privilege_id,
			'allow' 		=> $rule->allow,
			'resource_name' => $rule->resource_name,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_rule');
	}
	
	public function removeFromUser($userId)
	{
		$sql  = "DELETE FROM " . $this->_prefix . "core_rule WHERE obj_id = ? AND obj_type = 'user'";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($userId));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function removeFromRole($roleId)
	{
		$sql  = "DELETE FROM " . $this->_prefix . "core_rule WHERE obj_id = ? AND obj_type = 'role'";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($roleId));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function removeByResource($name)
	{
		$sql  = "DELETE FROM " . $this->_prefix . "core_rule WHERE resource_name = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($name));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
}
