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
 * @version 	$Id: CategoryCheckbox.php 4569 2010-08-12 18:04:39Z huuphuoc $
 * @since		2.0.8
 */

class Category_View_Helper_CategoryCheckbox
{
	const EOL = "\n";
	
	public function categoryCheckbox($attributes = array(), $lang = null)
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
		
		$output = sprintf("<div id='%s' viewHelperClass='%s' viewHelperAttributes='%s'>", $attributes['id'], get_class($this), Zend_Json::encode($attributes)) . self::EOL;
		foreach ($categories as $category) {
			$selected = (isset($attributes['selected']) && in_array($category->category_id, $attributes['selected'])) ? ' checked="checked"' : ''; 
			$output .= '<div>' . str_repeat('-----', $category->depth). ' <input type="checkbox" name="' . $attributes['name'] .'" value="' . $category->category_id . '"' . $selected. ' />' . $category->name . '</div>' . self::EOL;
		}
		$output .= '</div>' . self::EOL;		
		
		return $output;
	}
}
