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
 * @version 	$Id: Widget.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Widget extends Tomato_Model_Dao
	implements Core_Models_Interface_Widget
{
	public function convert($entity)
	{
		return new Core_Models_Widget($entity); 
	}
	
	public function add($widget) 
	{
		$this->_conn->insert($this->_prefix . 'core_widget', array(
			'name'		  => $widget->name,
			'title'		  => $widget->title,
			'module' 	  => $widget->module,
			'description' => $widget->description,
			'thumbnail'	  => $widget->thumbnail,
			'author'	  => $widget->author,
			'email'       => $widget->email,
			'version'     => $widget->version,
			'license'	  => $widget->license,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_widget');
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_widget WHERE widget_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $row;
	}
	
	public function getWidgets($offset = null, $count = null, $module = null)
	{
		$sql   = 'SELECT * FROM ' . $this->_prefix . 'core_widget';
		$param = array();
		if ($module) {
			$sql 	.= ' WHERE module = ?';
			$param[] = $module;
		}
		$sql .= ' ORDER BY module ASC, name ASC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($param);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($module = null)
	{
		$sql   = 'SELECT COUNT(*) AS num_widgets FROM ' . $this->_prefix . 'core_widget';
		$param = array();
		if ($module) {
			$sql 	.= ' WHERE module = ?';
			$param[] = $module;
		}
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($param);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_widgets;
	}
}
