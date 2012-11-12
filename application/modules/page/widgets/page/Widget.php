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
 * @version 	$Id: Widget.php 5375 2010-09-10 02:48:01Z huuphuoc $
 * @since		2.0.9
 */

class Page_Widgets_Page_Widget extends Tomato_Widget 
{
	protected function _prepareShow()
	{
		$pageId = $this->_request->getParam('page_id', null);
		
		if ($pageId != null) {
			$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
			$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
			$pageDao->setDbConnection($conn);
			
			$page = $pageDao->getById($pageId);
			
			$this->_view->assign('page', $page);
		}
	}
	
	protected function _prepareConfig()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		$pages = $pageDao->getTree();
		
		$this->_view->assign('pages', $pages);
	}
}
