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
 * @version 	$Id: Privilege.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Privilege extends Tomato_Model_Dao
	implements Core_Models_Interface_Privilege
{
	public function convert($entity)
	{
		return new Core_Models_Privilege($entity); 
	}
	
	public function getPrivileges()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_privilege";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getById($id) 
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_privilege WHERE privilege_id = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Core_Models_Privilege($row);
	}
	
	public function add($privilege) 
	{
		$this->_conn->insert($this->_prefix . 'core_privilege', array(
			'name' 			  => $privilege->name,
			'description' 	  => $privilege->description,
			'module_name' 	  => $privilege->module_name,
			'controller_name' => $privilege->controller_name,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_privilege');
	}
	
	public function delete($id) 
	{
		$sql  = "DELETE FROM " . $this->_prefix . "core_privilege WHERE privilege_id = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getByRole($resource, $roleId) 
	{
		$module     = $resource->module_name;
		$controller = $resource->controller_name;
		
		$sql = "SELECT p.privilege_id, name, description, r.allow
				FROM " . $this->_prefix . "core_privilege AS p
				LEFT JOIN " . $this->_prefix . "core_rule AS r 
					ON r.obj_type = 'role' 
					AND r.obj_id = ? 
					AND ((r.privilege_id IS NULL AND r.resource_name IS NULL)
						OR (r.privilege_id IS NULL AND (r.resource_name = ?))
						OR ((r.resource_name = ?)
						AND (r.privilege_id = p.privilege_id)))
				WHERE p.module_name = ? AND p.controller_name = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
						$roleId,
						$module . ':' . $controller,
						$module . ':' . $controller,
						$module,
						$controller
					));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);		
	}
	
	public function getByUser($resource, $userId) 
	{
		$module = $resource->module_name;
		$controller = $resource->controller_name;

		$sql = "SELECT p.privilege_id, name, description, r.allow
				FROM " . $this->_prefix . "core_privilege AS p
				LEFT JOIN " . $this->_prefix . "core_rule AS r 
					ON r.obj_type = 'user' 
					AND r.obj_id = ? 
					AND ((r.privilege_id IS NULL AND r.resource_name IS NULL)
						OR (r.privilege_id IS NULL AND (r.resource_name = ?))
						OR ((r.resource_name = ?)
						AND (r.privilege_id = p.privilege_id)))
				WHERE p.module_name = ? AND p.controller_name = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
						$userId,
						$module . ':' . $controller,
						$module . ':' . $controller,
						$module,
						$controller
					));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);	
	}
}
