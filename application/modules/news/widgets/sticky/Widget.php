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
 * @version 	$Id: Widget.php 5097 2010-08-29 19:43:44Z huuphuoc $
 * @since		2.0.0
 */

class News_Widgets_Sticky_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id', null);
		$limit 		= $this->_request->getParam('limit', 6);
		
		if ($categoryId != null) {
			$categoryId = ltrim($categoryId, '');
			$categoryId = rtrim($categoryId, '');
			if ($categoryId == '') {
				$categoryId = null;
			}
		}
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$dao = Tomato_Model_Dao_Factory::getInstance()->setWidget($this)->getArticleDao();
		$dao->setDbConnection($conn);
		$articles = $dao->getStickyArticles($limit, $categoryId);
		
		$this->_view->assign('articles', $articles);
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
