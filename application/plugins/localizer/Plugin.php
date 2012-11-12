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
 * @version 	$Id: Plugin.php 4825 2010-08-24 07:18:31Z huuphuoc $
 * @since		2.0.7
 */

class Plugins_Localizer_Plugin extends Tomato_Controller_Plugin 
{
	public function postDispatch(Zend_Controller_Request_Abstract $request) 
	{
		/**
		 * Localize jQuery UI datepicker component
		 */
		$config 	  = Tomato_Config::getConfig();
		$lang 		  = $config->web->lang;
		$staticServer = $config->web->url->static;
		
		$locale   = new Zend_Locale($lang);
		$language = $locale->getLanguage();
		
		$file = '/js/jquery.ui/i18n/ui.datepicker-' . $language . '.js';
		if (file_exists(TOMATO_ROOT_DIR . $file)
			&& Zend_Layout::getMvcInstance() != null
			&& 'admin' == Zend_Layout::getMvcInstance()->getLayout())
		{
			/**
			 * Get view instance
			 */
			$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
			$view = $viewRenderer->view;
			
			$view->headScript()->appendFile($staticServer . $file);
		}
	}
}
