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
 * @version 	$Id: Widget.php 4541 2010-08-12 09:58:41Z huuphuoc $
 * @since		2.0.2
 */

class Menu_Widgets_Menu_Widget extends Tomato_Widget
{
	protected function _prepareShow()
	{
		$menuId = $this->_request->getParam('menu_id');
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$itemDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getItemDao();
		$itemDao->setDbConnection($conn);
		
		$items = $itemDao->getTree($menuId);
		
		/**
		 * Use Zend_Navigation to render the menu
		 * @since 2.0.7
		 */
		$container = new Zend_Navigation();
		foreach ($items as $item) {
			$page = new Zend_Navigation_Page_Uri(array(
				'label' => $item->label,
				'uri'   => $item->link,
				'id'    => $item->item_id,
			));
			
			if ($item->parent_id == 0) {
				$container->addPage($page);
			} else {
				$page->setParent($container->findOneById($item->parent_id));
			}
		}
		
		$this->_view->assign('uuid', uniqid());
		$this->_view->assign('container', $container);
	}
	
	protected function _prepareConfig()
	{
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$menuDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getMenuDao();
		$menuDao->setDbConnection($conn);
		$menus = $menuDao->getMenus();
		$this->_view->assign('menus', $menus);
	}
}
