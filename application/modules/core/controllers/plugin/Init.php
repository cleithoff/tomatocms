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
 * @version 	$Id: Init.php 5023 2010-08-28 09:36:48Z huuphuoc $
 * @since		2.0.0
 */

class Core_Controllers_Plugin_Init extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$config = Tomato_Config::getConfig();
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		$view->doctype('XHTML1_STRICT');
		$view->addHelperPath(TOMATO_LIB_DIR . DS . 'Tomato' . DS . 'View' . DS . 'Helper', 'Tomato_View_Helper');
		$view->addHelperPath(TOMATO_APP_DIR . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'helpers', 'Core_View_Helper');
		
		/**
		 * Set base URL
		 */
		$view->getHelper('BaseUrl')->setBaseUrl($config->web->url->base);
		
		/**
		 * Append meta tags
		 */
		$view->headMeta()->appendName('description', $config->web->meta->description);
		$view->headMeta()->appendName('keywords', $config->web->meta->keyword);

		/** 
		 * Set timezone
		 */
		date_default_timezone_set($config->web->datetime->timezone);
		
		/**
		 * Set theme for site
		 * User can change skin at real time
		 * Check whether user set skin cookie or not
		 */
		$skin = (isset($_COOKIE['APP_SKIN'])) ? $_COOKIE['APP_SKIN'] : $config->web->skin; 
		$view->assign('APP_SKIN', $skin);
		
		$template = (!Zend_Registry::isRegistered(Tomato_GlobalKey::APP_TEMPLATE) 
						|| Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE) == null 
						|| Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE) == '')
				? $config->web->template : Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE);
		Zend_Registry::set(Tomato_GlobalKey::APP_TEMPLATE, $template);
		$view->assign('APP_TEMPLATE', $template);
		
		$view->assign('APP_URL', $config->web->url->base);
		$view->assign('APP_STATIC_SERVER', $config->web->url->static);
		$view->assign('SITE_NAME', $config->web->name);
		
		/**
		 * Get charset from configuration file
		 * @since 2.0.6
		 */
		$charset = $config->web->charset;
		if (null == $charset) {
			$charset = 'utf-8';
		}
		$view->assign('CHARSET', $charset);
		
		/**
		 * Support RTL language
		 * @since 2.0.3
		 */ 
		$langDir = isset($config->web->language->direction) ? $config->web->language->direction : 'ltr';
		$view->assign('APP_LANG_RTL', ($langDir == 'rtl'));
		
		/**
		 * @since 2.0.8
		 */
		$view->assign('APP_DEFAULT_LANG', $config->localization->languages->default);

		/**
		 * Set layout
		 */
		Zend_Layout::startMvc(array('layoutPath' => TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('default');
		
		/** 
		 * Cache language data if user configured to use cache
		 */
		$cache = Tomato_Cache::getInstance();
		if ($cache) {
			 Zend_Translate::setCache($cache);
		}
	}
}
