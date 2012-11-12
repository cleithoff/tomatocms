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
 * @version 	$Id: ModuleTask.php 3423 2010-07-05 15:55:03Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Show all module tasks in administrator's top menu 
 */
class Core_View_Helper_ModuleTask extends Zend_View_Helper_Abstract 
{
	/**
	 * Get list of modules including its administrator tasks
	 * 
	 * @return array
	 */
	public function moduleTask()
	{
		$modules = array();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$moduleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getModuleDao();
		$moduleDao->setDbConnection($conn);
		$installedModules = $moduleDao->getModules();
		
		if ($installedModules != null) {
			foreach ($installedModules as $module) {
				if ($module->name == 'core') {
					continue;
				}
				$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module->name . DS . 'config' . DS . 'about.xml';
				if (!file_exists($file)) {
					continue;
				}
				$xml 	 = simplexml_load_file($file);
				$attrs 	 = $xml->description->attributes();
				$langKey = (string)$attrs['langKey'];
				$desc 	 = $this->view->translator($langKey, $module->name);
				$item 	 = array(
					'name' 		  => strtolower($xml->name),
					'description' => ($desc == $langKey) ? (string) $xml->description : $desc, 
					'tasks' 	  => array(),
				);
				if ($xml->admin) {
					foreach ($xml->admin->task as $task) {
						$attrs 	 = $task->attributes();
						$langKey = (string)$attrs['langKey'];
						$desc 	 = $this->view->translator($langKey, $module->name);
						$label 	 = ($desc == $langKey) ? (string) $attrs['description'] : $desc;
						
						/**
						 * Link to perform task defined by route's URL
						 * @since 2.0.5
						 */
						if (isset($attrs['route'])) {
							$route = (string)$attrs['route'];
							$link  = $this->view->url(array(), $route);
							
							/**
							 * Only show the link if user have permission
							 */
							if ($this->view->routeAccessor($route)) {
								$item['tasks'][] = array(
									'label' => $label,
									'link' 	=> $link,
								);
							}
						}
					}
				}
				
				/**
				 * Only show module if it has at least one task
				 */
				if (count($item['tasks']) > 0) {
					$modules[] = $item;
				}
			}
		}

		return $modules;
	}
}
