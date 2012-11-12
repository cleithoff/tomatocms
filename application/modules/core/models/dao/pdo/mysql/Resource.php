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
 * @version 	$Id: Resource.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Resource extends Tomato_Model_Dao
	implements Core_Models_Interface_Resource
{
	public function convert($entity)
	{
		return new Core_Models_Resource($entity); 
	}
	
	public function getResources($module = null)
	{
		$select = $this->_conn
						->select()
						->from(array('r' => $this->_prefix . 'core_resource'));
		if ($module) {
			$select->where('r.module_name = ?', $module);	
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('r' => $this->_prefix . 'core_resource'))
					->where('r.resource_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Resource($row);
	}
	
	public function add($resource) 
	{
		$this->_conn->insert($this->_prefix . 'core_resource', 
							array(
								'description' 	  => $resource->description,
								'parent_id' 	  => $resource->parent_id,
								'module_name' 	  => $resource->module_name,
								'controller_name' => $resource->controller_name,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_resource');
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'core_resource', 
									array(
										'resource_id = ?' => $id,
									));
	}	
}
