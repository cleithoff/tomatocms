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
 * @version 	$Id: Revision.php 5340 2010-09-07 08:50:11Z huuphuoc $
 * @since		2.0.5
 */

interface News_Models_Interface_Revision
{
	/**
	 * Get article revision by Id
	 * 
	 * @param int $id Id of revision
	 * @return News_Models_Revision
	 */
	public function getById($id);

	/**
	 * Add new article
	 * 
	 * @param News_Models_Revision $revision
	 * @return int
	 */
	public function add($revision);
	
	/**
	 * List article revisions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Searching conditions. An array contain various conditions, keys including:
	 * - article_id
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset, $count, $exp = null);
	
	/**
	 * Count number of article revisions by searching conditions
	 * 
	 * @param array $exp Searching conditions (@see find)
	 * @return int
	 */
	public function count($exp = null);
	
	/**
	 * Delete revision by Id
	 * 
	 * @param int $id Id of revision
	 * @return int
	 */
	public function delete($id);
}
