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
 * @version 	$Id: Rule.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Rule extends Tomato_Model_Dao
	implements Core_Models_Interface_Rule
{
	public function convert($entity)
	{
		return new Core_Models_Rule($entity); 
	}
	
	public function getAclRules()
	{
		$sql1 = $this->_conn
					->select()
					->from(array('ru' => $this->_prefix . 'core_rule'), 
							array('role_name' => 'CONCAT(ru.obj_type, "_", ru.obj_id)', 'ru.allow', 'resource_name_2' => 'ru.resource_name', 'privilege_name' => new Zend_Db_Expr('NULL')))
					->where('ru.privilege_id IS NULL')
					->__toString();
		
		$sql2 = $this->_conn
					->select()
					->from(array('ru' => $this->_prefix . 'core_rule'),
							array('role_name' => 'CONCAT(ru.obj_type, "_", ru.obj_id)', 'ru.allow', 'resource_name_2' => 'CONCAT(p.module_name, ":", p.controller_name)'))
					->joinInner(array('p' => $this->_prefix . 'core_privilege'), 'ru.privilege_id = p.privilege_id', array('privilege_name' => 'p.name'))
					->__toString();
		
		$rs   = $this->_conn
					->select()
					->union(array($sql1, $sql2))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function removeRules($privilegeId)
	{
		return $this->_conn->delete($this->_prefix . 'core_rule', 
									array(
										'privilege_id = ?' => $privilegeId,
									));
	}
	
	public function add($rule)
	{
		$this->_conn->insert($this->_prefix . 'core_rule', 
							array(
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
		return $this->_conn->delete($this->_prefix . 'core_rule', 
									array(
										'obj_id = ?'   => $userId,
										'obj_type = ?' => 'user',
									));
	}
	
	public function removeFromRole($roleId)
	{
		return $this->_conn->delete($this->_prefix . 'core_rule', 
									array(
										'obj_id = ?'   => $roleId,
										'obj_type = ?' => 'role',
									));
	}
	
	public function removeByResource($name)
	{
		return $this->_conn->delete($this->_prefix . 'core_rule', 
									array(
										'resource_name = ?' => $name,
									));
	}
}
