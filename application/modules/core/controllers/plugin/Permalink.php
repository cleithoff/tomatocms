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
 * @version 	$Id: Permalink.php 4723 2010-08-18 06:33:59Z huuphuoc $
 * @since		2.0.7
 */

class Core_Controllers_Plugin_Permalink extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		/**
		 * Do nothing if we are in page of managing permalink
		 */
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);
		$uri = rtrim($uri, '/') . '/';
		if (is_int(strpos($uri, '/admin/core/permalink'))) {
			return;
		}
		
		$file = TOMATO_APP_DIR . DS . 'config' . DS . 'permalink.ini';
		if (!file_exists($file)) {
			return;
		}
		
		/**
		 * Get router instance
		 */
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$config = new Zend_Config_Ini($file, 'routes');
		
		/**
		 * Remove the routes if they already exist
		 */
		if (!isset($config->routes)) {
			return;
		}
		
		$routes = $config->routes;
		foreach ($routes as $name => $info) {
			if ($router->hasRoute($name)) {
				$router->removeRoute($name);
			}
		}
		
		/**
		 * And add routes again which their configurations have been set 
		 * in permalink.ini file
		 */
		$router->addConfig($config, 'routes');
	}
}
