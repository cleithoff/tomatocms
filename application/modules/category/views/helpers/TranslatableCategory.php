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
 * @version 	$Id: TranslatableCategory.php 4810 2010-08-24 03:36:37Z huuphuoc $
 * @since		2.0.8
 */

class Category_View_Helper_TranslatableCategory
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all categories
	 * which haven't been translated from the default language
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function translatableCategory($attributes = array(), $lang = null)
	{
		$defaultLang     = Tomato_Config::getConfig()->localization->languages->default;
		$elementDisabled = ($lang != null && $lang == $defaultLang) ? ' disabled="disabled"' : '';
		if (null == $lang) {
			$lang = $defaultLang;
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn);
		$categories = $categoryDao->getTranslatable($lang);
		
		$output = sprintf("<select name='%s' id='%s' viewHelperClass='%s' viewHelperAttributes='%s'%s>", 
							$attributes['name'], $attributes['id'], get_class($this), Zend_Json::encode($attributes), $elementDisabled) . self::EOL
				. '<option value=\'{"id": "", "language": ""}\'>---</option>' . self::EOL;
		
		foreach ($categories as $category) {
			$disabled = (0 == (int)$category->translatable 
							&& ((0 == (int)$attributes['disabled'] && $category->category_id != (int)$attributes['selected'])
								||
							((int)$attributes['disabled'] == (int)$attributes['selected'])))
						? ' disabled="disabled"' : '';
			$selected = ($elementDisabled == ''
							&& $disabled == ''
							&& (int)$category->category_id == (int)$attributes['selected'])
						? ' selected="selected"' : '';
			
			$output  .= sprintf("<option value='%s'%s%s>%s</option>", 
								Zend_Json::encode(array('id' => $category->category_id, 'language' => $defaultLang)), 
								$selected,
								$disabled,
								str_repeat('---', $category->depth) . $category->name) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}	
}
