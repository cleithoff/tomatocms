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
 * @version 	$Id: Widget.php 5400 2010-09-12 17:53:28Z huuphuoc $
 * @since		2.0.9
 */

class News_Widgets_Category_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$categoryId = $this->_request->getParam('category_id');
		$limit      = (int) $this->_request->getParam('limit', 5);
		$limit      = (0 == $limit) ? 5 : $limit;
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$categoryDao->setDbConnection($conn);
		$articleDao->setDbConnection($conn);
		
		$category = $categoryDao->getById($categoryId);
		$articles = $articleDao->find(0, $limit, array('status' => 'active', 'category_id' => $categoryId));
		
		$this->_view->assign('category', $category);
		$this->_view->assign('articles', $articles);
		$this->_view->assign('dateFormat', array(
			'DAY' 			=> $this->_view->translator()->widget('diff_day_format'),
			'DAY_HOUR'		=> $this->_view->translator()->widget('diff_day_hour_format'),
			'HOUR' 			=> $this->_view->translator()->widget('diff_hour_format'),
			'HOUR_MINUTE' 	=> $this->_view->translator()->widget('diff_hour_minute_format'),
			'MINUTE' 		=> $this->_view->translator()->widget('diff_minute_format'),
			'MINUTE_SECOND'	=> $this->_view->translator()->widget('diff_minute_second_format'),
			'SECOND'		=> $this->_view->translator()->widget('diff_second_format'),
		));
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
