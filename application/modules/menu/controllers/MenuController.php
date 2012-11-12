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
 * @version 	$Id: MenuController.php 4930 2010-08-25 03:38:40Z huuphuoc $
 * @since		2.0.2
 */

class Menu_MenuController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Build new menu
	 * 
	 * @return void
	 */	
	public function buildAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$menuDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getMenuDao();
		$menuDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$sourceId   = $request->getParam('source_id');
		$sourceMenu = (null == $sourceId) ? null : $menuDao->getById($sourceId);
		$this->view->assign('translatableData', (null == $sourceMenu) ? array() : $sourceMenu->getProperties());
		$this->view->assign('sourceMenu', $sourceMenu);
		$this->view->assign('lang', $request->getParam('lang'));
		
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$description = $request->getPost('description');
			$items       = $request->getPost('items');
			
			$user = Zend_Auth::getInstance()->getIdentity();
			$menu = new Menu_Models_Menu(array(
				'name'		   => $name,
				'description'  => $description,
				'user_id'	   => $user->user_id,
				'user_name'	   => $user->user_name,
				'created_date' => date('Y-m-d H:i:s'),

				/**
				 * @since 2.0.8
				 */
				'language'     => $request->getPost('languageSelector'),
			));
			$menuId = $menuDao->add($menu);
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->add(new Core_Models_Translation(array(
				'item_id' 	      => $menuId,
				'item_class'      => get_class($menu),
				'source_item_id'  => ('' == $source['id']) ? $menuId : $source['id'],
				'language'        => $menu->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			/**
			 * @since 2.0.7
			 */
			if ($items != '') {
				$itemDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getItemDao();
				$itemDao->setDbConnection($conn);
				
				$items = Zend_Json::decode($items);
				foreach ($items as $item) {
					$itemDao->add(new Menu_Models_Item(array(
						'item_id' 	=> $item['item_id'],
						'label' 	=> $item['label'],
						'link' 		=> $item['link'],
						'menu_id'	=> $menuId,
						'left_id'	=> $item['left_id'],
						'right_id'  => $item['right_id'],
						'parent_id' => $item['parent_id'],
					)));
				}
			}
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('menu_build_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'menu_menu_build'));
		}
	}
	
	/**
	 * Delete menu
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		$result = 'RESULT_ERROR';
		
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$menuDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getMenuDao();
			$menuDao->setDbConnection($conn);

			$menu = $menuDao->getById($id);
			if (null == $menu) {
				$this->getResponse()->setBody('RESULT_NOT_FOUND');
				return;
			} 
			$menuDao->delete($id);
			
			/**
			 * @since 2.0.8
			 */
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->delete($id, get_class($menu));
			
			$result = 'RESULT_OK';
		}
		$this->getResponse()->setBody($result);
	}
	
	/**
	 * Edit menu
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request = $this->getRequest();
		$menuId  = $request->getParam('menu_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$menuDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getMenuDao();
		$menuDao->setDbConnection($conn);
		$menu = $menuDao->getById($menuId);
		
		if (null == $menu) {
			throw new Exception('The menu with Id of ' . $menuId . ' does not exist');
		}
		
		$itemDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getItemDao();
		$itemDao->setDbConnection($conn);
		$items = $itemDao->getTree($menuId);
		
		/**
		 * @since 2.0.8
		 */
		$sourceMenu = $menuDao->getSource($menu);
		$this->view->assign('sourceMenu', $sourceMenu);
		
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$description = $request->getPost('description');
			$newItems    = $request->getPost('items');

			$menu->name 	   = $name;
			$menu->description = $description;
			
			/**
			 * @since 2.0.8
			 */
			$menu->language    = $request->getPost('languageSelector', $menu->language);
			
			$menuDao->update($menu);
			
			/**
			 * @since 2.0.7
			 */
			if ($newItems != '') {
				$itemDao->delete($menuId);
				
				$newItems = Zend_Json::decode($newItems);
				foreach ($newItems as $item) {
					$itemDao->add(new Menu_Models_Item(array(
						'item_id' 	=> $item['item_id'],
						'label' 	=> $item['label'],
						'link' 		=> $item['link'],
						'menu_id'	=> $menuId,
						'left_id'	=> $item['left_id'],
						'right_id'  => $item['right_id'],
						'parent_id' => $item['parent_id'],
					)));
				}
			}
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->update(new Core_Models_Translation(array(
				'item_id' 	      => $menuId,
				'item_class'      => get_class($menu),
				'source_item_id'  => ('' == $source['id']) ? $menuId : $source['id'],
				'language'        => $menu->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('menu_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('menu_id' => $menu->menu_id), 'menu_menu_edit'));
		}
		
		$this->view->assign('menu', $menu);
		$this->view->assign('items', $items);
	}
	
	/**
	 * List menus
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$request = $this->getRequest();
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$menuDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getMenuDao();
		$menuDao->setDbConnection($conn);	
		
		$perPage   = 20;
		$pageIndex = $request->getParam('pageIndex', 1);
		$offset    = ($pageIndex - 1) * $perPage;
		
		/**
		 * @since 2.0.8
		 */
		$lang = $request->getParam('lang');
		$menuDao->setLang($lang);
		
		$menus 	  = $menuDao->getMenus($offset, $perPage);
		$numMenus = $menuDao->count();
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($menus, $numMenus));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('menus', $menus);
		$this->view->assign('numMenus', $numMenus);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'menu_menu_list'),
			'itemLink' => 'page-%d',
		));		
	}
}
