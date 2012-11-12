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
 * @version 	$Id: Resource.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Resource extends Tomato_Model_Dao
	implements Core_Models_Interface_Resource
{
	public function convert($entity)
	{
		return new Core_Models_Resource($entity); 
	}
	
	public function getResources($module = null)
	{
		$sql    = 'SELECT * FROM ' . $this->_prefix . 'core_resource';
		$params = array();
		if ($module) {
			$sql     .= ' WHERE module_name = ?';
			$params[] = $module;
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_resource WHERE resource_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Core_Models_Resource($row);
	}
	
	public function add($resource) 
	{
		$this->_conn->insert($this->_prefix . 'core_resource', array(
			'description' 	  => $resource->description,
			'parent_id' 	  => $resource->parent_id,
			'module_name' 	  => $resource->module_name,
			'controller_name' => $resource->controller_name,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_resource');
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_resource WHERE resource_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows; 
	}	
}
