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
 * @version 	$Id: Module.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Module extends Tomato_Model_Dao
	implements Core_Models_Interface_Module
{
	public function convert($entity)
	{
		return new Core_Models_Module($entity); 
	}
	
	public function getModules()
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_module ORDER BY name ASC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($module) 
	{
		$this->_conn->insert($this->_prefix . 'core_module', array(
			'name'		  => (string)$module->name,
			'description' => (string)$module->description,
			'thumbnail'	  => (string)$module->thumbnail,
			'author'	  => (string)$module->author,
			'email'		  => (string)$module->email,
			'version'	  => (string)$module->version,
			'license'	  => (string)$module->license,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_module');
	}
	
	public function delete($name) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_module WHERE name = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($name));
		$row = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $row;
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
		$xpath = $xml->xpath('install/db[contains(@adapter, "sqlsrv")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					$stmt  = $this->_conn->prepare($query);
					$stmt->execute();
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
		$xpath = $xml->xpath('uninstall/db[contains(@adapter, "sqlsrv")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					$stmt  = $this->_conn->prepare($query);
					$stmt->execute();
				} catch (Exception $ex) {
					break;
				}
			}
		}
		
		return $ret;
	}
}
