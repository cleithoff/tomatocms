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
 * @version 	$Id: Widget.php 5439 2010-09-15 06:37:06Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Widget extends Tomato_Model_Dao
	implements Core_Models_Interface_Widget
{
	public function convert($entity)
	{
		return new Core_Models_Widget($entity); 
	}

	public function add($widget)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_widget (name, title, module, description, thumbnail, author, email, version, license)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s') 
						RETURNING widget_id",
						pg_escape_string($widget->name),
						pg_escape_string($widget->title),
						pg_escape_string($widget->module),
						pg_escape_string($widget->description),
						pg_escape_string($widget->thumbnail),
						pg_escape_string($widget->author),
						pg_escape_string($widget->email),
						pg_escape_string($widget->version),
						pg_escape_string($widget->license));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		return $row->widget_id;
	}
	
	public function delete($id)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_widget',
						array(
							'widget_id' => $id,
						));
	}
	
	public function getWidgets($offset = null, $count = null, $module = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "core_widget";
		if ($module) {
			$sql .= sprintf(" WHERE module = '%s'", $module);
		}
		$sql .= " ORDER BY module ASC, name ASC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s OFFSET %s", $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($module = null)
	{
		$sql = "SELECT COUNT(*) AS num_widgets FROM " . $this->_prefix . "core_widget";
		if ($module) {
			$sql .= sprintf(" WHERE module = '%s'", $module);
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_widgets;
	}
}
