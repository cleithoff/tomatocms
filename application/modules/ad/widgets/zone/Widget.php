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
 * @version 	$Id: Widget.php 5280 2010-09-02 04:11:04Z huuphuoc $
 * @since		2.0.0
 */

class Ad_Widgets_Zone_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$containerId = $this->_request->getParam('container');
		$zoneId 	 = $this->_request->getParam('code');
		$url 		 = $this->_request->getParam('url', 
			//$this->_request->getRequestUri()
			$this->_request->getPathInfo()
		);
		
		/**
		 * Get current route
		 * @since 2.0.8
		 */
		$route = implode('_', array(
							$this->_request->getModuleName(),
							$this->_request->getControllerName(),
							$this->_request->getActionName(),
						));
		$this->_view->assign('route', $route);

		$this->_view->assign('zoneId', $zoneId);
		$this->_view->assign('containerId', $containerId);
		$this->_view->assign('url', $url);
	}
	
	protected function _prepareConfig()
	{
		/**
		 * Get the list of zones
		 */
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$zoneDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
		$zoneDao->setDbConnection($conn);
		$zones = $zoneDao->getZones();
		$this->_view->assign('zones', $zones);
	}
}
