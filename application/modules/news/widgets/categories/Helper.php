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
 * @version 	$Id: Helper.php 5230 2010-08-31 04:38:12Z huuphuoc $
 * @since		2.0.0
 */

class News_Widgets_Categories_Helper extends Zend_View_Helper_Abstract 
{
	public function helper() 
	{
		return $this;
	}
	
	public function getSubCategories($category, $depth = 1)
	{
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn)
					/**
					 * @since 2.0.8
					 */
					->setLang($category->language);
			
		return $categoryDao->getSubCategories($category->category_id, $depth);
	}
	
	public function getLatestArticles($category, $limit = 5) 
	{
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		
		$exp = array('status' => 'active');
		if ($category != null) {
			/**
			 * @since 2.0.8
			 */
			$articleDao->setLang($category->language);
			$exp['category_id'] = $category->category_id;
		}
		return $articleDao->find(0, $limit, $exp);
	}
}
