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
 * @version 	$Id: PageController.php 5376 2010-09-10 07:40:05Z huuphuoc $
 * @since		2.0.0
 */

class Core_PageController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	/**
	 * Add new page
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		/**
		 * Get the list of routes which will be used at front-end section
		 */
		$frontendRoutes = array();
		
		$router = $this->getFrontController()->getRouter();
		$routes = $router->getRoutes();
		foreach ($routes as $name => $route) {
			/**
			 * Continue looping if the route is instance of Zend_Controller_Router_Route_Chain
			 * @since 2.0.9
			 */
			if ($route instanceof Zend_Controller_Router_Route_Chain) {
				continue;
			}
			
			$defaults = $route->getDefaults();
			if (isset($defaults['frontend']) && 'true' == $defaults['frontend']) {
				$frontendRoutes[$name] = array(
					'used' 	  	  => false,
					'description' => $this->view->translator($defaults['langKey'], $defaults['module']),
				);
			}
		}
		
		/**
		 * Get the list of page from database
		 */
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$pageDao->setDbConnection($conn);
		$pages = $pageDao->getOrdered();
		
		foreach ($pages as $page) {
			if (isset($frontendRoutes[$page->route])) {
				/**
				 * This route has been used to create page
				 */
				$frontendRoutes[$page->route]['used'] = true;
			}
		}
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$route 		 = $request->getPost('route');
			$title 		 = $request->getPost('title');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
			$pageDao->setDbConnection($conn);			
			$ordering = $pageDao->reupdateOrder();
			
			$page = new Core_Models_Page(array(
				'route' 	  => $route,
				'title' 	  => $title,
			));
			$id = $pageDao->add($page);
			if ($id > 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('page_add_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_page_add'));
			}
		}
		
		$this->view->assign('frontendRoutes', $frontendRoutes);
	}
	
	/**
	 * Check if the page name has been existed or not
	 * 
	 * @return void
	 */
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request   = $this->getRequest();
		$checkType = $request->getParam('check_type');
		$value 	   = $request->getParam($checkType);
		$original  = $request->getParam('original');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$pageDao->setDbConnection($conn);
		$result = false;
		if ($original == null || ($original != null && $value != $original)) {
			$result = $pageDao->exist($checkType, $value);
		}
		($result == true) ? $this->getResponse()->setBody('false') 
						  : $this->getResponse()->setBody('true');		
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
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
			$pageDao->setDbConnection($conn);
			$pageDao->delete($id);

			/**
			 * Update the page orders
			 */
			$ordering = $pageDao->reupdateOrder();
		}
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Edit page
	 * 
	 * @return void
	 */
	public function editAction() 
	{
	/**
		 * Get the list of routes which will be used at front-end section
		 */
		$frontendRoutes = array();
		
		$router = $this->getFrontController()->getRouter();
		$routes = $router->getRoutes();
		foreach ($routes as $name => $route) {
			/**
			 * Continue looping if the route is instance of Zend_Controller_Router_Route_Chain
			 * @since 2.0.9
			 */
			if ($route instanceof Zend_Controller_Router_Route_Chain) {
				continue;
			}
			
			$defaults = $route->getDefaults();
			if (isset($defaults['frontend']) && 'true' == $defaults['frontend']) {
				$frontendRoutes[$name] = array(
					'used' 	  	  => false,
					'description' => $this->view->translator($defaults['langKey'], $defaults['module']),
				);
			}
		}
		
		/**
		 * Get the list of page from database
		 */
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$pageDao->setDbConnection($conn);
		$pages = $pageDao->getOrdered();
		
		foreach ($pages as $page) {
			if (isset($frontendRoutes[$page->route])) {
				/**
				 * This route has been used to create page
				 */
				$frontendRoutes[$page->route]['used'] = true;
			}
		}
		
		$request   = $this->getRequest();
		$routeName = $request->getParam('route');
		$template  = $request->getParam('template');
		$page 	   = $pageDao->getByRoute($routeName);
		
		if ($request->isPost()) {
			$page->route = $request->getPost('route');
			$page->title = $request->getPost('title');
						
			$result = $pageDao->update($page);
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('page_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('template' => $template, 'route' => $page->route), 'core_page_edit')); 
		}
		
		$this->view->assign('frontendRoutes', $frontendRoutes);
		$this->view->assign('page', $page);
		$this->view->assign('template', $template);
	}
	
	/**
	 * Edit layout of page
	 * 
	 * @return void
	 */
	public function layoutAction() 
	{
		$request  = $this->getRequest();
		$template = $request->getParam('template');
		$pageName = $request->getParam('route');
		
		$moduleDirs = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		
		/**
		 * Get modules
		 * TODO: Only show module that has at least one widgets
		 */
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$moduleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getModuleDao();
		$pageDao   = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$moduleDao->setDbConnection($conn);
		$pageDao->setDbConnection($conn);

		$modules = $moduleDao->getModules();
		$page 	 = $pageDao->getByRoute($pageName);
		
		/**
		 * Load layout data from JSON file
		 */
		$jsonData = null;
		$jsonFile = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'layouts' . DS . $pageName . '.json';
		$xmlFile  = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'layouts' . DS . $pageName . '.xml';
		if (file_exists($jsonFile)) {
			$jsonData = file_get_contents($jsonFile);
		} else if (file_exists($xmlFile)) {
			/**
			 * Try to build JSON file if it does not exist
			 */
			$array = Tomato_Layout::load($xmlFile);
			$array = Zend_Json::encode($array);
			file_put_contents($jsonFile, $array);
			$jsonData = file_get_contents($jsonFile);
		}
		
		$this->view->assign('template', $template);
		$this->view->assign('modules', $moduleDirs);
		$this->view->assign('widgetModules', $modules);
		$this->view->assign('page', $page);
		
		$this->view->assign('jsonData', $jsonData);
	}
	
	/**
	 * List pages
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$request  = $this->getRequest();
		$template = $request->getParam('template', 'default');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$pageDao->setDbConnection($conn);		
		$pages = $pageDao->getOrdered();
		
		$this->view->assign('template', $template);
		$this->view->assign('pages', $pages);
	}
	
	/**
	 * Update the order of pages
	 * 
	 * @return void
	 */
	public function orderingAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$ids = $request->getPost('pageId');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
			$pageDao->setDbConnection($conn);
			
			/**
			 * Reset the order
			 */
			$pageDao->updateOrder(null, 0);
			
			/**
			 * Update new order
			 */
			if ($ids != null) {
				for ($i = 0; $i < count($ids); $i++) {
					$pageDao->updateOrder($ids[$i], $i);
				}
			}
		}
		
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Save page layout
	 * 
	 * @return void
	 */	
	public function savelayoutAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$template 	= $request->getPost('template');
			$page 		= $request->getPost('page');
			$jsonLayout = $request->getPost('layout');
			$layout 	= Zend_Json::decode($jsonLayout);
			
			/**
			 * Save data in JSON format for reading process more easy later
			 */
			$jsonFile = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'layouts' . DS . $page . '.json';
			$file 	  = fopen($jsonFile, 'w');
			fwrite($file, $jsonLayout);
			fclose($file);
			
			$xmlFile = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'layouts' . DS . $page . '.xml';
			Tomato_Layout::save($xmlFile, $layout);
			
			$this->getResponse()->setBody('RESULT_OK');	
		}
	}
	
	/**
	 * Load widgets
	 * 
	 * @return void
	 */
	public function widgetsAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request   = $this->getRequest();
		$module    = $request->getParam('mod');
		$pageIndex = $request->getParam('page', 1);
		$perPage   = 10;
		$offset	   = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$widgetDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getWidgetDao();
		$widgetDao->setDbConnection($conn);
		
		/**
		 * Get the number of widgets in the module
		 */
		$numWidgets = $widgetDao->count($module);
					
		/**
		 * List widgets
		 */
		$widgets = $widgetDao->getWidgets($offset, $perPage, $module);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($widgets, $numWidgets));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('widgets', $widgets);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => '',
			'itemLink' => "javascript: loadWidgets(%d, '$module');",
		));
	}
}
