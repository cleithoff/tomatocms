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
 * @version 	$Id: Hook.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Hook extends Tomato_Model_Dao
	implements Core_Models_Interface_Hook
{
	public function convert($entity) 
	{
		return new Core_Models_Hook($entity); 
	}
	
	public function getHooks()
	{
		$rs = $this->_conn
				   	->select()
				   	->from(array('h' => $this->_prefix . 'core_hook'))
				   	->order('h.name ASC')					   
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getModules()
	{
		$rs = $this->_conn
				  	->select()
				  	->from(array('h' => $this->_prefix . 'core_hook'), array('module'))
				  	->distinct()
				  	->order('module')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function add($hook) 
	{
		$this->_conn->insert($this->_prefix . 'core_hook', 
							array(
								'module' 	  => $hook->module,
								'name' 		  => $hook->name,
								'description' => $hook->description,
								'thumbnail'   => $hook->thumbnail,
								'author' 	  => $hook->author,
								'email' 	  => $hook->email,
								'version' 	  => $hook->version,
								'license' 	  => $hook->license,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_hook');
	}
	
	public function exist($hook) 
	{ 
		$select = $this->_conn
						->select()
						->from(array('h' => $this->_prefix . 'core_hook'), array('num_hooks' => 'COUNT(*)'))
						->where('h.name = ?', $hook->name);
		if ($hook->module && $hook->module != '') {
			$select->where('h.module = ?', $hook->module);
		} else {
			$select->where('h.module IS NULL');
		}
		return ($select->query()->fetch()->num_hooks > 0);
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'core_hook', 
									array(
										'hook_id = ?' => $id,
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
		$this->_conn->delete($this->_prefix . 'core_target', 
							array(
								'hook_name = ?' => $hook->name,
							));
	}	
}
