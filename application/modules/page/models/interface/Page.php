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
 * @version 	$Id: Page.php 4685 2010-08-16 08:54:12Z huuphuoc $
 * @since		2.0.7
 */

interface Page_Models_Interface_Page
{
	/**
	 * Get page by given Id
	 * 
	 * @param int $id Id of page
	 * @return Page_Models_Page
	 */
	public function getById($id);
	
	/**
	 * Add new page
	 * 
	 * @param Page_Models_Page $page
	 * @return int
	 */
	public function add($page);
	
	/**
	 * Update page
	 * 
	 * @param Page_Models_Page $page
	 * @return void
	 */
	public function update($page);
	
	/**
	 * Update page order
	 * 
	 * @param Page_Models_Page $page
	 * @return int
	 */
	public function updateOrder($page);
	
	/**
	 * Delete page
	 * 
	 * @param Page_Models_Page $page
	 * @return void
	 */
	public function delete($page);
	
	/**
	 * Build pages tree with depth for each item
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getTree();
	
	/**
	 * Get parent pages
	 * 
	 * @param int $pageId Id of page
	 * @return Tomato_Model_RecordSet
	 */
	public function getParents($pageId);
	
	/**
	 * Get translable items which haven't been translated of the default language
	 * 
	 * @since 2.0.8
	 * @param string $lang
	 * @return Tomato_Model_RecordSet
	 */
	public function getTranslatable($lang);
	
	/**
	 * Get translation item which was translated to given page
	 * 
	 * @since 2.0.8
	 * @param Page_Models_Page $page
	 * @return Page_Models_Page
	 */
	public function getSource($page);	
}
