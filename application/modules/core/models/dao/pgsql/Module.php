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
 * @version 	$Id: Module.php 5423 2010-09-14 08:39:48Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Module extends Tomato_Model_Dao
	implements Core_Models_Interface_Module
{
	public function convert($entity)
	{
		return new Core_Models_Module($entity); 
	}
	
	public function getModules()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_module ORDER BY name ASC";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($module) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_module (name, description, thumbnail, author, email, version, license)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s') 
						RETURNING module_id",
						pg_escape_string($module->name),
						pg_escape_string($module->description),
						pg_escape_string($module->thumbnail),
						pg_escape_string($module->author),
						pg_escape_string($module->email),
						pg_escape_string($module->version),
						pg_escape_string($module->license));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		return $row->module_id;
	}
	
	public function delete($name) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_module', 
						array(
							'name' => $name,
						));
	}
	
	public function install($module)
	{
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'about.xml';
		if (!file_exists($file)) {
			return null;
		}
		
		$xml = simplexml_load_file($file);
		$moduleObj = new Core_Models_Module(array(
			'name' 		  => strtolower($xml->name),
			'description' => $xml->description,
			'thumbnail'   => $xml->thumbnail,
			'author' 	  => $xml->author,
			'email' 	  => $xml->email,
			'version' 	  => $xml->version,
			'license' 	  => $xml->license,
		));		
		
		/**
		 * Execute install scripts
		 */
		$xpath = $xml->xpath('install/db[contains(@adapter, "pgsql")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					pg_query($query);
				} catch (Exception $ex) {
					break;
				}
			}
		}
		
		return $moduleObj;		
	}
	
	public function uninstall($module)
	{
		$ret  = $this->delete($module);
		
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'about.xml';
		if (!file_exists($file)) {
			return 0;
		}
		$xml = simplexml_load_file($file);
		
		/**
		 * Execute uninstall scripts
		 */
		$xpath = $xml->xpath('uninstall/db[contains(@adapter, "pgsql")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					pg_query($query);
				} catch (Exception $ex) {
					break;
				}
			}
		}
		
		return $ret;
	}
}
