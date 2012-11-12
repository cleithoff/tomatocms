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
 * @version 	$Id: Item.php 4541 2010-08-12 09:58:41Z huuphuoc $
 * @since		2.0.7
 */

interface Menu_Models_Interface_Item
{
	/**
	 * Add menu item
	 * 
	 * @param Menu_Models_Menu_Item $item
	 */
	public function add($item);
	
	/**
	 * Remove all menu items that belong to given menu
	 * 
	 * @param int $menuId
	 * @return int
	 */
	public function delete($menuId);

	/**
	 * Get all menu items
	 * 
	 * @param int $menuId
	 * @return array
	 */
	public function getTree($menuId);
}
