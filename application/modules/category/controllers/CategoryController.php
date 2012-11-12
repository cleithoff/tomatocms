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
 * @version 	$Id: CategoryController.php 4929 2010-08-25 03:36:50Z huuphuoc $
 * @since		2.0.0
 */

class Category_CategoryController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	/**
	 * Add new category
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		
		$request  = $this->getRequest();
		
		/**
		 * @since 2.0.8
		 */
		$sourceId       = $request->getParam('source_id');
		$sourceCategory = (null == $sourceId) ? null : $categoryDao->getById($sourceId);
		$this->view->assign('sourceCategory', $sourceCategory);
		$this->view->assign('lang', $request->getParam('lang'));
		
		if ($request->isPost()) {
			$user 	  = Zend_Auth::getInstance()->getIdentity();
			$name 	  = $request->getPost('name');
			$slug 	  = $request->getPost('slug');
			$meta 	  = $request->getPost('meta');
			$purifier = new HTMLPurifier();
			
			$category = new Category_Models_Category(array(
				'name'		   => $purifier->purify($name),
				'slug'		   => $slug,
				'meta'		   => $purifier->purify($meta),
				'created_date' => date('Y-m-d H:i:s'),
				'user_id'	   => $user->user_id,
				
				/**
				 * @since 2.0.7
				 */
				'parent_id'    => $request->getPost('parentId'),
			
				/**
				 * @since 2.0.8
				 */
				'language'	   => $request->getPost('languageSelector'),
			));
			$id = $categoryDao->add($category);
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->add(new Core_Models_Translation(array(
				'item_id' 	      => $id,
				'item_class'      => get_class($category),
				'source_item_id'  => ('' == $source['id']) ? $id : $source['id'],
				'language'        => $category->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('category_add_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'category_category_add'));
		}
	}
	
	/**
	 * Delete category
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
			$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
			$categoryDao->setDbConnection($conn);
			$category = $categoryDao->getById($id);
			
			if ($category != null) {
				$categoryDao->delete($category);
				
				/**
				 * @since 2.0.8
				 */
				$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
				$translationDao->setDbConnection($conn);
				$translationDao->delete($id, get_class($category));
				
				$response = 'RESULT_OK';
			}
		}
		$this->getResponse()->setBody($response);
	}
	
	/**
	 * Edit category
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request = $this->getRequest();
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		
		$categoryId = $request->getParam('category_id');
		$category 	= $categoryDao->getById($categoryId);
		
		if (null == $category) {
			throw new Exception('Not found category with id of ' . $categoryId);
		}
		
		/**
		 * @since 2.0.8
		 */
		$sourceCategory = $categoryDao->getSource($category);
		
		$this->view->assign('sourceCategory', $sourceCategory);
		$this->view->assign('category', $category);
		
		if ($request->isPost()) {
			$parentId = $request->getPost('parentId');
			$purifier = new HTMLPurifier();
			
			$category->language		 = $request->getPost('languageSelector', $category->language);
			$category->name 		 = $purifier->purify($request->getPost('name'));
			$category->slug 		 = $request->getPost('slug');
			$category->meta 		 = $purifier->purify($request->getPost('meta'));
			$category->modified_date = date('Y-m-d H:i:s');

			/**
			 * Get parent category
			 */
			$parent = ($category->parent_id) ? $categoryDao->getById($category->parent_id) : null;
			if ((null == $parent && 0 == $parentId) || ($parent != null && $parent->category_id == $parentId)) {
				/**
				 * User do NOT change the parent category value
				 */
				$categoryDao->update($category);
			} else {
				/**
				 * User changed parent category
				 */
				$category->parent_id = $parentId;
				$categoryDao->delete($category);
				$categoryId = $categoryDao->add($category);
			}
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->update(new Core_Models_Translation(array(
				'item_id' 	      => $categoryId,
				'item_class'      => get_class($category),
				'source_item_id'  => ('' == $source['id']) ? $categoryId : $source['id'],
				'language'        => $category->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('category_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('category_id' => $categoryId), 'category_category_edit'));
		}
	}
	
	/**
	 * List categories
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$lang = $this->getRequest()->getParam('lang', Tomato_Config::getConfig()->web->lang);
		$categoryDao->setLang($lang);
		
		$categories = $categoryDao->getTree();
		$this->view->assign('categories', $categories);
	} 	
	
	/**
	 * Update categories' order
	 * 
	 * @since 2.0.7
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
			$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
			$categoryDao->setDbConnection($conn);
			
			foreach ($data as $category) {
				$categoryDao->updateOrder(new Category_Models_Category(array(
					'category_id' => $category['id'],
					'parent_id'   => $category['parent_id'],
					'left_id'     => $category['left_id'],
					'right_id'    => $category['right_id'],
				)));
			}
			
			$response = 'RESULT_OK';
		}
		$this->getResponse()->setBody($response);
	}	
}
	