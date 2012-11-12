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
 * @version 	$Id: TranslatableMenu.php 4810 2010-08-24 03:36:37Z huuphuoc $
 * @since		2.0.8
 */

class Menu_View_Helper_TranslatableMenu
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all menus
	 * which haven't been translated from the default language
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function translatableMenu($attributes = array(), $lang = null)
	{
		$defaultLang = Tomato_Config::getConfig()->localization->languages->default;
		if (null == $lang) {
			$lang = $defaultLang;
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$menuDao = Tomato_Model_Dao_Factory::getInstance()->setModule('menu')->getMenuDao();
		$menuDao->setDbConnection($conn);
		$menus = $menuDao->getTranslatable($lang);
		
		$output = sprintf("<select name='%s' id='%s' viewHelperClass='%s' viewHelperAttributes='%s'>", 
							$attributes['name'], $attributes['id'], get_class($this), Zend_Json::encode($attributes)) . self::EOL
				. '<option value=\'{"id": "", "language": ""}\'>---</option>' . self::EOL;
		
		foreach ($menus as $menu) {
			$disabled = (0 == (int)$menu->translatable
							&& ((0 == (int)$attributes['disabled'] && $menu->menu_id != (int)$attributes['selected'])
								||
							((int)$attributes['disabled'] == (int)$attributes['selected'])))
						? ' disabled="disabled"' : '';
			$selected = ($disabled == ''
							&& (int)$menu->menu_id == (int)$attributes['selected'])
						? ' selected="selected"' : '';
			
			$output  .= sprintf("<option value='%s'%s%s>%s</option>", 
								Zend_Json::encode(array('id' => $menu->menu_id, 'language' => $defaultLang)), 
								$selected,
								$disabled,
								$menu->name) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}	
}
