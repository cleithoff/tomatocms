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
 * @version 	$Id: Widget.php 5207 2010-08-30 16:53:06Z huuphuoc $
 * @since		2.0.2
 */

class Tag_Widgets_TagCloud_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$limit 	   = $this->_request->getParam('limit');
		
		/**
		 * @since 2.0.8
		 */
		$routeName = $this->_request->getParam('route', null);
		
		if ($routeName == null || $routeName == '') {
			$router    = Zend_Controller_Front::getInstance()->getRouter();
			$routeName = $router->getCurrentRouteName();
			$currRoute = $router->getCurrentRoute();
			if (!($currRoute instanceof Zend_Controller_Router_Route_Regex)) {
				return;
			}
		}
		
		$params = array();
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$tagItemDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagItemDao();
		$tagItemDao->setDbConnection($conn);
		$rs = $tagItemDao->getTagCloud($routeName, $limit);
		
		/**
		 * Build tag cloud data
		 */
		$items    = array();
		$keywords = array();
		foreach ($rs as $row) {
			$data = array(
				'tag_id' 			 => $row->tag_id, 
				'tag_text' 			 => $row->tag_text, 
				'details_route_name' => $row->details_route_name,
			);
			$items[] = array(
				'title'  => $row->tag_text,
				'weight' => $row->num_items,
				'params' => array(
					'url' => $this->_view->url($data, 'tag_tag_details'),
				),
			);
			
			$keywords[] = $row->tag_text;
		}
		
		/**
		 * Create keywords meta tag containing all tags and put into the head section
		 * @since 2.0.8
		 */
		if (count($keywords) > 0) {
			$className = get_class($this);
			$this->_view->placeholder($className)->append(sprintf('<meta name="keywords" content="%s" />', implode(',', $keywords)));
			Zend_Controller_Front::getInstance()->registerPlugin(new Tomato_Controller_Plugin_PlaceHolder($className));
		}
		
		$cloud = new Zend_Tag_Cloud(array('tags' => $items));
		$this->_view->assign('cloud', $cloud);
	}
	
	/**
	 * @since 2.0.8
	 */
	protected function _prepareConfig()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$pageDao->setDbConnection($conn);
		$pages = $pageDao->getOrdered();
		
		$this->_view->assign('pages', $pages);
	}
}
