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
 * @version 	$Id: CategorySelect.php 4569 2010-08-12 18:04:39Z huuphuoc $
 * @since		2.0.8
 */

class Category_View_Helper_CategorySelect
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all categories
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function categorySelect($attributes = array(), $lang = null)
	{
		if (null == $lang) {
			$lang = Zend_Controller_Front::getInstance()
							->getRequest()
							->getParam('lang', Tomato_Config::getConfig()->web->lang);
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$categoryDao = Tomato_Model_Dao_Factory::getInstance()->setModule('category')->getCategoryDao();
		$categoryDao->setDbConnection($conn)->setLang($lang);
		$categories = $categoryDao->getTree();
		
		$selectedId = isset($attributes['selected']) ? $attributes['selected'] : null;
		$disableId  = isset($attributes['disable']) ? $attributes['disable'] : null;
		
		$output = sprintf("<select name='%s' id='%s' viewHelperClass='%s' viewHelperAttributes='%s'>", $attributes['name'], $attributes['id'], get_class($this), Zend_Json::encode($attributes)) . self::EOL
				. '<option value="0">---</option>' . self::EOL;
		
		foreach ($categories as $category) {
			$selected = ($selectedId == null || $selectedId != $category->category_id) ? '' : ' selected="selected"';
			$disable  = ($disableId == null || $disableId != $category->category_id) ? '' : ' disabled';
			$output  .= sprintf('<option value="%s"%s%s>%s</option>', $category->category_id, $selected, $disable, str_repeat('---', $category->depth) . $category->name) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}
}
