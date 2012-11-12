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
 * @version 	$Id: Set.php 5103 2010-08-29 20:54:33Z huuphuoc $
 * @since		2.0.5
 */

interface Multimedia_Models_Interface_Set
{
	/**
	 * Get set by given Id
	 * 
	 * @param int $id Id of set
	 * @return Multimedia_Models_Set
	 */
	public function getById($id);

	/**
	 * Add new set
	 * 
	 * @param Multimedia_Models_Set $set
	 * @return int
	 */
	public function add($set);
	
	/**
	 * Update set
	 * 
	 * @param Multimedia_Models_Set $set
	 * @return int
	 */
	public function update($set);
	
	/**
	 * Search for sets by array of conditions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Searching conditions. An array contain various conditions, keys including:
	 * - created_user_id
	 * - keyword
	 * - is_active
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset = null, $count = null, $exp = null);
	
	/**
	 * Count the number of sets by searching conditions
	 * 
	 * @param array $exp Searching conditions (@see find)
	 * @return int
	 */
	public function count($exp = null);
	
	/**
	 * Delete set by given Id
	 * 
	 * @param int $id Id of set
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Update set title/description
	 * 
	 * @param int $setId Id of set
	 * @param string $title New set title
	 * @param string $description New set description
	 * @return int
	 */
	public function updateDescription($setId, $title, $description = null);
	
	/**
	 * Toggle set status
	 * 
	 * @param int $id Id of set
	 * @return int
	 */
	public function toggleStatus($id);
	
	/**
	 * Get list of sets tagged by given tag
	 * 
	 * @param int $tagId Id of tag
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getByTag($tagId, $offset, $count);
	
	/**
	 * Get number of sets tagged by given tag
	 * 
	 * @param int $tagId Id of tag
	 * @return int
	 */
	public function countByTag($tagId);
}
