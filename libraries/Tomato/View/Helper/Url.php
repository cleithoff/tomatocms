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
 * @version 	$Id: Url.php 5351 2010-09-09 01:40:20Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_View_Helper_Url extends Zend_View_Helper_Url
{
	public function url(array $urlOptions = array(), $name = null)
	{
		$url = parent::url($urlOptions, $name);
		
		$router = Zend_Controller_Front::getInstance()->getRouter();
		if ($router instanceof Zend_Controller_Router_Rewrite) {
			$route    = $router->getRoute($name);
			$defaults = $route->getDefaults();
			
			/**
			 * Add token to URL if the CSRF protection is enable
			 */
			if (isset($defaults['csrf']) 
				&& 'true' == (string)$defaults['csrf']['enable'] 
				&& 'get' == $defaults['csrf']['retrive'])
			{
				$csrfHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Csrf');
				return $url . '?' . $csrfHelper->getTokenName() . '=' . $csrfHelper->getToken();
			}
			
			/**
			 * Append the language to the beginning of URI
			 * if it is localizable route
			 * @since 2.0.8
			 */
			$config = Tomato_Config::getConfig();
			if (isset($defaults['localization']) 
				&& 'true' == (string)$defaults['localization']['enable']
				&& 'true' == $config->localization->enable) 
			{
				$lang = isset($urlOptions['language'])
						? $urlOptions['language']
						: Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
				
				$baseUrl   = $this->view->baseUrl();
				$serverUrl = $this->view->serverUrl();				
				$path      = substr($serverUrl . $url, strlen($baseUrl));				
//				$url  	   = rtrim($baseUrl, '/') . '/' . $lang . '/' . ltrim($path, '/');
				$newUrl    = rtrim(substr($baseUrl, strlen($serverUrl)), '/') . '/' . $lang . '/' . ltrim($path, '/');
				return $newUrl;
			}
		}
		
		return $url;
	}
}
