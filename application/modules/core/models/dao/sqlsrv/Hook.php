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
 * @version 	$Id: Hook.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Hook extends Tomato_Model_Dao
	implements Core_Models_Interface_Hook
{
	public function convert($entity)
	{
		return new Core_Models_Hook($entity); 
	}
	
	public function getHooks()
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_hook ORDER BY name ASC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getModules()
	{
		$sql  = 'SELECT DISTINCT module FROM ' . $this->_prefix . 'core_hook ORDER BY module ASC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function add($hook) 
	{
		$this->_conn->insert($this->_prefix . 'core_hook', array(
			'module' 	  => (string)$hook->module,
			'name' 		  => (string)$hook->name,
			'description' => (string)$hook->description,
			'thumbnail'   => (string)$hook->thumbnail,
			'author' 	  => (string)$hook->author,
			'email' 	  => (string)$hook->email,
			'version' 	  => (string)$hook->version,
			'license' 	  => (string)$hook->license,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_hook');
	}
	
	public function exist($hook) 
	{
		$sql    = 'SELECT COUNT(*) AS num_hooks FROM ' . $this->_prefix . 'core_hook 
					WHERE name = ?';
		$params = array($hook->name);
		if ($hook->module && $hook->module != '') {
			$sql     .= ' AND module = ?';
			$params[] = $hook->module;
		} else {
			$sql .= ' AND module IS NULL';
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$numHooks = $stmt->fetch()->num_hooks;
		return ($numHooks > 0);
	}
	
	public function delete($id)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_hook WHERE hook_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
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
		$sql = 'DELETE FROM ' . $this->_prefix . 'core_target WHERE hook_name = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($hook->name));
		$stmt->closeCursor();
	}	
}
