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
 * @version 	$Id: Widget.php 4569 2010-08-12 18:04:39Z huuphuoc $
 * @since		2.0.0
 */

class News_Widgets_CategoryTree_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$articleDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$categoryDao->setDbConnection($conn);
		$articleDao->setDbConnection($conn);
		
		$categories = $categoryDao->getTree();
		
		$this->_view->assign('categories', $categories);
		$this->_view->assign('articleDao', $articleDao);
	}	
}
