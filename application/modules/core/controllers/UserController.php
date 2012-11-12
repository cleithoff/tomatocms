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
 * @version 	$Id: UserController.php 4521 2010-08-12 09:37:29Z huuphuoc $
 * @since		2.0.0
 */

class Core_UserController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Update user's password
	 * 
	 * @return void
	 */
	public function changepassAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
		$userDao->setDbConnection($conn);
				
		$user = Zend_Auth::getInstance()->getIdentity();
		
		if ($request->isPost()) {
			$password = $request->getPost('password');
			$user = new Core_Models_User(array(
				'user_id'  => $user->user_id,
				'password' => $password,
			));
			$result = $userDao->updatePassword($user);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('user_changepass_update_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_user_changepass'));
			}
		}
	}
	
	/**
	 * Check user exist or not
	 * 
	 * @return void
	 */
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request   = $this->getRequest();
		$checkType = $request->getParam('check_type');
		$original  = $request->getParam('original');
		$value 	   = $request->getParam($checkType);
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
		$userDao->setDbConnection($conn);
		
		$result = false;
		if ($original == null || ($original != null && $value != $original)) {
			$result = $userDao->exist($checkType, $value);
		}			
		($result == true) ? $this->getResponse()->setBody('false') 
						  : $this->getResponse()->setBody('true');
	}
	
	/**
	 * Activate or deactive user
	 * 
	 * @return void
	 */
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id 	= $request->getPost('id');
			$status = $request->getPost('status');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
			$userDao->setDbConnection($conn);
			
			/**
			 * Do NOT allow user to activate/deactivate himself/herself
			 */
			if ($id != Zend_Auth::getInstance()->getIdentity()->user_id) {
				$userDao->toggleStatus($id);
				$this->getResponse()->setBody(1 - $status);
			}
		}
	}
	
	/**
	 * Add new user
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
		$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
		$roleDao->setDbConnection($conn);
		$userDao->setDbConnection($conn);
		
		$roles = $roleDao->getRoles();
		$this->view->assign('roles', $roles);
		
		if ($request->isPost()) {
			$fullname  = $request->getPost('full_name');
			$username  = $request->getPost('username');
			$password  = $request->getPost('password');
			$password2 = $request->getPost('confirmPassword');
			$email 	   = $request->getPost('email');
			$roleId    = $request->getPost('role');
			
			$user = new Core_Models_User(array(
				'user_name' 	 => $username,
				'password' 		 => $password,
				'full_name' 	 => $fullname,
				'email' 		 => $email,
				'is_active' 	 => 0,
				'created_date' 	 => date('Y-m-d H:i:s'),
				'logged_in_date' => null,
				'is_online' 	 => 0,
				'role_id' 		 => $roleId,
			));
			$id = $userDao->add($user);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('user_add_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_user_add'));
			}
		}
	}
	
	/**
	 * Edit user
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request = $this->getRequest();
		$userId  = $request->getParam('user_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
		$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
		$userDao->setDbConnection($conn);
		$roleDao->setDbConnection($conn);

		$roles = $roleDao->getRoles();
		$user  = $userDao->getById($userId);

		$this->view->assign('roles', $roles);
		$this->view->assign('user', $user);
		
		if ($request->isPost()) {
			$fullname = $request->getPost('full_name');
			$username = $request->getPost('username');
			$password = $request->getPost('confirmPassword');
			$email 	  = $request->getPost('email');
			$roleId   = $request->getPost('role');
			
			$user = new Core_Models_User(array(
				'user_id' 	=> $userId,
				'user_name' => $username,
				'password' 	=> $password,
				'full_name' => $fullname,
				'email' 	=> $email,
				'role_id' 	=> $roleId,
			));
			$result = $userDao->update($user);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('user_edit_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array('user_id' => $userId), 'core_user_edit'));
			}
		}
	}
	
	/**
	 * List users
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
		$roleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
		$userDao->setDbConnection($conn);
		$roleDao->setDbConnection($conn);
		
		/**
		 * Get roles
		 */
		$roles = $roleDao->getRoles();
		
		/**
		 * Get users 
		 */
		$perPage = 15;
		$query 	 = $request->getParam('query', '');
		$params  = array();
		if ($query == '') {
			$pageIndex = 1;
			$params['pageIndex'] = $pageIndex;
		} else {
			$params = Zend_Json::decode($query);
			$pageIndex = $params['pageIndex'];
			
			foreach (array('username', 'email', 'role', 'status') as $key) {
				if (isset($params[$key]) && $params[$key] == '') {
					$params[$key] = null;
				}
			}
		}
		$offset   = ($pageIndex > 0) ? ($pageIndex - 1) * $perPage : 0;
		$users 	  = $userDao->find($offset, $perPage, $params);
		$numUsers = $userDao->count($params);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($users, $numUsers));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('roles', $roles);
		$this->view->assign('users', $users);
		$this->view->assign('currentUser', Zend_Auth::getInstance()->getIdentity()->user_name);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => '',
			'itemLink' => 'javascript: filterUsers(%d, ' . urlencode(Zend_Json::encode($params)) . ');',
		));
		
		if ($query != '') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$this->_helper->getHelper('layout')->disableLayout();
			
			$content = $this->view->render('user/_filter.phtml');
			$this->getResponse()->setBody($content);
		}
	}	
}
