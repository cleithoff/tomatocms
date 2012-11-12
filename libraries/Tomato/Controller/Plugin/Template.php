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
 * @version 	$Id: Template.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Controller_Plugin_Template extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$config = Tomato_Config::getConfig();
		
		/** 
		 * Support template
		 */
		$template = (!Zend_Registry::isRegistered(Tomato_GlobalKey::APP_TEMPLATE) 
						|| Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE) == null 
						|| Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE) == '')
					? $config->web->template 
					: Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE);
		Zend_Registry::set(Tomato_GlobalKey::APP_TEMPLATE, $template);
		
		$module 	= $request->getModuleName();
		$controller = strtolower($request->getControllerName());
		$action 	= strtolower($request->getActionName());	
		
		/**
		 * Check if we are in modules or widgets folder
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		
		$file1 = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'views' . DS . 'scripts' . DS . $controller . DS . $action . '.phtml';
		$path  = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'views' . DS . $module;
		$file2 = $path . DS . 'scripts' . DS . $controller . DS . $action . '.phtml';

		/**
		 * TODO: Try to find the script in template first
		 */
		if (!file_exists($file1) && file_exists($file2)) {
//			$view->addScriptPath($path . DS . 'scripts' . DS);
			$view->setScriptPath($path . DS . 'scripts' . DS);
			
			/**
			 * Add helper path for template
			 */
			if (file_exists($path . DS . 'helpers')) {
				$view->addHelperPath($path . DS . 'helpers', $module . '_View_Helper_');
//				$view->setHelperPath($path . DS . 'helpers', $module . '_View_Helper_');
			}
		}
	}
}
