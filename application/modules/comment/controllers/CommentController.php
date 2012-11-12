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
 * @version 	$Id: CommentController.php 4623 2010-08-15 04:40:01Z huuphuoc $
 * @since		2.0.1
 */

class Comment_CommentController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Activate comment
	 * 
	 * @return void
	 */
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
			$commentDao->setDbConnection($conn);
			
			$comment = $commentDao->getById($id);
			if (null == $comment) {
				$this->getResponse()->setBody('RESULT_NOT_FOUND');
				return;
			}
			$comment->activate_date = date('Y-m-d H:i:s');
			$commentDao->toggleActive($comment);
			$isActive = 1 - $comment->is_active;
			$this->getResponse()->setBody(Zend_Json::encode($isActive));			
		}
	}
	
	/**
	 * Add new comment
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request   = $this->getRequest();
		$paramsStr = $request->getParam('paramsString');
		$params    = base64_decode(rawurldecode($paramsStr));
		$params    = Zend_Json::decode($params);
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
		$commentDao->setDbConnection($conn);
		
		if ($request->isPost() && isset($params['page_url'])) {
			/**
			 * Add new comment
			 */
			$comment = new Comment_Models_Comment(array(
				'title' 	    => $request->getPost('tCommentTitle'),
				'content' 	    => $request->getPost('tCommentContent'),
				'full_name'     => $request->getPost('tCommentFullName'),
				'web_site'	    => $request->getPost('tCommentWebsite'),
				'email' 	    => $request->getPost('tCommentEmail'),
				'ip' 		    => $request->getClientIp(),
				'created_date'  => date('Y-m-d H:i:s'),
				'is_active'     => 1,
				'reply_to' 	    => (int)$request->getPost('tCommentReply'),
				'page_url'	    => $params['page_url'],
				'activate_date' => date('Y-m-d H:i:s'),
			));
			$commentId = $commentDao->add($comment);
			
			/**
   			 * Update order for all comments in thread
			 */
			$comment->comment_id = $commentId;
			$commentDao->reupdateOrderInThread($comment);
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('comment_add_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('paramsString' => $paramsStr), 'comment_thread'));	
		}
	}
	
	/**
	 * Delete comment
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		$string = 'RESULT_ERROR';
		
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
			$commentDao->setDbConnection($conn);
			
			$comment = $commentDao->getById($id);
			if (null == $comment) {
				$this->getResponse()->setBody('RESULT_NOT_FOUND');
				return;
			} 
			$commentDao->delete($id);
			$string = 'RESULT_OK';
		}
		$this->getResponse()->setBody($string);
	}
	
	/**
	 * Edit comment
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$request = $this->getRequest();
		$id 	 = $request->getParam('comment_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
		$commentDao->setDbConnection($conn);
		
		$comment = $commentDao->getById($id);
		if (null == $comment) {
			return;
		}
		
		$params = array('page_url' => $comment->page_url);
		$params = rawurlencode(base64_encode(Zend_Json::encode($params)));
		if (null === $params) {
			$params = '';
		}
		
		$this->view->assign('comment', $comment);
		$this->view->assign('queryString', $params);
		
		if ($request->isPost()) {
			$title 	  = $request->getPost('title');
			$content  = $request->getPost('content');
			$fullName = $request->getPost('fullName');
			$email 	  = $request->getPost('email');
			$webSite  = $request->getPost('website');
			$isActive = $request->getPost('status');
			
			$comment->title 	= $title;
			$comment->content 	= $content;
			$comment->full_name = $fullName;
			$comment->email 	= $email;
			$comment->web_site  = $webSite;
			$comment->is_active = $isActive;
			if ($comment->is_active != 1 && $isActive == 1) {
				$comment->activate_date = date('Y-m-d H:i:s');
			}
			$commentDao->update($comment);
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('comment_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('comment_id' => $id), 'comment_edit'));
		}
	}
	
	/**
	 * List comments
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
		$commentDao->setDbConnection($conn);
		
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage   = 20;
		$offset    = ($pageIndex - 1) * $perPage;

		$comments = $commentDao->getLatestByThread();
		$total 	  = $commentDao->countThreads();
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($comments, $total));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('comments', $comments);
		$this->view->assign('numComments', $total);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'comment_list'),
			'itemLink' => 'page-%d',
		));
	}

	/**
	 * List comments in thread
	 * 
	 * @return void
	 */
	public function threadAction()
	{
		$user 	   = Zend_Auth::getInstance()->getIdentity();
		$request   = $this->getRequest();
		$paramsStr = $request->getParam('paramsString');
		$pageIndex = $request->getParam('pageIndex', 1);
		
		$params  = base64_decode(rawurldecode($paramsStr));
		$params  = Zend_Json::decode($params);
		$pageUrl = (isset($params['page_url'])) ? $params['page_url'] : null;
		$perPage = 10;
		$offset  = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
		$commentDao->setDbConnection($conn);
		
		$comments = $commentDao->getThreadComments($offset, $perPage, $pageUrl);
		$total 	  = $commentDao->countThreadComments($pageUrl);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($comments, $total));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('user', $user);
		$this->view->assign('paramsString', $paramsStr);
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('pageUrl', $pageUrl);
		
		$this->view->assign('comments', $comments);
		$this->view->assign('numComments', $total);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array('paramsString' => ''), 'comment_thread'),
			'itemLink' => 'page-%d/' . $params,
		));
	}
}
