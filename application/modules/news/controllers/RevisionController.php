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
 * @version 	$Id: RevisionController.php 4642 2010-08-15 16:09:28Z huuphuoc $
 * @since		2.0.4
 */

class News_RevisionController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new revision
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$articleId 	  = $request->getPost('articleId');
			$categoryId   = $request->getPost('category');
			$subTitle 	  = $request->getPost('subTitle');
			$title 		  = $request->getPost('title');
			$slug 		  = $request->getPost('slug');
			$description  = $request->getPost('description');
			$content 	  = $request->getPost('content');
			$author 	  = $request->getPost('author');
			$icons 		  = $request->getPost('icons'); 
			$articleIcons = "";
			if (count($icons) == 1 ) {
				$articleIcons = '{"' . $icons[0] . '"}';
			}
			if (count($icons) == 2 ) {
				$articleIcons = '{"' . $icons[0] . '","' . $icons[1] . '"}';
			}
			
			$revision = new News_Models_Revision(array(
				'article_id' 		=> $articleId,
				'category_id' 		=> $categoryId,
				'title' 			=> $title,	
				'sub_title' 		=> $subTitle,
				'slug'				=> $slug,
				'description' 		=> $description,
				'content' 			=> $content,
				'created_date' 		=> date('Y-m-d H:i:s'),
				'created_user_id' 	=> $user->user_id,
				'created_user_name' => $user->user_name,
				'author' 			=> $author,
				'icons' 			=> $articleIcons,
			));
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
			$revisionDao->setDbConnection($conn);
			$revisionId = $revisionDao->add($revision);
			
			$properties = array(
				'article_id'  => $articleId,
				'category_id' => $categoryId,
			);
			$url = $this->view->serverUrl() . $this->view->url($properties, 'news_article_details')
					. '?preview=true&revision=' . $revisionId;	
			$this->_redirect($url);
		}
	}
	
	/**
	 * Delete revistion
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request  = $this->getRequest();
		$response = 'RESULT_ERROR';
		if ($request->isPost()) {
			$id = $request->getPost('revision_id');
						
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
			$revisionDao->setDbConnection($conn);
			$revisionDao->delete($id);
						
			$response = 'RESULT_OK';
		}
		
		$this->getResponse()->setBody($response);
	}
	
	/**
	 * List of article's revision
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$revisionDao->setDbConnection($conn);
		$articleDao->setDbConnection($conn);
		
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$articleId = $request->getParam('article_id');
		
		$perPage = 20;
		$offset	 = ($pageIndex - 1) * $perPage;
		$article = $articleDao->getById($articleId);
		if (null == $article) {
			throw new Tomato_Exception_NotFound();
		}
		$exp = array(
			'article_id' => $articleId,
		);
		$revisions 	  = $revisionDao->find($offset, $perPage, $exp);
		$numRevisions = $revisionDao->count($exp);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($revisions, $numRevisions));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('article', $article);
		$this->view->assign('exp', $exp);
		
		$this->view->assign('revisions', $revisions);
		$this->view->assign('numRevisions', $numRevisions);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array('article_id' => $articleId), 'news_revision_list'),
			'itemLink' => 'page-%d',
		));
	}
	
	/**
	 * Restore a revision
	 * 
	 * @return void
	 */
	public function restoreAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();

		$response = 'RESULT_ERROR';
		$request  = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('revision_id');
						
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
			$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
			$revisionDao->setDbConnection($conn);
			$articleDao->setDbConnection($conn);
			
			$revision = $revisionDao->getById($id);
			if (null == $revision) {
				$this->getResponse()->setBody($response);
				return;
			}
			
			/**
			 * Update article
			 */
			$user = Zend_Auth::getInstance()->getIdentity();
			$articleDao->update(new News_Models_Article(array(
				'article_id' 		=> $revision->article_id,
				'updated_user_id' 	=> $user->user_id,
				'updated_user_name' => $user->user_name,
				'updated_date' 		=> date('Y-m-d H:i:s'),
				'title' 			=> $revision->title,
				'sub_title' 		=> $revision->sub_title,
				'slug' 				=> $revision->slug,
				'description' 		=> $revision->description,
				'content' 			=> $revision->content,
				'author' 			=> $revision->author,
				'icons' 			=> $revision->icons,
				'category_id' 		=> $revision->category_id,
			)));
						
			/**
			 * Delete this revision
			 */
			$revisionDao->delete($id);
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('revision_restore_success'));			
			$response = $this->view->url(array('article_id' => $revision->article_id), 'news_article_edit');
		}
		
		$this->getResponse()->setBody($response);
	}
}
