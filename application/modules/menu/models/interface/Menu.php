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
 * @version 	$Id: Menu.php 4631 2010-08-15 08:11:25Z huuphuoc $
 * @since		2.0.5
 */

interface Menu_Models_Interface_Menu
{
	/**
	 * Get menu by given Id
	 * 
	 * @param int $id Id of menu
	 * @return Menu_Models_Menu
	 */
	public function getById($id);
	
	/**
	 * Add new menu
	 * 
	 * @param Menu_Models_Menu $menu
	 * @return int
	 */
	public function add($menu);
	
	/**
	 * Update menu
	 * 
	 * @param Menu_Models_Menu $menu
	 * @return int
	 */
	public function update($menu);
	
	/**
	 * Delete menu by Id
	 * 
	 * @param int $id Id of menu
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Get list of menus
	 * 
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getMenus($offset = null, $count = null);
	
	/**
	 * Count the number of menus
	 * 
	 * @return int
	 */
	public function count();
	
	/**
	 * Get translable items which haven't been translated of the default language
	 * 
	 * @since 2.0.8
	 * @param string $lang
	 * @return Tomato_Model_RecordSet
	 */
	public function getTranslatable($lang);
	
	/**
	 * Get translation item which was translated to given category
	 * 
	 * @since 2.0.8
	 * @param Menu_Models_Menu $menu
	 * @return Menu_Models_Menu
	 */
	public function getSource($menu);
}
