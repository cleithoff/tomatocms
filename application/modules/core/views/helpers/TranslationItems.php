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
 * @version 	$Id: TranslationItems.php 4808 2010-08-24 03:27:26Z huuphuoc $
 * @since		2.0.8
 */

class Core_View_Helper_TranslationItems
{
	/**
	 * Get all translation items of given item
	 * 
	 * @param Tomato_Model_Entity $item
	 * @return array
	 */
	public function translationItems($item)
	{
		$config = Tomato_Config::getConfig();
		if (!isset($config->localization->languages->list) || $config->localization->languages->list == $config->web->lang) {
			return array();
		}
		
		if (!is_object($item)) {
			throw new Exception('Source item has to be an object');
		}
		if (!($item instanceof Tomato_Model_Entity)) {
			throw new Exception('The item class (' . get_class($item) . ') has to extend from Tomato_Model_Entity');
		}
		
		/**
		 * Create DAO instance based on the item class
		 */
		$conn        = Tomato_Db_Connection::factory()->getMasterConnection();
		$array       = explode('_', get_class($item));
		$daoClass    = 'get' . array_pop($array) . 'Dao';
		$daoInstance = Tomato_Model_Dao_Factory::getInstance()->setModule($array[0])->$daoClass();
		$daoInstance->setDbConnection($conn)->setLang($config->localization->languages->default);
		
		$result = array();
		foreach (explode(',', $config->localization->languages->list) as $lang) {
			$result[$lang] = null;
		}
		
		$translations = $daoInstance->getTranslations($item);
		if ($translations != null) {
			foreach ($translations as $translation) {
				$result[$translation->language] = $translation;
			}
		}
		
		return $result;
	}
}
