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
 * @version 	$Id: Widget.php 5407 2010-09-13 07:14:35Z leha $
 * @since		2.0.8
 */

class Core_Widgets_Localizer_Widget extends Tomato_Widget 
{
	protected function _prepareShow()
	{
		$router    = Zend_Controller_Front::getInstance()->getRouter();
		$routeName = $router->getCurrentRouteName();
		$currRoute = $router->getCurrentRoute();
		$defaults  = $currRoute->getDefaults();
		
		$links     = array();
		if (isset($defaults['localization']['enable']) 
			&& ('true' == $defaults['localization']['enable'])
			&& isset($defaults['localization']['identifier']['class']))
		{
			$class  = $defaults['localization']['identifier']['class'];
			$name   = $defaults['localization']['identifier']['param'];
			
			/**
			 * The DAO method used to get the model instance
			 */
			$method = isset($defaults['localization']['identifier']['method'])
						? $defaults['localization']['identifier']['method']
						: 'getById';
			
			$id     = Zend_Controller_Front::getInstance()->getRequest()->getParam($name);
			
			$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$items = $translationDao->getItems($id, $class);
			
			$array       = explode('_', $class);
			$daoClass    = 'get' . array_pop($array) . 'Dao';
			$daoInstance = Tomato_Model_Dao_Factory::getInstance()->setModule($array[0])->$daoClass();
			$daoInstance->setDbConnection($conn);
			
			if ($items != null) {
				$config    = Tomato_Config::getConfig()->toArray();
				$languages = $config['localization']['languages']['details']; 
				
				foreach ($items as $item) {
					if ($item->item_id == $id) {
						continue;
					}
					$object = $daoInstance->$method($item->item_id);
					if ($object != null && get_class($object) == $class) {
						$properties = $object->getProperties();
						$properties['language'] = $item->language;
						
						$language = $item->language;
						if (isset($languages[$item->language])) {
							$info     = explode('|', $languages[$item->language]);
							$language = $info[1];
						}
						
						$links[] = array(
							'url'   => $this->_view->url($properties, $routeName),
							'label' => $language,
						);
					}
				}
			}
		}
		
		$this->_view->assign('links', $links);
	}
}
