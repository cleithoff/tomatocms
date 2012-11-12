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
 * @version 	$Id: RoleController.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_RoleController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new role
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$description = $request->getPost('description');
			$lock 		 = $request->getPost('lock');
			$lock 		 = ($lock) ? 1 : 0;
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
			$roleDao->setDbConnection($conn);
			$roleDao->add(new Core_Models_Role(array(
				'name' 		  => $name,
				'description' => $description,
				'locked' 	  => $lock,
			)));
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_role_list'));
		}	
	}
	
	/**
	 * Delete role
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			
			/**
			 * Count the user in this role
			 */
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
			$roleDao->setDbConnection($conn);

			/**
			 * Delete role if there's no users belonging to this role
			 */
			$numUsers = $roleDao->countUsers($id);
			if ($numUsers == 0) {
				$roleDao->delete($id);
				$this->getResponse()->setBody('RESULT_OK');
			} else {
				$this->getResponse()->setBody('RESULT_ERROR');
			}
		}
	}
	
	/**
	 * List roles
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
		$roleDao->setDbConnection($conn);
		$roles = $roleDao->getRolesIncludeUser();
		
		$this->view->assign('roles', $roles);
	}
	
	/**
	 * Lock role
	 * 
	 * @return void
	 */
	public function lockAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id   = $request->getPost('id');
			$lock = $request->getPost('lock');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
			$roleDao->setDbConnection($conn);
			$roleDao->toggleLock($id);
			
			$this->getResponse()->setBody(1 - $lock);
		}
	}
}
