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
 * @version 	$Id: Widget.php 5084 2010-08-29 14:00:49Z huuphuoc $
 * @since		2.0.0
 */

class News_Widgets_Categories_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$limit = $this->_request->getParam('limit', 5);
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$categoryDao->setLang($this->_request->getParam('lang'));
			
		$categories = $categoryDao->getSubCategories(0);
		
		$this->_view->assign('categories', $categories);
		$this->_view->assign('limit', $limit);
		/**
		 * @since 2.0.8
		 */
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
	
	/**
	 * Show latest articles belonging to certain category
	 * 
	 * @since 2.0.8
	 * @return void
	 */
	protected function _prepareLoad()
	{
		$categoryId = $this->_request->getParam('category_id');
		$limit 		= $this->_request->getParam('limit', 5);
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		$category = $categoryDao->getById($categoryId);
		
		$this->_view->assign('category', $category);
		$this->_view->assign('limit', $limit);
		/**
		 * @since 2.0.8
		 */
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
}
