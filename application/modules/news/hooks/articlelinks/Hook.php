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
 * @version 	$Id: Hook.php 4690 2010-08-16 09:49:17Z huuphuoc $
 * @since		2.0.7
 */

class News_Hooks_ArticleLinks_Hook extends Tomato_Hook
{
	/**
	 * @param array $links
	 * @param string $lang (since 2.0.8)
	 * @return array
	 */
	public static function filter($links, $lang)
	{
		/**
		 * Get the view instance
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		
		/**
		 * Get most recently activated articles
		 * TODO: Make this variable configurable
		 */
		$limit = 10;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		$articleDao->setLang($lang);
		$articles = $articleDao->find(0, $limit, array('status' => 'active'));
		
		if (count($articles) > 0) {
			foreach ($articles as $article) {
				$links['news_article_details'][] = array(
					'title' => $article->title,
					'href'  => $view->url($article->getProperties(), 'news_article_details'),
				);
			}
		}
		
		/**
		 * Get categories links
		 */
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		$categoryDao->setLang($lang);
		$categories = $categoryDao->getTree();
		if (count($categories) > 0) {
			foreach ($categories as $category) {
				$links['news_article_category'][] = array(
					'title' => str_repeat('---', $category->depth) . ' ' . $category->name,
					'href'  => $view->url($category->getProperties(), 'news_article_category'),
				);
				
				$links['news_rss_category'][] = array(
					'title' => str_repeat('---', $category->depth) . ' ' . $category->name,
					'href'  => $view->url($category->getProperties(), 'news_rss_category'),
				);
			}
		}
		
		/**
		 * RSS links for latest articles
		 */
		$links['news_rss_index'][] = array(
			'href' => $view->url(array('language' => $lang), 'news_rss_index'),
		);
		
		return $links;
	}
}
