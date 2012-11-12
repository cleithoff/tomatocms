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
 * @version 	$Id: Widget.php 5373 2010-09-10 02:22:07Z huuphuoc $
 * @since		2.0.9
 */

class News_Widgets_Article_Widget extends Tomato_Widget 
{
	const LIMIT = 10;
	
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$articleId  = $this->_request->getParam('article_id');
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$categoryDao->setDbConnection($conn);
		$articleDao->setDbConnection($conn);
		
		if ($articleId != '') {
			$article  = $articleDao->getById($articleId);
		} elseif ($categoryId == '') {
			$result  = $articleDao->find(0, 1, array('status' => 'active'));
			$article = (null == $result || 0 == count($result)) ? null : $result[0];
		} else {
			$result  = $articleDao->find(0, 1, array('status' => 'active', 'category_id' => $categoryId));
			$article = (null == $result || 0 == count($result)) ? null : $result[0];
		}
		$category = (null == $article) ? null : $categoryDao->getById($article->category_id);
		
		$this->_view->assign('article', $article);
		$this->_view->assign('category', $category);
	}
	
	protected function _prepareConfig() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$categoryDao->setDbConnection($conn);
		$articleDao->setDbConnection($conn);
		
		$categories = $categoryDao->getTree();
		
		$params = $this->_request->getParam('params');
		if ($params) {
			$params  = Zend_Json::decode($params);
			$articleId  = $params['article_id']['value'];
			$categoryId = $params['category_id']['value'];
			
			if ($articleId != '') {
				$article = $articleDao->getById($articleId);
				$this->_view->assign('article', $article);
			}
			
			if ($categoryId != '') {
				$articles = $articleDao->find(0, self::LIMIT, array('status' => 'active', 'category_id' => $categoryId));
				$this->_view->assign('articles', $articles);
			}
		}
		
		$this->_view->assign('categories', $categories);
	}
	
	protected function _prepareLoad()
	{
		$categoryId = $this->_request->getParam('categoryId');
		$limit 		= $this->_request->getParam('limit');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		
		$lang = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
		$articleDao->setLang($lang);
		
		$exp = array('status' => 'active');
		if ($categoryId != null && $categoryId != '') {
			$exp['category_id'] = $categoryId;
		}
		
		$result = $articleDao->find(0, $limit, $exp);

		$articles = array();
		if ($result) {
			foreach ($result as $row) {
				$articles[] = $row->getProperties();
			}
		}
		
		$this->_response->setBody(Zend_Json::encode($articles));
	}
}
