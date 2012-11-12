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
 * @version 	$Id: Widget.php 4559 2010-08-12 10:43:55Z huuphuoc $
 * @since		2.0.2
 */

class Tag_Widgets_Tags_Widget extends Tomato_Widget 
{
	protected function _prepareShow()
	{
		$limit 	   = $this->_request->getParam('limit');
		$router    = Zend_Controller_Front::getInstance()->getRouter();
		$routeName = $router->getCurrentRouteName();
		$currRoute = $router->getCurrentRoute();
		$params    = array();
		if (!($currRoute instanceof Zend_Controller_Router_Route_Regex)) {
			return;
		}
		$requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		foreach ($currRoute->getVariables() as $variable) {
			$params[] = $variable . ':' . $requestParams[$variable]; 
		}
		$params = '|' . implode('|', $params) . '|';
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$tagDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagDao();
		$tagDao->setDbConnection($conn);
		$tags = $tagDao->getByRoute(new Tag_Models_TagItem(array(
											'route_name' => $routeName,
											'params' 	 => $params,
										)), $limit);

		/**
		 * Create keywords meta tag containing all tags and put into the head section
		 * @since 2.0.8
		 */
		if (count($tags) > 0) {
			$className = get_class($this);
			$keywords  = array();
			foreach ($tags as $tag) {
				$keywords[] = $tag->tag_text;
			}
			$this->_view->placeholder($className)->append(sprintf('<meta name="keywords" content="%s" />', implode(',', $keywords)));
			Zend_Controller_Front::getInstance()->registerPlugin(new Tomato_Controller_Plugin_PlaceHolder($className));
		}
		
		$this->_view->assign('tags', $tags);
	}
}
