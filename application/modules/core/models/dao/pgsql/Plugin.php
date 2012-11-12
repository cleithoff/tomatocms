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
 * @version 	$Id: Plugin.php 5425 2010-09-14 08:40:29Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Plugin extends Tomato_Model_Dao
	implements Core_Models_Interface_Plugin
{
	public function convert($entity) 
	{
		return new Core_Models_Plugin($entity); 
	}
	
	public function getOrdered()
	{
		$sql = "SELECT * FROM " . $this->_prefix . "core_plugin ORDER BY ordering ASC";
		$rs  = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($plugin) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_plugin (name, description, thumbnail, author, email, version, license, ordering) 
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %s)
						RETURNING plugin_id",
						pg_escape_string($plugin->name),
						pg_escape_string($plugin->description),
						pg_escape_string($plugin->thumbnail),
						pg_escape_string($plugin->author),
						pg_escape_string($plugin->email),
						pg_escape_string($plugin->version),
						pg_escape_string($plugin->license),
						pg_escape_string($plugin->ordering));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->plugin_id;
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_plugin', 
						array(
							'plugin_id' => $id,
						));
	}
}
