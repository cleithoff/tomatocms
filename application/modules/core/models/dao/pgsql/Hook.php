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
 * @version 	$Id: Hook.php 5422 2010-09-14 08:38:55Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Hook extends Tomato_Model_Dao
	implements Core_Models_Interface_Hook
{
	public function convert($entity) 
	{
		return new Core_Models_Hook($entity); 
	}
	
	public function getHooks()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_hook ORDER BY name ASC";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getModules()
	{
		$sql  = "SELECT DISTINCT module FROM " . $this->_prefix . "core_hook ORDER BY module ASC";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($hook) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_hook (module, name, description, thumbnail, author, email, version, license)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s') 
						RETURNING hook_id", 
						pg_escape_string($hook->module),
						pg_escape_string($hook->name),
						pg_escape_string($hook->description),
						pg_escape_string($hook->thumbnail),
						pg_escape_string($hook->author),
						pg_escape_string($hook->email),
						pg_escape_string($hook->version),
						pg_escape_string($hook->license)); 
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		return $row->hook_id;
	}
	
	public function exist($hook) 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_hooks FROM " . $this->_prefix . "core_hook
						WHERE name = '%s'", pg_escape_string($hook->name));
		if ($hook->module && $hook->module != '') {
			$sql .= sprintf(" AND module = '%s'", $hook->module);
		} else {
			$sql .= " AND module IS NULL";
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return ($row->num_hooks > 0) ? true : false;
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_hook', 
									array(
										'hook_id' => $id,
									));	
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
		pg_delete($this->_conn, $this->_prefix . 'core_target', 
					array(
						'hook_name' => $hook->name,
					));
	}	
}
