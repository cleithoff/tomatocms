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
 * @version 	$Id: Hook.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Hook extends Tomato_Model_Dao
	implements Core_Models_Interface_Hook
{
	public function convert($entity) 
	{
		return new Core_Models_Hook($entity); 
	}
	
	public function getHooks()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_hook ORDER BY name ASC";
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getModules()
	{
		$sql  = "SELECT DISTINCT module FROM " . $this->_prefix . "core_hook ORDER BY module ASC";
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($hook) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_hook (module, name, description, thumbnail, author, email, version, license)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", 
						mysql_real_escape_string($hook->module),
						mysql_real_escape_string($hook->name),
						mysql_real_escape_string($hook->description),
						mysql_real_escape_string($hook->thumbnail),
						mysql_real_escape_string($hook->author),
						mysql_real_escape_string($hook->email),
						mysql_real_escape_string($hook->version),
						mysql_real_escape_string($hook->license)); 
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function exist($hook) 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_hooks FROM " . $this->_prefix . "core_hook
						WHERE name = '%s'", 
						mysql_real_escape_string($hook->name));
		if ($hook->module && $hook->module != '') {
			$sql .= sprintf(" AND module = '%s'", $hook->module);
		} else {
			$sql .= " AND module IS NULL";
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return ($row->num_hooks > 0) ? true : false;
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_hook WHERE hook_id = '%s'", mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function install($hook) 
	{
		$id = $this->add($hook);
				
		/**
		 * Perform the action when hook is activated
		 */
		$hookClass = (null == $hook->module || '' == $hook->module) 
					? 'Hooks_' . $hook->name . '_Hook'
					: $hook->module . '_Hooks_' . $hook->name . '_Hook';
		/**
		 * TODO: Make this as service
		 */
		if (class_exists($hookClass)) {
			$hookInstance = new $hookClass();
			if ($hookInstance instanceof Tomato_Hook) {
				$hookInstance->activate();
			}
		}
		
		return $id;
	}
	
	public function uninstall($hook) 
	{
		/**
		 * Delete hook
		 */
		$this->delete($hook->hook_id);
		
		/**
		 * Perform the action when hook is deactivated
		 * TODO: Make this as service
		 */
		$hookClass = (null == $hook->module || '' == $hook->module) 
					? 'Hooks_' . $hook->name . '_Hook'
					: $hook->module . '_Hooks_' . $hook->name . '_Hook';
		if (class_exists($hookClass)) {
			$hookInstance = new $hookClass();
			if ($hookInstance instanceof Tomato_Hook) {
				$hookInstance->deactivate();
			}
		}
		
		/**
		 * Remove hook from targets if any
		 */
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_target WHERE hook_name = '%s'", 
						mysql_real_escape_string($hook->name));
		$rs  = mysql_query($sql);
		mysql_free_result($rs);
	}	
}
