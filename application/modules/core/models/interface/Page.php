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
 * @version 	$Id: Page.php 4534 2010-08-12 09:50:52Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Page
{
	/**
	 * List all pages ordered by pages' ordering number
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getOrdered();
	
	/**
	 * Re-update the order
	 * 
	 * @return int Maximum ordering
	 */
	public function reupdateOrder();
	
	/**
	 * Update the page order
	 * 
	 * @param int $pageId Id of page
	 * @param int $order New ordering number
	 * @return int
	 */
	public function updateOrder($pageId = null, $order);
	
	/**
	 * Get page by its route
	 * 
	 * @param string $routeName Name of route
	 * @return Core_Models_Page
	 */
	public function getByRoute($routeName);
	
	/**
	 * Add new page
	 * 
	 * @param Core_Models_Page $page
	 * @return int
	 */
	public function add($page);
	
	/**
	 * Delete page by Id
	 * 
	 * @param int $id Id of page
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Update page
	 * 
	 * @param Core_Models_Page $page
	 * @return int
	 */
	public function update($page);
}
