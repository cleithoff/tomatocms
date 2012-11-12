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
 * @version 	$Id: TagController.php 3978 2010-07-25 11:34:33Z huuphuoc $
 * @since		2.0.2
 */

class Tag_TagController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * View tag details
	 * 
	 * @return void
	 */
	public function detailsAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();

		$request   = $this->getRequest();
		$tagId 	   = $request->getParam('tag_id');
		$routeName = $request->getParam('details_route_name');
		$router    = Zend_Controller_Front::getInstance()->getRouter();
		
		if ($router->hasRoute($routeName)) {
			$route = $router->getRoute($routeName);
			if ($route instanceof Zend_Controller_Router_Route_Regex) {
				$defaults = $route->getDefaults();
				$this->_forward($defaults['action'], $defaults['controller'], $defaults['module'], array('tag_id' => $tagId, 'details_route_name' => $routeName));
			}
		}
	}
	
	/**
	 * Suggest tags based on user's input
	 * 
	 * @return void
	 */
	public function suggestAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		$q 		 = $request->getParam('q');
		$limit 	 = $request->getParam('limit', 10);
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$tagDao	= Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagDao();
		$tagDao->setDbConnection($conn);
		
		$tags = $tagDao->find($q, 0, $limit);
		$return = '';
		foreach ($tags as $tag) {
			$return .= $tag->tag_text . '|' . $tag->tag_id . "\n";
		}
		$this->getResponse()->setBody($return);  
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new tag
	 * 
	 * @return void
	 */
	public function addAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$text = $request->getPost('keyword');
			if ($text != null && $text != '') {
				$conn = Tomato_Db_Connection::factory()->getMasterConnection();
				$tagDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagDao();
				$tagDao->setDbConnection($conn);
				
				/**
				 * Check whether the tag exists or not
				 */
				if (!$tagDao->exist($text)) {
					$tagDao->add(new Tag_Models_Tag(array(
						'tag_text' => strip_tags($text),
					)));
					
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('tag_add_success'));
				}
			}
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'tag_tag_list'));
		}
	}
	
	/**
	 * Delete tag
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$tagId = $request->getParam('id');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$tagDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagDao();
			$tagDao->setDbConnection($conn);
			$tagDao->delete($tagId);
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * List tags
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('page_index', 1);
		$perPage = 100;
		$offset  = ($pageIndex - 1) * $perPage;
		$params  = null;
		$keyword = '';
		
		if ($request->isPost()) {
			$keyword = $request->getPost('keyword');
			if ($keyword) {
				$params = rawurlencode(base64_encode($keyword));
			}
		} else {
			$params = $request->getParam('q');
			if (null != $params) {
				$keyword = rawurldecode(base64_decode($params));
			} else {
				$params = rawurlencode(base64_encode($keyword));
			}
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$tagDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagDao();
		$tagDao->setDbConnection($conn);
		
		$tags 	 = $tagDao->find($keyword, $offset, $perPage);
		$numTags = $tagDao->count($keyword);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($tags, $numTags));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('keyword', $keyword);
		$this->view->assign('tags', $tags);
		$this->view->assign('numTags', $numTags);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'tag_tag_list'),
			'itemLink' => (null == $params) ? 'page-%d' : 'page-%d?q=' . $params,
		));		
	}	
}
