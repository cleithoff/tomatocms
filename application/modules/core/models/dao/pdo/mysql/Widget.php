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
 * @version 	$Id: Widget.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Widget extends Tomato_Model_Dao
	implements Core_Models_Interface_Widget
{
	public function convert($entity)
	{
		return new Core_Models_Widget($entity); 
	}
	
	public function add($widget) 
	{
		$this->_conn->insert($this->_prefix . 'core_widget', 
							array(
								'name' 		  => $widget->name,
								'title' 	  => $widget->title,
								'module' 	  => $widget->module,
								'description' => $widget->description,
								'thumbnail'   => $widget->thumbnail,
								'author' 	  => $widget->author,
								'email' 	  => $widget->email,
								'version' 	  => $widget->version,
								'license' 	  => $widget->license,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_widget');
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'core_widget',
									array(
										'widget_id = ?' => $id,
									));
	}
	
	public function getWidgets($offset = null, $count = null, $module = null)
	{
		$select = $this->_conn
						->select()
						->from(array('w' => $this->_prefix . 'core_widget'));
		if ($module) {
			$select->where('w.module = ?', $module);
		}
		$select->order('w.module ASC')
				->order('w.name ASC');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($module = null)
	{
		$select = $this->_conn
						->select()
						->from(array('w' => $this->_prefix . 'core_widget'), array('num_widgets' => 'COUNT(widget_id)'));
		if ($module) {
			$select->where('w.module = ?', $module);
		}
		return $select->limit(1)->query()->fetch()->num_widgets;	
	}
}
