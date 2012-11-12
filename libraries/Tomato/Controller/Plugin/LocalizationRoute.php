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
 * @version 	$Id: LocalizationRoute.php 4807 2010-08-24 03:27:18Z huuphuoc $
 * @since		2.0.8
 */

class Tomato_Controller_Plugin_LocalizationRoute extends Zend_Controller_Plugin_Abstract 
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		$currUri  = $request->getRequestUri();
		$currUri  = rtrim($currUri, '/');
		$config   = Tomato_Config::getConfig();
		$currLang = $config->localization->languages->default;
		
		/**
		 * We are in the front-end section
		 */
		if (strpos(strtolower($currUri) . '/', '/admin/') === false) {
			$paths    = explode('/', ltrim($request->getPathInfo(), '/'));
			$currLang = array_shift($paths);
		} 
		/**
		 * We are in the back-end section
		 */
		else {
			$paths    = explode('/', rtrim($request->getPathInfo(), '/'));
			$currLang = array_pop($paths);
		}
		
		/**
		 * Add language parameter.
		 * Set the request URI if there is language in URI
		 */
		if (in_array($currLang, explode(',', (string) $config->localization->languages->list))) {
			$request->setParam('lang', $currLang);
			$path = implode('/', $paths);
			if ('' == $path) {
				$path = '/';
			}
			$request->setPathInfo($path);
		} else {
			$request->setParam('lang', $config->localization->languages->default);
		}
	}
}
