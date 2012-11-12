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
 * @version 	$Id: PermalinkController.php 5376 2010-09-10 07:40:05Z huuphuoc $
 * @since		2.0.7
 */

class Core_PermalinkController extends Zend_Controller_Action
{
	/* ========== Backend actions =========================================== */
	
	public function indexAction()
	{
		/**
		 * Permalink routes file
		 */
		$file = TOMATO_APP_DIR . DS . 'config' . DS . 'permalink.ini';
		$usedPemalinks = array();
		if (file_exists($file)) {
			$config = new Zend_Config_Ini($file, 'routes');
			$config = $config->routes;
			
			if ($config != null) {
				$config = $config->toArray();
				foreach ($config as $routeName => $settings) {
					$usedPemalinks[$routeName] = array(
						'url'  => $settings['defaults']['permalink']['url'],
						'type' => $settings['defaults']['permalink']['type'], 				
					);
				}
			}
		}
		
		$routes 	= $this->getFrontController()->getRouter()->getRoutes();
		$permalinks = array();
		
		foreach ($routes as $name => $route) {
			/**
			 * Continue looping if the route is instance of Zend_Controller_Router_Route_Chain
			 * @since 2.0.9
			 */
			if ($route instanceof Zend_Controller_Router_Route_Chain) {
				continue;
			}
			
			$defaults = $route->getDefaults();
			if (isset($defaults['permalink']['enable']) && 'true' == (string)$defaults['permalink']['enable']) {
				
				$permalinks[$name] = array(
					'module'	  => $defaults['module'],
					'controller'  => $defaults['controller'],
					'action'	  => $defaults['action'],
					'frontend'	  => $defaults['frontend'],
					'langKey'	  => $defaults['langKey'],
					'description' => $this->view->translator($defaults['permalink']['langKey'], $defaults['module']),
					'params'  	  => $defaults['permalink']['params'],
					'default'	  => $defaults['permalink']['default'],
				);
				
				if (isset($defaults['permalink']['predefined'])) {
					$permalinks[$name]['predefined'] = $defaults['permalink']['predefined'];
				}
			}
		}
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$routeNames = $request->getPost('routeNames');
			$urlTypes   = $request->getPost('urlType');
			$urls   	= $request->getPost('url');
			
			$output = array();
			foreach ($routeNames as $name) {
				/**
				 * Do NOT save to file if user use default route URL
				 */
				if ($urlTypes[$name] != 'default') {
					/**
					 * Get the config URL
					 */
					$url	 = $urls[$name][0];
					$route   = $url;
					$reverse = $url;
					$map     = array();
					
					preg_match_all('/{(\w+)}/', $url, $matches);
					if (count($matches) > 0) {
						foreach ($matches[1] as $index => $param) {
							$map[$index + 1] = $param;
							if (isset($permalinks[$name]['params'][$param])) {
								$settings = $permalinks[$name]['params'][$param];
								$route    = str_replace('{' . $settings['name'] . '}', $settings['regex'], $route);
								$reverse  = str_replace('{' . $settings['name'] . '}', $settings['reverse'], $reverse);
							}
						}
					}
					
					$defaults = $routes[$name]->getDefaults();
					$defaults['permalink']['url']  = $url;
					$defaults['permalink']['type'] = $urlTypes[$name];
					
					$output['routes'][$name] = array(
						'type' 	   => 'Zend_Controller_Router_Route_Regex',
						'route'    => $route,
						'reverse'  => $reverse,
						'defaults' => $defaults,
						'map' 	   => $map,
					);
					
					if (isset($permalinks[$name])) {
						$output['routes'][$name]['defaults']['langKey'] = $permalinks[$name]['langKey'];
					}
				}
			}
			
			/**
			 * Save to file
			 */
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config(array('routes' => $output)));
			
			/**
			 * Redirect
			 */
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('permalink_index_save_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_permalink_index'));
		}
		
		$this->view->assign('usedPemalinks', $usedPemalinks);
		$this->view->assign('permalinks', $permalinks);
	}
}
