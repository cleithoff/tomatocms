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
 * @version 	$Id: Plugin.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Plugin extends Tomato_Model_Dao
	implements Core_Models_Interface_Plugin
{
	public function convert($entity) 
	{
		return new Core_Models_Plugin($entity); 
	}
	
	public function getOrdered()
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_plugin ORDER BY ordering ASC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function add($plugin) 
	{
		$this->_conn->insert($this->_prefix . 'core_plugin', array(
			'name' 		  => $plugin->name,
			'description' => $plugin->description,
			'thumbnail'   => $plugin->thumbnail,
			'author' 	  => $plugin->author,
			'email' 	  => $plugin->email,
			'version' 	  => $plugin->version,
			'license' 	  => $plugin->license,
			'ordering' 	  => $plugin->ordering,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_plugin');
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_plugin WHERE plugin_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
}
