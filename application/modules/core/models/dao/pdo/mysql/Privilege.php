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
 * @version 	$Id: Privilege.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Privilege extends Tomato_Model_Dao
	implements Core_Models_Interface_Privilege
{
	public function convert($entity)
	{
		return new Core_Models_Privilege($entity); 
	}
	
	public function getPrivileges()
	{
		$rs = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_privilege'))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_privilege'))
					->where('p.privilege_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Privilege($row);
	}
	
	public function add($privilege) 
	{
		$this->_conn->insert($this->_prefix . 'core_privilege', 
							array(
								'name' 			  => $privilege->name,
								'description' 	  => $privilege->description,
								'module_name' 	  => $privilege->module_name,
								'controller_name' => $privilege->controller_name,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_privilege');
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'core_privilege', 
									array(
										'privilege_id = ?' => $id,
									));
	}
	
	public function getByRole($resource, $roleId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		$rs = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_privilege'), array('privilege_id', 'name', 'description'))
					->joinLeft(array('r' => $this->_prefix . 'core_rule'), 
						'r.obj_type = ? 
						AND r.obj_id = ? 
						AND 
						(
							(r.privilege_id IS NULL AND r.resource_name IS NULL) 
							OR 
							(r.privilege_id IS NULL AND (r.resource_name = ?))
							OR 
							((r.resource_name = ?) AND (r.privilege_id = p.privilege_id))
						)',
						array('allow'))
					->where('p.module_name = ?', $module)
					->where('p.controller_name = ?', $controller)
					->bind(array(
						'role',
						$roleId,
						$module . ':' . $controller,
						$module . ':' . $controller,
					))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getByUser($resource, $userId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		$rs = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_privilege'), array('privilege_id', 'name', 'description'))
					->joinLeft(array('r' => $this->_prefix . 'core_rule'), 
						'r.obj_type = ?
						AND r.obj_id = ? 
						AND 
						(
							(r.privilege_id IS NULL AND r.resource_name IS NULL) 
							OR 
							(r.privilege_id IS NULL AND (r.resource_name = ?)) 
							OR 
							((r.resource_name = ?) AND (r.privilege_id = p.privilege_id))
						)', 
						array('allow'))
					->where('p.module_name = ?', $module)
					->where('p.controller_name = ?', $controller)
					->bind(array(
						'user',
						$userId,
						$module . ':' . $controller,
						$module . ':' . $controller,
					))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
}
