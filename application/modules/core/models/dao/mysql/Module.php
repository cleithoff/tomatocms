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
 * @version 	$Id: Module.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Module extends Tomato_Model_Dao
	implements Core_Models_Interface_Module
{
	public function convert($entity)
	{
		return new Core_Models_Module($entity); 
	}
	
	public function getModules()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_module ORDER BY name ASC";
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($module) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_module (name, description, thumbnail, author, email, version, license)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($module->name),
						mysql_real_escape_string($module->description),
						mysql_real_escape_string($module->thumbnail),
						mysql_real_escape_string($module->author),
						mysql_real_escape_string($module->email),
						mysql_real_escape_string($module->version),
						mysql_real_escape_string($module->license));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($name) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_module WHERE name = '%s'", 
						mysql_real_escape_string($name));
		mysql_query($sql);
		return mysql_affected_rows();
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
		$xpath = $xml->xpath('install/db[contains(@adapter, "mysql")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					mysql_query($query);
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
		$xpath = $xml->xpath('uninstall/db[contains(@adapter, "mysql")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					mysql_query($query);
				} catch (Exception $ex) {
					break;
				}
			}
		}
		
		return $ret;
	}
}
