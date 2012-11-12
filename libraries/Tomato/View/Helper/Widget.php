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
 * @version 	$Id: Widget.php 4637 2010-08-15 09:53:41Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_View_Helper_Widget extends Zend_View_Helper_Abstract 
{
	private $_translationDao;
	
	public function __construct()
	{
		$this->_translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
	}
	
	public function widget($module, $name, array $params = array()) 
	{
		$module  = strtolower($module);
		$name 	 = strtolower($name);
		$timeout = isset($params[Tomato_Widget::PARAM_CACHE_LIFETIME]) 
					? $params[Tomato_Widget::PARAM_CACHE_LIFETIME] 
					: null;
					
		$cache 		 = Tomato_Cache::getInstance();
		$widgetClass = ucfirst($module) . '_Widgets_' . ucfirst($name) . '_Widget';
		
		if (!class_exists($widgetClass)) {
			/**
			 * TODO: Should we inform to user that the widget does not exist
			 */
			return '';
		}
		
		/**
		 * Add language parameter
		 * We can get the language in widget by calling:
		 * $lang = $this->_request->getParam('lang');
		 * @since 2.0.8 
		 */
		$params['lang'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
		
		/**
		 * Determine the value of param if it is used as localize param
		 * @since 2.0.8 
		 */
		$aboutFile = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $name . DS . 'about.xml';
		if (file_exists($aboutFile)) {
			$info         = simplexml_load_file($aboutFile);
			$localization = $info->localization['enable'];
			
			if ($localization != null && 'true' == (string)$localization) {
				$idClass = (string) $info->localization->identifier['class'];
				$idParam = (string) $info->localization->identifier['param'];
				
				$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
				$this->_translationDao->setDbConnection($conn);
				$items = $this->_translationDao->getItems($params[$idParam], $idClass, $params['lang']);
				
				if ($items != null && 1 == count($items)) {
					$params[$idParam] = $items[0]->item_id;
				}
			}
		}
		
		if ($cache && $timeout != null) {
			/**
			 * The cache key ensure we will get the same cached value
			 * if the widget has been cached on other pages
			 */
			$cacheKey = $widgetClass . '_' . md5($module . '_' . $name . '_' . serialize($params));
			$cache->setLifetime($timeout);
			
			if (!($fromCache = $cache->load($cacheKey))) {
				$widget  = new $widgetClass($module, $name);
				$content = $widget->show($params);
				$cache->save($content, $cacheKey, array($module . '_Widgets'));
				return $content;
			} else {
				return $fromCache;
			}
		} else {
			$widget = new $widgetClass($module, $name);
			return $widget->show($params);
		}
	}
}
