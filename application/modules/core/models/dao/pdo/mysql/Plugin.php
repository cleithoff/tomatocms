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
 * @version 	$Id: Plugin.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Plugin extends Tomato_Model_Dao
	implements Core_Models_Interface_Plugin
{
	public function convert($entity) 
	{
		return new Core_Models_Plugin($entity); 
	}
	
	public function getOrdered()
	{
		$rs = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_plugin'))
					->order('p.ordering ASC')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function add($plugin) 
	{
		$this->_conn->insert($this->_prefix . 'core_plugin', 
							array(
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
		return $this->_conn->delete($this->_prefix . 'core_plugin', 
									array(
										'plugin_id = ?' => $id,
									));
	}
}
