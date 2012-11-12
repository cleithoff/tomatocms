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
 * @version 	$Id: Tag.php 4559 2010-08-12 10:43:55Z huuphuoc $
 * @since		2.0.5
 */

interface Tag_Models_Interface_Tag
{
	/**
	 * Get tag by given id
	 * 
	 * @param int $id Id of tag
	 * @return Tag_Models_Tag
	 */
	public function getById($id);
	
	/**
	 * Check whether a tag exists or not
	 * 
	 * @param string $text
	 * @return boolean TRUE if tag exist
	 */
	public function exist($text);
	
	/**
	 * Create new tag
	 * 
	 * @param Tag_Models_Tag $tag
	 * @return int Id of tag that have been added
	 */
	public function add($tag);
	
	/**
	 * Delete tag by its id
	 * 
	 * @param int $tagId Id of tag
	 * @return int
	 */
	public function delete($tagId);
	
	/**
	 * Search tags by keyword
	 * 
	 * @param string $keyword
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet array of tags
	 */
	public function find($keyword, $offset, $count);
	
	/**
	 * Count number of tags which like given keyword
	 * 
	 * @param string $keyword
	 * @return int
	 */
	public function count($keyword);
	
	/**
	 * List of tags tagged for given item
	 * 
	 * @param Tag_Models_TagItem $item
	 * @return Tomato_Model_RecordSet
	 */
	public function getByItem($item);
	
	/**
	 * List of tags when user view item
	 * TODO: Choose other name
	 * 
	 * @param Tag_Models_TagItem $item
	 * @param int $limit
	 * @return Tomato_Model_RecordSet
	 */
	public function getByRoute($item, $limit = null);
}
