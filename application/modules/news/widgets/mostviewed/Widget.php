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
 * @version 	$Id: Widget.php 4714 2010-08-17 02:32:22Z huuphuoc $
 * @since		2.0.0
 */

class News_Widgets_MostViewed_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$limit 		= $this->_request->getParam('limit', 15);
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setWidget($this)->getArticleDao();
		$articleDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$articleDao->setLang($this->_request->getParam('lang'));
		
		$articles = $articleDao->getMostViewedArticles($limit, $categoryId);
		
		$this->_view->assign('articles', $articles);
		$this->_view->assign('categoryId', $categoryId);
		$this->_view->assign('uuid', uniqid());
	}
		
	protected function _prepareConfig() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		$categories = $categoryDao->getTree();
		
		$this->_view->assign('categories', $categories); 
	}
}
