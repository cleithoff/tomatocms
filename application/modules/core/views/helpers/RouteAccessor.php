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
 * @version 	$Id: RouteAccessor.php 3749 2010-07-17 11:49:58Z huuphuoc $
 * @since		2.0.5
 */

class Core_View_Helper_RouteAccessor extends Zend_View_Helper_Abstract 
{
	/**
	 * List of routes
	 * 
	 * @var array
	 */
	private $_routes;
	
	/**
	 * Check whether user can access page defined by given route or not
	 * 
	 * @param string $routeName
	 * @return bool
	 */
	public function routeAccessor($routeName) 
	{
		/**
		 * Cache list of routes
		 */
		if ($this->_routes == null) {
			$router = Zend_Controller_Front::getInstance()->getRouter();
			$this->_routes = $router->getRoutes();
		}
		if (!isset($this->_routes[$routeName])) {
			return false;
		}
		/**
		 * Get the route
		 */
		$routes = $this->_routes[$routeName];
		
		/**
		 * Get the array of action, controller and module associated with route
		 */
		$defs = $routes->getDefaults();
		
		return Core_Services_RuleChecker::isAllowed($defs['action'], $defs['controller'], $defs['module']);
	}
}
