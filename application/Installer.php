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
 * @version 	$Id: Installer.php 4826 2010-08-24 07:32:20Z huuphuoc $
 * @since 		2.0.1
 */

class Installer extends Zend_Application_Bootstrap_Bootstrap 
{
	protected function _initAutoload()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$modules    = Tomato_Module_Loader::getInstance()->getModuleNames();
		foreach ($modules as $module) {
			new Tomato_Autoloader(array(
    			'basePath'  => TOMATO_APP_DIR . DS . 'modules' . DS . $module,
    			'namespace' => ucfirst($module) . '_',
			));
		}
		
		return $autoloader;
	}

	protected function _initInstallChecker()
	{
		/**
		 * Check whether the app is installed or not
		 */
		$config = Tomato_Config::getConfig();
		if ($config->install && $config->install->date) {
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			if (null === $viewRenderer->view) {
				$viewRenderer->initView();
			}
			$view = $viewRenderer->view;
			//die($view->translator('install_already_installed', 'core'));
			
			/**
			 * Redirect to the homepage
			 */
			header('Location: ' . $config->web->url->base);
		}
	}
	
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
		/** 
		 * Load routes
		 */
		$router = $front->getRouter();
		
		/**
		 * Don't use default route
		 */
		$router->removeDefaultRoutes();
		
		/**
		 * Add installer routes
		 */
		$router->addRoute(
			'core_install_language',
    		new Zend_Controller_Router_Route('/', array(
				'module' 	 => 'core', 
				'controller' => 'Install', 
				'action' 	 => 'index',
			))
		);
		$router->addRoute(
			'core_install_requirement',
    		new Zend_Controller_Router_Route('/requirement/', array(
				'module' 	 => 'core', 
				'controller' => 'Install', 
				'action' 	 => 'requirement',
			))
		);
		$router->addRoute(
			'core_install_config',
    		new Zend_Controller_Router_Route('/config/', array(
				'module' 	 => 'core', 
				'controller' => 'Install', 
				'action' 	 => 'config',
			))
		);
		$router->addRoute(
			'core_install_complete',
    		new Zend_Controller_Router_Route('/complete/', array(
				'module' 	 => 'core', 
				'controller' => 'Install', 
				'action' 	 => 'complete',
			))
		);
	}

	protected function _initView()
	{
		/** 
		 * Init view 
		 */
		$config = Tomato_Config::getConfig();
		date_default_timezone_set($config->web->datetime->timezone);
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		$view->doctype('XHTML1_STRICT');
		$view->assign('APP_SKIN', 'default');
		$view->assign('APP_TEMPLATE', 'default');
		$view->assign('SITE_NAME', $config->web->name);
		
		$view->addHelperPath(TOMATO_LIB_DIR . DS . 'Tomato' . DS . 'View' . DS . 'Helper', 'Tomato_View_Helper');
		$view->addHelperPath(TOMATO_APP_DIR . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'helpers', 'Core_View_Helper');
		
		/** 
		 * Build root URL 
		 */
		$request  = new Zend_Controller_Request_Http();
		$siteUrl  = $request->getScheme() . '://' . $request->getHttpHost();
		$basePath = $request->getBasePath();
		$siteUrl  = ($basePath == '') ? $siteUrl : $siteUrl . '/' . ltrim($basePath, '/');
		$view->assign('APP_URL', $siteUrl);
		$view->assign('APP_STATIC_SERVER', $siteUrl);
		$view->getHelper('BaseUrl')->setBaseUrl(rtrim($siteUrl, '/') . '/install.php');
		
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
		 * Set layout path and default layout
		 */
		Zend_Layout::startMvc(array('layoutPath' => TOMATO_APP_DIR . DS . 'templates' . DS . 'admin' . DS . 'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('install');
		
		/** 
		 * Cache language if user used caching system
		 */
		$cache = Tomato_Cache::getInstance();
		if ($cache) {
			 Zend_Translate::setCache($cache);
		}
	}
	
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
        /** 
		 * Error handler 
		 */
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
								    'module'     => 'core',
								    'controller' => 'message',
								    'action'     => 'error',
								)));
	}
}
