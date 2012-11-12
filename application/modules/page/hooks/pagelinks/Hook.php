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
 * @version 	$Id: Hook.php 4688 2010-08-16 09:32:47Z huuphuoc $
 * @since		2.0.7
 */

class Page_Hooks_PageLinks_Hook extends Tomato_Hook
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
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		$pageDao->setLang($lang);
		$pages = $pageDao->getTree();
		
		if ($pages != null) {
			foreach ($pages as $page) {
				$links['page_page_details'][] = array(
					'title' => str_repeat('---', $page->depth) . ' ' . $page->name,
					'href'  => $view->url($page->getProperties(), 'page_page_details'),
				);
			}
		}
		
		return $links;
	}
}
