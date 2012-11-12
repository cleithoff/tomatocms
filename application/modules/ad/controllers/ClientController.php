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
 * @version 	$Id: ClientController.php 4513 2010-08-12 09:31:43Z huuphuoc $
 * @since		2.0.0
 */

class Ad_ClientController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new client
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name 	   = $request->getPost('name');
			$email 	   = $request->getPost('email');
			$telephone = $request->getPost('telephone');
			$address   = $request->getPost('address');
			
			$client = new Ad_Models_Client(array(
				'name' 		   => $name,
				'email' 	   => $email,
				'telephone'    => $telephone,
				'address' 	   => $address,
				'created_date' => date('Y-m-d H:i:s'),
			));
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$clientDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getClientDao();
			$clientDao->setDbConnection($conn);
			
			$id = $clientDao->add($client);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('client_add_success')
				);
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'ad_client_add'));
			}
		}
	}
	
	/**
	 * Delete client
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$clientDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getClientDao();
			$clientDao->setDbConnection($conn);
			$clientDao->delete($id);
		}
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Edit client
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$clientDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getClientDao();
		$clientDao->setDbConnection($conn);
		
		$request  = $this->getRequest();
		$clientId = $request->getParam('client_id');
		$client   = $clientDao->getById($clientId);
		
		if (null == $client) {
			throw new Exception('Not found client with id of ' . $clientId);
		}
		
		$this->view->assign('client', $client);
		
		if ($request->isPost()) {
			$clientId  = $request->getPost('client_id');
			$name 	   = $request->getPost('name');
			$email 	   = $request->getPost('email');
			$telephone = $request->getPost('telephone');
			$address   = $request->getPost('address');
			
			$client = new Ad_Models_Client(array(
				'client_id' => $clientId,
				'name' 		=> $name,
				'email' 	=> $email,
				'telephone' => $telephone,
				'address' 	=> $address,
			));
			$result = $clientDao->update($client);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('client_edit_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array('client_id' => $clientId), 'ad_client_edit'));
			}
		}
	}
	
	/**
	 * List clients
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$clientDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getClientDao();
		$clientDao->setDbConnection($conn);

		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage   = 20;
		$offset	   = ($pageIndex - 1) * $perPage;
		
		$user 	= Zend_Auth::getInstance()->getIdentity();
		$params = null;
		$exp 	= array(
			'name'	  => null,
			'email'	  => null,
			'address' => null,
		);
		
		if ($request->isPost()) {
			$name 	 = $request->getPost('name');
			$email 	 = $request->getPost('email');
			$address = $request->getPost('address');
			if ($name) {
				$exp['name'] = strip_tags($name);
			}
			if ($email) {
				$exp['email'] = strip_tags($email);
			}
			if ($address) {
				$exp['address'] = strip_tags($address);
			}
			$params = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		} else {
			$params = $request->getParam('q');
			if (null != $params) {
				$exp = rawurldecode(base64_decode($params));
				$exp = Zend_Json::decode($exp); 
			} else {
				$params = rawurlencode(base64_encode(Zend_Json::encode($exp)));
			}
		}
		
		$clients 	= $clientDao->find($offset, $perPage, $exp);
		$numClients = $clientDao->count($exp);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($clients, $numClients));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('numClients', $numClients);
		$this->view->assign('clients', $clients);
		$this->view->assign('exp', $exp);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'ad_client_list'),
			'itemLink' => (null == $params) ? 'page-%d' : 'page-%d?q=' . $params,
		));
	}
}
