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
 * @copyright	Copyright (c) 2008-2009 TIG Corporation (http://www.tig.vn)
 * @license		GNU GPL license, see http://www.tomatocms.com/license.txt or license.txt
 * @version 	$Id: Widget.php 5272 2010-09-01 09:49:55Z hoangninh $
 * @since		2.0.1
 */

class Comment_Widgets_Comment_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$perPage 	  = $this->_request->getParam('limit', 10);
		$allowComment = $this->_request->getParam('allow_comment');
		$isPreviewing = $this->_request->getParam(Tomato_Widget::PARAM_PREVIEW_MODE);
		$showAvatar   = $this->_request->getParam('show_avatar');
		$pageUrl 	  = $this->_request->getPathInfo();
		$avatarSize   = $this->_request->getParam('avatar_size');
		$pageIndex 	  = $this->_request->getParam('pageIndex', 1);
		$message 	  = $this->_request->getParam('message');
		
		switch ($allowComment) {
			case 1:
				$allowComment = true;
				break;
			case 2:
				$user = Zend_Auth::getInstance()->getIdentity();
				$allowComment = (null == $user) ? false : true;
				break;
			case 0:
			default:
				$allowComment = false;
				break;	
		}
		
		$allowComment = ($allowComment == 1) ? true : false;
		$isPreviewing = $isPreviewing ? true : false;
		$showAvatar   = ($showAvatar == 1) ? true : false;
		$pageUrl 	  = rtrim($pageUrl, '/') . '/';
		$offset 	  = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
		$commentDao->setDbConnection($conn);
		
		$numComments = $commentDao->countThreadComments($pageUrl, true);
		$comments 	 = $commentDao->getThreadComments($offset, $perPage, $pageUrl, true);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($comments, $numComments));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->_view->assign('isPreviewing', $isPreviewing);
		$this->_view->assign('limit', $perPage);
		$this->_view->assign('allowComment', $allowComment);
		$this->_view->assign('showAvatar', $showAvatar);
		$this->_view->assign('pageUrl', $pageUrl);
		$this->_view->assign('avatarSize', $avatarSize);
		$this->_view->assign('message', $message);
		$this->_view->assign('pageIndex', $pageIndex);
		$this->_view->assign('comments', $comments);
		
		$this->_view->assign('paginator', $paginator);
		$this->_view->assign('paginatorOptions', array(
			'path' 	   => $_SERVER['REQUEST_URI'],
			'itemLink' => 'javascript: Comment.Widgets.Comment.loadComments(%d);',
		));	
		
		$ok = true;
		if ($this->_request->isPost('tCommentEmail') && $allowComment) {
			/**
			 * TODO: Remove this variable
			 */
			$url = $this->_request->getPost('tCommentUrl');
			
			$comment = new Comment_Models_Comment(array(
				'title' 	   => $this->_request->getPost('tCommentTitle'),
				'content' 	   => $this->_request->getPost('tCommentContent'),
				'full_name'    => $this->_request->getPost('tCommentFullName'),
				'email' 	   => $this->_request->getPost('tCommentEmail'),
				'web_site'	   => $this->_request->getPost('tCommentWebsite'),
				'ip' 		   => $this->_request->getClientIp(),
				'created_date' => date('Y-m-d H:i:s'),
				'is_active'    => 0,
				'reply_to' 	   => (int)$this->_request->getPost('tCommentReply'),
				'page_url'	   => $pageUrl,
			));
			
			$ok = false;
			$moduleConfig = Tomato_Module_Config::getConfig('comment');	
			if ($moduleConfig != null && $moduleConfig->akismet->api_key && $moduleConfig->akismet->api_key != '') {
				$akismetService = new Zend_Service_Akismet($moduleConfig->akismet->api_key, Tomato_Config::getConfig()->web->url->base);
     			$params = array(
     				'user_ip' 			   => $comment->ip,
     				'user_agent' 		   => $this->_request->getServer('HTTP_USER_AGENT'),
     				'comment_type' 		   => 'comment',
     				'comment_author' 	   => $comment->full_name,
     				'comment_author_email' => $comment->email,
     				'comment_content' 	   => $comment->content, 
     			);
				$ok = ($akismetService->verifyKey() && !$akismetService->isSpam($params));
			} else {
				$ok = true;
			}
			if ($ok) {
				/**
				 * Add new comment
				 */
				$commentId = $commentDao->add($comment);
				
				/**
				 * Update order for all comments in thread
				 */
				$comment->comment_id = $commentId;
				$commentDao->reupdateOrderInThread($comment);
				
				$flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
				$flashMessenger->addMessage($this->_view->translator()->widget('send_comment_success'));	
				
				/**
				 * Redirect to the original page
				 */
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
				$redirector->gotoUrl($this->_view->APP_URL . '/' . ltrim($pageUrl, '/') . '#commentForm');
			}
		}
		$this->_view->assign('ok', $ok);
	}
	
	protected function _prepareLoad() 
	{
		$pageUrl 	= $this->_request->getParam('page_url');
		$allowComment = $this->_request->getParam('allow_comment');
		$perPage 	= $this->_request->getParam('limit', 10);
		$showAvatar = $this->_request->getParam('show_avatar');
		$avatarSize = $this->_request->getParam('avatar_size');
		$pageIndex 	= $this->_request->getParam('pageIndex', 1);
		
		$showAvatar = ($showAvatar == 1) ? true : false;
		$offset 	= ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$commentDao = Tomato_Model_Dao_Factory::getInstance()->setModule('comment')->getCommentDao();
		$commentDao->setDbConnection($conn);
		
		$numComments = $commentDao->countThreadComments($pageUrl, true);
		$comments 	 = $commentDao->getThreadComments($offset, $perPage, $pageUrl, true);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($comments, $numComments));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->_view->assign('allowComment', $allowComment);
		$this->_view->assign('showAvatar', $showAvatar);
		$this->_view->assign('avatarSize', $avatarSize);
		$this->_view->assign('comments', $comments);
		
		$this->_view->assign('paginator', $paginator);
		$this->_view->assign('paginatorOptions', array(
			'path' 	   => $_SERVER['REQUEST_URI'],
			'itemLink' => 'javascript: Comment.Widgets.Comment.loadComments(%d);',
		));		
	}
}
