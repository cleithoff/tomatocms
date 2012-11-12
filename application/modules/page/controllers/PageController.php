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
 * @version 	$Id: PageController.php 4930 2010-08-25 03:38:40Z huuphuoc $
 * @since		2.0.7
 */

class Page_PageController extends Zend_Controller_Action
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * View page details
	 * 
	 * @return void
	 */
	public function detailsAction()
	{
		$request = $this->getRequest();
		$pageId  = $request->getParam('page_id');
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		
		$page = $pageDao->getById($pageId);
		if (null == $page) {
			throw new Exception('Not found page with id of ' . $pageId);
		}
		
		/**
		 * Add meta description tag
		 */
		$description = strip_tags($page->description);
		$this->view->headMeta()->setName('description', $description);
		
		$this->view->assign('page', $page);
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new page
	 * 
	 * @return void
	 */
	public function addAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		
		$request = $this->getRequest();
		
		/**
		 * @since 2.0.8
		 */
		$sourceId   = $request->getParam('source_id');
		$sourcePage = (null == $sourceId) ? null : $pageDao->getById($sourceId);
		$this->view->assign('sourcePage', $sourcePage);
		$this->view->assign('translatableData', (null == $sourcePage) ? array() : $sourcePage->getProperties());
		$this->view->assign('lang', $request->getParam('lang'));
		
		if ($request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$page = new Page_Models_Page(array(
				'name' 			=> $request->getPost('name'),
				'slug' 			=> $request->getPost('slug'),
				'description'	=> $request->getPost('description'),
				'content'		=> $request->getPost('content'),
				'parent_id' 	=> $request->getPost('parentId'),
				'num_views'     => 0,
				'created_date' 	=> date('Y-m-d H:i:s'),
				'modified_date' => null,
				'user_id' 		=> $user->user_id,
			
				/**
				 * @since 2.0.8
				 */
				'language'	   => $request->getPost('languageSelector'),
			));
			$pageId = $pageDao->add($page);
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->add(new Core_Models_Translation(array(
				'item_id' 	      => $pageId,
				'item_class'      => get_class($page),
				'source_item_id'  => ('' == $source['id']) ? $pageId : $source['id'],
				'language'        => $page->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('page_add_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'page_page_add'));
		}
	}
	
	/**
	 * Delete page
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$response = 'RESULT_ERROR';
		$request  = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
			$pageDao->setDbConnection($conn);
			$page = $pageDao->getById($id);
			
			if ($page != null) {
				$pageDao->delete($page);
				
				/**
				 * @since 2.0.8
				 */
				$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
				$translationDao->setDbConnection($conn);
				$translationDao->delete($id, get_class($page));
				
				$response = 'RESULT_OK';
			}
		}
		$this->getResponse()->setBody($response);
	}
	
	/**
	 * Edit page
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		
		$pageId = $request->getParam('page_id');
		$page   = $pageDao->getById($pageId);
		
		if (null == $page) {
			throw new Exception('Not found page with id of ' . $pageId);
		}
		
		/**
		 * @since 2.0.8
		 */
		$sourcePage = $pageDao->getSource($page);
		
		$this->view->assign('sourcePage', $sourcePage);
		$this->view->assign('page', $page);
		
		if ($request->isPost()) {
			$parentId = $request->getPost('parentId');
			$purifier = new HTMLPurifier();
			
			$page->name 		 = $purifier->purify($request->getPost('name'));
			$page->slug 		 = $request->getPost('slug');
			$page->description 	 = $purifier->purify($request->getPost('description'));
			$page->content 		 = $purifier->purify($request->getPost('content'));
			$page->modified_date = date('Y-m-d H:i:s');
			
			/**
			 * @since 2.0.8
			 */
			$page->language      = $request->getPost('languageSelector', $page->language);
			
			/**
			 * Get parent page
			 */
			$parent = ($page->parent_id) ? $pageDao->getById($page->parent_id) : null;
			if ((null == $parent && 0 == $parentId) || ($parent != null && $parent->page_id == $parentId)) {
				/**
				 * User do NOT change the parent page value
				 */
				$pageDao->update($page);
			} else {
				/**
				 * User changed parent page
				 */
				$page->parent_id = $parentId;
				$pageDao->delete($page);
				$pageId = $pageDao->add($page);
			}
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->update(new Core_Models_Translation(array(
				'item_id' 	      => $pageId,
				'item_class'      => get_class($page),
				'source_item_id'  => ('' == $source['id']) ? $pageId : $source['id'],
				'language'        => $page->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('page_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('page_id' => $pageId), 'page_page_edit'));
		}
	}
	
	/**
	 * List pages
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$lang = $this->getRequest()->getParam('lang', Tomato_Config::getConfig()->web->lang);
		$pageDao->setLang($lang);
		
		$pages = $pageDao->getTree();
		$this->view->assign('pages', $pages);
	}
	
	/**
	 * Update pages' order
	 * 
	 * @return void
	 */
	public function orderAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$response = 'RESULT_ERROR';
		$request  = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost('data');
			$data = Zend_Json::decode($data);
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
			$pageDao->setDbConnection($conn);
			
			foreach ($data as $page) {
				$pageDao->updateOrder(new Page_Models_Page(array(
					'page_id'   => $page['id'],
					'parent_id' => $page['parent_id'],
					'left_id'   => $page['left_id'],
					'right_id'  => $page['right_id'],
				)));
			}
			
			$response = 'RESULT_OK';
		}
		$this->getResponse()->setBody($response);
	}
}
