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
 * @version 	$Id: Translator.php 5304 2010-09-03 10:39:14Z huuphuoc $
 * @since		2.0.3
 */

class Tomato_View_Helper_Translator extends Zend_View_Helper_Abstract
{
	public function __construct()
	{
	}
	
	/**
	 * @param string $key Key to translate
	 * @param string $module Name of module. If this is not specified, 
	 * it will take the current module
	 * @return string
	 */
	public function translator($key = null, $module = null)
	{
		if (null == $key && null == $module) {
			return $this;
		}
		
		if (null == $module) {
			/**
			 * Get current module
			 */
			$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		}
		
		/**
		 * Get current language
		 */
		$lang = self::_getLang();
		
		/**
		 * If we want to use the file in languages directory
		 * $file = TOMATO_ROOT_DIR . DS . 'languages' . DS . $lang . DS . 'application' . DS . 'modules' . DS . $module . DS . 'languages' . DS . 'lang.' . $lang . '.ini';		 
		 */
		
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'languages' . DS . 'lang.' . $lang . '.ini';
		if (file_exists($file) && file_get_contents($file) != '') {
			$translate = new Zend_Translate('Ini', $file, $lang);
			return $translate->_($key);
		} 
		return $key;
	}
	
	/**
	 * @param string $key Key to translate
	 * @return string
	 */
	public function widget($key)
	{
		/**
		 * Get current widget instance which have been set in
		 * __call() of Tomato_Widget
		 */
		if (!Zend_Registry::isRegistered(Tomato_Widget::CURRENT_WIDGET_KEY)) {
			return $key;
		}
		$widget = Zend_Registry::get(Tomato_Widget::CURRENT_WIDGET_KEY);
		if (null == $widget || !($widget instanceof Tomato_Widget)) {
			return $key;
		}
		
		$lang = self::_getLang();
		
		/**
		 * If we want to use the file in languages directory
		 * $file = TOMATO_ROOT_DIR . DS . 'languages' . DS . $lang . DS . 'application' . DS . 'modules' . DS . strtolower($widget->getModule()) . DS . 'widgets' . DS . strtolower($widget->getName()) . DS . 'lang.' . $lang . '.ini';
		 */
		
	 	$file = TOMATO_APP_DIR . DS . 'modules' . DS . strtolower($widget->getModule()) . DS  . 'widgets' . DS . strtolower($widget->getName()) . DS . 'lang.' . $lang . '.ini';
		if (file_exists($file) && file_get_contents($file) != '') {
            $translate = new Zend_Translate('Ini', $file, $lang);
			return $translate->_($key);
		}
		return $key;
	}
	
	private static function _getLang()
	{
		$config = Tomato_Config::getConfig();
		$lang   = $config->web->language->code;
		
		/**
		 * In the front-end section, loads the language based on the route URL
		 * @since 2.0.8
		 */
		if ('false' == $config->localization->enable
			|| (Zend_Layout::getMvcInstance() != null && 'admin' == Zend_Layout::getMvcInstance()->getLayout())) {
			return $lang;
		} else {
			return Zend_Controller_Front::getInstance()->getRequest()->getParam('lang', $lang);
		}
	}
}
