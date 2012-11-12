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
 * @version 	$Id: Plugin.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Plugin extends Tomato_Model_Dao
	implements Core_Models_Interface_Plugin
{
	public function convert($entity) 
	{
		return new Core_Models_Plugin($entity); 
	}
	
	public function getOrdered()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_plugin ORDER BY ordering ASC";
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($plugin) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_plugin (name, description, thumbnail, author, email, version, license, ordering) 
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($plugin->name),
						mysql_real_escape_string($plugin->description),
						mysql_real_escape_string($plugin->thumbnail),
						mysql_real_escape_string($plugin->author),
						mysql_real_escape_string($plugin->email),
						mysql_real_escape_string($plugin->version),
						mysql_real_escape_string($plugin->license),
						mysql_real_escape_string($plugin->ordering));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_plugin WHERE plugin_id = '%s'", mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();	
	}
}
