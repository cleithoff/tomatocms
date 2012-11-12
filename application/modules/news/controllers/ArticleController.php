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
 * @version 	$Id: ArticleController.php 5462 2010-09-20 04:45:41Z huuphuoc $
 * @since		2.0.0
 */

class News_ArticleController extends Zend_Controller_Action 
{
	/**
	 * Init controller
	 * 
	 * @return void
	 */
	public function init() 
	{
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Hook_Registry::getInstance()->register('News_Article_Add_ShowSidebar', array(
			new Tag_Hooks_Tagger_Hook(), 
			'show', 
			array('article_id', 'news_article_details', 'news_tag_article')
		));
		Tomato_Hook_Registry::getInstance()->register('News_Article_Edit_ShowSidebar', array(
			new Tag_Hooks_Tagger_Hook(), 
			'show', 
			array('article_id', 'news_article_details', 'news_tag_article')
		));
		Tomato_Hook_Registry::getInstance()->register(
			'News_Article_Add_Success',
			'Tag_Hooks_Tagger_Hook::add'
		);
		Tomato_Hook_Registry::getInstance()->register(
			'News_Article_Edit_Success',
			'Tag_Hooks_Tagger_Hook::add'
		);
	}
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * List articles in given category
	 * 
	 * @return void
	 */
	public function categoryAction() 
	{
		$request	= $this->getRequest();
		$categoryId = $request->getParam('category_id');
		$pageIndex 	= $request->getParam('page_index', 1);
		$perPage = 5;
		$offset	 = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		$category = $categoryDao->getById($categoryId);
		
		if (null == $category) {
			throw new Tomato_Exception_NotFound();
		}
		
		/**
		 * Add RSS link
		 */
		$this->view->headLink(array(
			'rel' 	=> 'alternate', 
			'type' 	=> 'application/rss+xml', 
			'title' => 'RSS',
			'href' 	=> $this->view->url($category->getProperties(), 'news_rss_category'),
		));
		
		/**
		 * Add meta keyword tag
		 */
		if ($category->meta) {
			$keyword = strip_tags($category->meta);
			$this->view->headMeta()->setName('keyword', $keyword);
		}
		
		/**
		 * Add meta description tag
		 */
		$description = strip_tags($category->name);
		$this->view->headMeta()->setName('description', $description);
		
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		$articles    = $articleDao->getByCategory($categoryId, $offset, $perPage);
		$numArticles = $articleDao->count(array('status' => 'active', 'category_id' => $categoryId)); 
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($articles, $numArticles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('articles', $articles);
		$this->view->assign('category', $category);
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url($category->getProperties(), 'news_article_category'),
			'itemLink' => 'page-%d',
		));
	}	
	
	/**
	 * View article details
	 * 
	 * @return void
	 */
	public function detailsAction()
	{
		$request	= $this->getRequest();
		$articleId 	= $request->getParam('article_id');
		$categoryId = $request->getParam('category_id');
		$preview 	= $request->getParam('preview');
		$preview 	= ($preview == 'true') ? true : false;
		
		/**
		 * If user are going to preview article
		 * @since 2.0.4
		 */
		if ($preview) {
			$revisionId = $request->getParam('revision');
			if ($revisionId) {
				$this->_forward('preview', 'article', 'news', array('revision_id' => $revisionId));
				return;
			}
		}
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		$article = $articleDao->getById($articleId);
		
		if (null == $article || ($article->status != 'active' && !$preview)) {
			throw new Exception('Not found article with Id of ' . $articleId);
		}
		
		/**
		 * Add meta description tag
		 */
		$description = strip_tags($article->description);
		$this->view->headMeta()->setName('description', $description);
		
		/**
		 * Format content
		 */
		$article->content = Tomato_Hook_Registry::getInstance()->executeFilter('News_Article_Details_FormatContent', $article->content);
		
		/**
		 * Add activate date
		 * @since 2.0.4
		 */
		if (null == $article->activate_date) {
			$article->activate_date = $article->created_date;
		}
		
		if (null == $categoryId) {
			$categoryId = $article->category_id;
		}
		
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		$category = $categoryDao->getById($categoryId);
		
		/**
		 * Increase article views
		 */ 
		if (!$preview && $article->status != 'draft') {
			$cookieName = '__tomato_news_details_numviews';
			$viewed = false;
			if (!isset($_COOKIE[$cookieName])) {
				setcookie($cookieName, $articleId, time() + 3600);
			} else {
				if (strpos($_COOKIE[$cookieName], $articleId) === false) {
					$cookie = $_COOKIE[$cookieName].','.$articleId;
					setcookie($cookieName, $cookie);
				} else {
					$viewed = true;
				}
			}
			if (!$viewed) {
				$conn = Tomato_Db_Connection::factory()->getMasterConnection();
				$articleDao->setDbConnection($conn);
				$articleDao->increaseViews($articleId);
			}
		}
		
		$this->view->assign('article', $article);
		$this->view->assign('category', $category);
	}
	
	/**
	 * Search articles by keyword
	 * 
	 * @since 2.0.2
	 * @return void
	 */
	public function searchAction()
	{
		$request   = $this->getRequest();
		$keyword   = $request->getParam('q');
		$pageIndex = $request->getParam('page_index', 1);
		
		$purifier  = new HTMLPurifier();
		$keyword   = $purifier->purify($keyword);
		
		$perPage = 10;
		$offset  = ($pageIndex - 1) * $perPage;
		
		if (null == $keyword) {
			return;
		}
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		
		$exp = array(
			'keyword' => $keyword,
			'status'  => 'active',
		);
		$articles 	 = $articleDao->find($offset, $perPage, $exp);
		$numArticles = $articleDao->count($exp);

		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($articles, $numArticles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$from = ($numArticles > 0) ? $offset + 1 : 0;
		$to   = 0;
		if ($numArticles > 0) {
			$to = ($from + $perPage <= $numArticles) ? ($from + $perPage - 1): $numArticles;	
		}
		
		$this->view->assign('keyword', $keyword);
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('articles', $articles);
		$this->view->assign('numArticles', $numArticles);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'news_article_search'),
			'itemLink' => '?q=' . $keyword . '&page_index=%d',
		));
		$this->view->assign('from', $from);
		$this->view->assign('to', $to);
	}	
	
	/**
	 * Suggest list of articles containing keyword
	 * 
	 * @since 2.0.3
	 * @return void 
	 */
	public function suggestAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		$limit 	 = $request->getParam('limit');
		$keyword = $request->getParam('q');
		$keyword = strip_tags($keyword);
		
		$exp = array(
			'keyword' => $keyword,
			'status'  => 'active',
		);
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		$articles = $articleDao->find(0, $limit, $exp);
		
		$response = null;
		foreach ($articles as $article) {
			$response .= $article->title . '|' . $article->article_id . '|' . $article->image_square . "\n";
		}
		$this->_response->setBody($response);
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Activate or deactivate article
	 * 
	 * @return void
	 */
	public function activateAction() 
	{
		$request = $this->getRequest();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if ($request->isPost()) {
			$status = ($request->getPost('status') == 'active') ? 'inactive' : 'active';
			$id     = $request->getPost('id');
			$ids    = array();
			if (is_numeric($id)) {
				$ids[] = $id;
			} else {
				$ids = Zend_Json::decode($id);
			}
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
			$articleDao->setDbConnection($conn);
			foreach ($ids as $articleId) {
				$articleDao->updateStatus($articleId, $status);
			}
			
			$this->getResponse()->setBody($status);
		}
	}
	
	/**
	 * Add new article
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
		$articleDao->setDbConnection($conn);
		$revisionDao->setDbConnection($conn);
		
		$descriptionPrefix = Tomato_Module_Config::getConfig('news')->general->description_prefix;
		if (null == $descriptionPrefix) {
			$descriptionPrefix = '';
		}
		
		$this->view->assign('descriptionPrefix', stripslashes($descriptionPrefix));
		
		/**
		 * @since 2.0.8
		 */
		$sourceId      = $request->getParam('source_id');
		$sourceArticle = (null == $sourceId) ? null : $articleDao->getById($sourceId);
		$this->view->assign('translatableData', (null == $sourceArticle) ? array() : $sourceArticle->getProperties());
		$this->view->assign('sourceArticle', $sourceArticle);
		$this->view->assign('lang', $request->getParam('lang'));
		
		if ($request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$categoryId   = $request->getPost('category');
			$title 		  = $request->getPost('title');
			$subTitle 	  = $request->getPost('subTitle');
			$slug 		  = $request->getPost('slug');			
			$description  = $request->getPost('description');
			$content 	  = $request->getPost('content');
			$allowComment = $request->getPost('allowComment');
			$sticky 	  = $request->getPost('stickyCategory');
			$articleCats  = $request->getPost('categories');
			$icons 		  = $request->getPost('icons'); 
			$articleImage = $request->getPost('articleImage');
			$author 	  = $request->getPost('author');
			$preview 	  = $request->getPost('preview');
			$preview 	  = ($preview == 'true') ? true : false;
			
			$imageUrls 	  = Zend_Json::decode(stripslashes($articleImage));
			$articleIcons = "";
			if (count($icons) == 1 ) {
				$articleIcons = '{"' . $icons[0] . '"}';
			}
			if (count($icons) == 2 ) {
				$articleIcons = '{"' . $icons[0] . '","' . $icons[1] . '"}';
			}
			$article = new News_Models_Article(array(
				'category_id' 		=> $categoryId,
				'title' 			=> strip_tags($title),	
				'sub_title' 		=> strip_tags($subTitle),
				'slug' 				=> $slug,
				'description' 		=> $description,
				'content' 			=> $content,
				'allow_comment' 	=> $allowComment,
				'created_date' 		=> date('Y-m-d H:i:s'),
				'created_user_id' 	=> $user->user_id,
				'created_user_name' => $user->user_name,
				'author' 			=> strip_tags($author),
				'icons' 			=> $articleIcons,
				'sticky' 			=> false,
			
				/**
				 * @since 2.0.8
				 */
				'language'          => $request->getPost('languageSelector'),
			));
			if ($preview) {
				$article->status = 'draft';
			}
			if ($sticky == 1) {
				$article->sticky = true;
			}
			if (null != $imageUrls) {
				$article->image_square 	  = $imageUrls['square'];
				$article->image_thumbnail = $imageUrls['thumbnail'];
				$article->image_small 	  = $imageUrls['small'];
				$article->image_crop 	  = $imageUrls['crop'];
				$article->image_medium 	  = $imageUrls['medium'];
				$article->image_large 	  = $imageUrls['large'];
			}
			$id = $articleDao->add($article);
			if ($id > 0) {
				/**
				 * Add translation relation
				 * @since 2.0.8
				 */
				$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
				
				$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
				$translationDao->setDbConnection($conn);
				$translationDao->add(new Core_Models_Translation(array(
					'item_id' 	      => $id,
					'item_class'      => get_class($article),
					'source_item_id'  => ('' == $source['id']) ? $id : $source['id'],
					'language'        => $article->language,
					'source_language' => ('' == $source['language']) ? null : $source['language'],
				)));
				
				/**
				 * Save draft and preview article
				 * @since 2.0.4
				 */
				if ($preview) {
					$this->_helper->getHelper('viewRenderer')->setNoRender();
					$this->_helper->getHelper('layout')->disableLayout();
					$article->article_id = $id;
					$response = array(
						'article_id' 	   => $id,
						'article_url' 	   => $this->view->serverUrl() . $this->view->url($article->getProperties(), 'news_article_details') . '?preview=true',
						'article_edit_url' => $this->view->serverUrl() . $this->view->url($article->getProperties(), 'news_article_edit'),
					);
					$this->_response->setBody(Zend_Json::encode($response));
				} else {
					$articleDao->addToCategory($id, $categoryId);
					if ($articleCats) {
						for ($i = 0; $i < count($articleCats); $i++) {
							if ($articleCats[$i] != $categoryId) {
								$articleDao->addToCategory($id, $articleCats[$i]);
							}
						}
					}
					
					if ($request->getPost('hotArticle') == 1) {
						$articleDao->addHotArticle($id);
					}
					
					/**
					 * Add new revistion
					 * @since 2.0.4
					 */
					$revisionDao->add(new News_Models_Revision(array(
						'article_id' 		=> $id,
						'category_id' 		=> $categoryId,
						'title' 			=> $title,	
						'sub_title' 		=> $subTitle,
						'slug' 				=> $slug,
						'description' 		=> $description,
						'content' 			=> $content,
						'created_date' 		=> date('Y-m-d H:i:s'),
						'created_user_id' 	=> $user->user_id,
						'created_user_name' => $user->user_name,
						'author' 			=> $author,
						'icons' 			=> $articleIcons,
					)));
					
					/**
					 * Execute hooks
					 * @since 2.0.2
					 */
					Tomato_Hook_Registry::getInstance()->executeAction('News_Article_Add_Success', $id);
					
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('article_add_success'));
					//$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'news_article_add'));
					$this->_redirect($this->view->serverUrl() .  $this->view->url(array('article_id' => $id), 'news_article_edit') . '/' . $article->language . '/');
				}
			}
		}
	}
	
	/**
	 * Delete article
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();

		$request = $this->getRequest();
		$result  = 'RESULT_ERROR';
		if ($request->isPost()) {
			$id     = $request->getPost('id');
			$ids    = array();
			if (is_numeric($id)) {
				$ids[] = $id;
			} else {
				$ids = Zend_Json::decode($id);
			}
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
			$articleDao->setDbConnection($conn);
			
			foreach ($ids as $articleId) {
				$article = $articleDao->getById($articleId);
				if ($article != null) {
					if ('deleted' == $article->status) {
						$articleDao->delete($articleId);
						
						/**
						 * @since 2.0.8
						 */
						$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
						$translationDao->setDbConnection($conn);
						$translationDao->delete($articleId, get_class($article));
					} else { 
						$articleDao->updateStatus($articleId, 'deleted');
					}
				}
			}
			$result = 'RESULT_OK';
		}
		$this->getResponse()->setBody($result);
	}	
	
	/**
	 * Edit article
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request   = $this->getRequest();
		$articleId = $request->getParam('article_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
		$articleDao->setDbConnection($conn);
		$revisionDao->setDbConnection($conn);
		
		$article = $articleDao->getById($articleId);
		if (null == $article) {
			throw new Exception('Not found article with id of ' . $articleId);
		}
		
		/**
		 * @since 2.0.8
		 */
		$sourceArticle = $articleDao->getSource($article);
		
		$categories    = $articleDao->getCategoryIds($articleId);
		$isHot         = $articleDao->isHot($articleId);
		
		$this->view->assign('article', $article);
		$this->view->assign('articleImages', Zend_Json::encode(array(
			'square'    => $article->image_square,
			'thumbnail' => $article->image_thumbnail,
			'small'     => $article->image_small,
			'crop' 	    => $article->image_crop,
			'medium'    => $article->image_medium,
			'large'     => $article->image_large,
		)));
		$this->view->assign('categories', $categories);
		$this->view->assign('hotArticle', $isHot);
		$this->view->assign('sourceArticle', $sourceArticle);
		
		if ($request->isPost()) {
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$categoryId   = $request->getPost('category');
			$sticky	 	  = $request->getPost('stickyCategory');
			$articleCats  = $request->getPost('categories');
			$icons 		  = $request->getPost('icons'); 
			$articleImage = $request->getPost('articleImage');
			$imageUrls 	  = Zend_Json::decode(stripslashes($articleImage));
			$preview	  = $request->getPost('preview');
			$preview 	  = ($preview == 'true') ? true : false;
			$articleIcons = "";
			if (count($icons) == 1 ) {
				$articleIcons = '{"' . $icons[0] . '"}';
			}
			if (count($icons) == 2 ) {
				$articleIcons = '{"' . $icons[0] . '","' . $icons[1] . '"}';
			}
			$article->category_id 		= $categoryId;
			$article->title 	  		= strip_tags($request->getPost('title'));	
			$article->sub_title 		= strip_tags($request->getPost('subTitle'));
			$article->slug 			    = $request->getPost('slug');
			$article->description       = $request->getPost('description');
			$article->content           = $request->getPost('content');
			$article->allow_comment     = $request->getPost('allowComment');
			$article->updated_date      = date('Y-m-d H:i:s');
			$article->updated_user_id   = $user->user_id;
			$article->updated_user_name = $user->user_name;
			$article->author 			= strip_tags($request->getPost('author'));
			$article->icons 			= $articleIcons;
			$article->sticky 			= false;
			
			/**
			 * @since 2.0.8
			 */
			$article->language 			= $request->getPost('languageSelector', $article->language);
			
			if ($sticky == 1) {
				$article->sticky = true;
			}
			if (null != $imageUrls) {
				$article->image_square 	  = $imageUrls['square'];
				$article->image_thumbnail = $imageUrls['thumbnail'];
				$article->image_small 	  = $imageUrls['small'];
				$article->image_crop 	  = $imageUrls['crop'];
				$article->image_medium 	  = $imageUrls['medium'];
				$article->image_large 	  = $imageUrls['large'];
			}
			$result = $articleDao->update($article);
			
			/**
			 * Update translation relation
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->update(new Core_Models_Translation(array(
				'item_id' 	      => $article->article_id,
				'item_class'      => get_class($article),
				'source_item_id'  => ('' == $source['id']) ? $article->article_id : $source['id'],
				'language'        => $article->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			if ($preview) {
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				$response = array(
					'article_id'  => $article->article_id,
					'article_url' => $this->view->serverUrl() . $this->view->url($article->getProperties(), 'news_article_details') . '?preview=true',
				);
				$this->_response->setBody(Zend_Json::encode($response));
			} else {
				$articleDao->addToCategory($articleId, $categoryId, true);
				if ($articleCats) {
					for ($i = 0; $i < count($articleCats); $i++) {
						if ($articleCats[$i] != $categoryId) {
							$articleDao->addToCategory($articleId, $articleCats[$i]);
						}
					}
				}
				if ($request->getPost('hotArticle') == 1) {
					$articleDao->addHotArticle($articleId, true);
				}
			
				/**
				 * Add new revistion
				 * @since 2.0.4
				 */
				$revisionDao->add(new News_Models_Revision(array(
					'article_id' 		=> $articleId,
					'category_id' 		=> $categoryId,
					'title' 			=> $article->title,	
					'sub_title' 		=> $article->sub_title,
					'slug' 				=> $article->slug,
					'description' 		=> $article->description,
					'content' 			=> $article->content,
					'created_date' 		=> date('Y-m-d H:i:s'),
					'created_user_id' 	=> $user->user_id,
					'created_user_name' => $user->user_name,
					'author'			=> $article->author,
					'icons' 			=> $articleIcons,
				)));
				
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Hook_Registry::getInstance()->executeAction('News_Article_Edit_Success', $articleId);
				
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('article_edit_success'));
					
				/**
				 * Redirect to edit form
				 */
				$this->_redirect($this->view->serverUrl() .  $this->view->url(array('article_id' => $articleId), 'news_article_edit') . '/' . $request->getPost('languageSelector') . '/');
			}
		}
	}
	
	/**
	 * Empty the trash
	 * 
	 * @return void
	 * @since 2.0.7
	 */
	public function emptytrashAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
			$articleDao->setDbConnection($conn);
			$articleDao->delete();
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Manage hot articles
	 * 
	 * @return void
	 */
	public function hotAction() 
	{
		$request = $this->getRequest();
		$limit 	 = 20;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$lang = $request->getParam('lang', Tomato_Config::getConfig()->web->lang);
		$articleDao->setLang($lang);
		
		if ($request->isPost()) {
			$this->_helper->getHelper('layout')->disableLayout();
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$articleIds = $request->getPost('articleRow');
			$response 	= 'RESULT_ERROR';
			
			/**
			 * Update ordering all hot articles
			 */
			$articleDao->updateHotOrder(1000);
			
			if (is_array($articleIds)) {
				for ($i = 0; $i < count($articleIds); $i++) {
					$articleDao->updateHotOrder($i + 1, $articleIds[$i]);
				}
				$response = 'RESULT_OK';
			}
			$this->getResponse()->setBody($response);
			return;
		}
		
		$articles = $articleDao->getHotArticles($limit);
		$this->view->assign('articles', $articles);
		$this->view->assign('numArticles', $articles->count());
	}
	
	/**
	 * List articles
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		
		$request 	= $this->getRequest();
		$pageIndex 	= $request->getParam('pageIndex', 1);
		$perPage = 20;
		$offset  = ($pageIndex - 1) * $perPage;
		
		/**
		 * Build article search expression
		 */
		$user 	= Zend_Auth::getInstance()->getIdentity();
		$params = null;
		$exp 	= array(
			'created_user_id' => $user->user_id,
		);
		
		/**
		 * @since 2.0.8
		 */
		$lang = $request->getParam('lang', Tomato_Config::getConfig()->web->lang);
		$articleDao->setLang($lang);
		
		$this->view->assign('pageIndex', $pageIndex);
		
		if ($request->isPost()) {
			$id 		  = $request->getPost('articleId');
			$keyword 	  = $request->getPost('keyword');
			$categoryId   = $request->getPost('category');
			$findMineOnly = $request->getPost('findMyArticles');
			$purifier     = new HTMLPurifier();
			
			if ($keyword) {
				$exp['keyword'] = $purifier->purify($keyword);
			}
			if ($id) {
				$exp['article_id'] = $purifier->purify($id);
			}
			if ($categoryId) {
				$exp['category_id'] = $categoryId;
			}
			if (null == $findMineOnly) {
				$exp['created_user_id'] = null;
			}
			if ($request->getPost('status')) {
				$exp['status'] = $request->getPost('status');
			}
			$params = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		} else {
			$params = $request->getParam('q');
			if ($params != null) {
				$exp = rawurldecode(base64_decode($params));
				$exp = Zend_Json::decode($exp);
			} else {
				$params = rawurlencode(base64_encode(Zend_Json::encode($exp)));
			}
		}
		
		$articles 	 = $articleDao->find($offset, $perPage, $exp);
		$numArticles = $articleDao->count($exp);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($articles, $numArticles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('exp', $exp);
		$this->view->assign('articles', $articles);
		$this->view->assign('numArticles', $numArticles);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path'	   => $this->view->url(array(), 'news_article_list'),
			'itemLink' => (null == $params)
							? 'page-%d/' . $request->getParam('lang') . '/'
							: 'page-%d/' . $request->getParam('lang') . '?q=' . $params,
		));
	}
	
	/**
	 * Preview article
	 * 
	 * @since 2.0.4
	 * @return void
	 */
	public function previewAction() 
	{
		$request 	= $this->getRequest();
		$revisionId = $request->getUserParam('revision_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$revisionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getRevisionDao();
		$revisionDao->setDbConnection($conn);
		$revision = $revisionDao->getById($revisionId);
		if (null == $revision) {
			throw new Tomato_Exception_NotFound();
		}
		$this->view->assign('revision', $revision);
	}
}
