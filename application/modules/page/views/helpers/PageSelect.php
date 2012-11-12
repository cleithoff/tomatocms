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
 * @version 	$Id: PageSelect.php 4908 2010-08-24 21:05:47Z huuphuoc $
 * @since		2.0.8
 */

class Page_View_Helper_PageSelect
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all pages
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function pageSelect($attributes = array(), $lang = null)
	{
		if (null == $lang) {
			$lang = Zend_Controller_Front::getInstance()
							->getRequest()
							->getParam('lang', Tomato_Config::getConfig()->web->lang);
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn)->setLang($lang);
		$pages = $pageDao->getTree();
		
		$selectedId = isset($attributes['selected']) ? $attributes['selected'] : null;
		$disableId  = isset($attributes['disable']) ? $attributes['disable'] : null;
		
		$output = sprintf("<select name='%s' id='%s' viewHelperClass='%s' viewHelperAttributes='%s'>", $attributes['name'], $attributes['id'], get_class($this), Zend_Json::encode($attributes)) . self::EOL
				. '<option value="0">---</option>' . self::EOL;
		
		foreach ($pages as $page) {
			$selected = ($selectedId == null || $selectedId != $page->page_id) ? '' : ' selected="selected"';
			$disable  = ($disableId == null || $disableId != $page->page_id) ? '' : ' disabled';
			$output  .= sprintf('<option value="%s"%s%s>%s</option>', $page->page_id, $selected, $disable, str_repeat('---', $page->depth) . $page->name) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}
}
