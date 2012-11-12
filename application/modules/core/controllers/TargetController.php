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
 * @version 	$Id: TargetController.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_TargetController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Apply hook for target
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module = $request->getPost('mod', '');
			if ('_' == $module) {
				$module = '';
			}
			$hookName = $request->getPost('hook');
			$target   = $request->getPost('target');
			$target   = Zend_Json::decode($target);
			$target['hook_name']   = $hookName;
			$target['hook_module'] = $module;

			$target = new Core_Models_Target($target);
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$targetDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTargetDao();
			$targetDao->setDbConnection($conn);
			$id = $targetDao->add($target);
			
			if ($id > 0) {
				$this->getResponse()->setBody($id);
			} else {
				$this->getResponse()->setBody('RESULT_ERROR');
			}
		}
	}
	
	/**
	 * List targets
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$modules = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		$targets = array();
		foreach ($modules as $module) {
			$info = Tomato_Hook_Config::getTargetInfo($module);
			if ($info) {
				$targets[$module] = $info;
			}
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$hookDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getHookDao();
		$targetDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTargetDao();
		$hookDao->setDbConnection($conn);
		$targetDao->setDbConnection($conn);
		
		/**
		 * Get the list of hook modules
		 */
		$hookModules = array();
		$hooks 		 = array();
		$rs = $hookDao->getModules();
		if ($rs) {
			foreach ($rs as $row) {
				$module = (null == $row->module || '' == $row->module) ? '_' : $row->module;
				$hooks[$module] = array();
				$hookModules[] = $module;
			}
		}
		
		/**
		 * Get the list of hooks
		 */
		$rs = $hookDao->getHooks();
		if ($rs) {
			foreach ($rs as $row) {
				if (null == $row->module || '' == $row->module) {
					$row->module = '_';
				}
				$hooks[$row->module][$row->name] = $row;
			}
		}
					
		/** 
		 * ... and tagets
		 */
		$dbTargets = array();
		$rs = $targetDao->getTargets();
		if ($rs) {
			foreach ($rs as $row) {
				if (!isset($dbTargets[$row->target_name])) {
					$dbTargets[$row->target_name] = array();
				}
				$module = (null == $row->hook_module) ? '_' : $row->hook_module;
				$dbTargets[$row->target_name][$row->target_id . ''] = $module . ':' . $row->hook_name;
			}
		}
		
		$this->view->assign('targets', $targets);
		$this->view->assign('hookModules', $hookModules);
		$this->view->assign('hooks', $hooks);
		$this->view->assign('dbTargets', $dbTargets);
	}
	
	/**
	 * Remove hook from target
	 * 
	 * @return void
	 */
	public function removeAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$targetDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTargetDao();
			$targetDao->setDbConnection($conn);
			$targetDao->delete($id);
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
}
