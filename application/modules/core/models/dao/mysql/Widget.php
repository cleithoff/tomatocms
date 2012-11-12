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
 * @version 	$Id: Widget.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Widget extends Tomato_Model_Dao
	implements Core_Models_Interface_Widget
{
	public function convert($entity)
	{
		return new Core_Models_Widget($entity); 
	}
	
	public function add($widget) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_widget (name, title, module, description, thumbnail, author, email, version, license)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($widget->name),
						mysql_real_escape_string($widget->title),
						mysql_real_escape_string($widget->module),
						mysql_real_escape_string($widget->description),
						mysql_real_escape_string($widget->thumbnail),
						mysql_real_escape_string($widget->author),
						mysql_real_escape_string($widget->email),
						mysql_real_escape_string($widget->version),
						mysql_real_escape_string($widget->license));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_widget WHERE widget_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getWidgets($offset = null, $count = null, $module = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "core_widget";
		if ($module) {
			$sql .= sprintf(" WHERE module = '%s'", $module);
		}
		$sql .= " ORDER BY module ASC, name ASC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($module = null)
	{
		$sql = "SELECT COUNT(*) AS num_widgets FROM " . $this->_prefix . "core_widget";
		if ($module) {
			$sql .= sprintf(" WHERE module = '%s'", $module);
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_widgets;
	}
}
