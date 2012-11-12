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
 * @version 	$Id: File.php 4545 2010-08-12 10:01:37Z huuphuoc $
 * @since		2.0.5
 */

interface Multimedia_Models_Interface_File
{
	/**
	 * Get file by given Id
	 * 
	 * @param int $id Id of file
	 * @return Multimedia_Models_File
	 */
	public function getById($id);

	/**
	 * Add new file
	 * 
	 * @param Multimedia_Models_File $file
	 * @return int
	 */
	public function add($file);
	
	/**
	 * Search for files by array of conditions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * - file_id
	 * - created_user
	 * - file_type
	 * - photo
	 * - clip
	 * - keyword
	 * - is_active
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset, $count, $exp = null);
	
	/**
	 * Count the number of files which satisfy searching conditions
	 * 
	 * @param array $exp Search expression (@see find)
	 * @return int
	 */
	public function count($exp = null);
	
	/**
	 * Delete file by Id
	 * 
	 * @param int $id Id of file
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Update file title/description
	 * 
	 * @param int $fileId Id of file
	 * @param string $title New title of file
	 * @param string $description New description of file
	 * @return int
	 */
	public function updateDescription($fileId, $title, $description = null);
	
	/**
	 * Toggle activated status of file
	 * 
	 * @param int $id Id of file
	 * @return int
	 */
	public function toggleStatus($id);
	
	/**
	 * Update file
	 * 
	 * @param Multimedia_Models_File $file
	 * @return int
	 */
	public function update($file);

	/**
	 * Get files from given set
	 * 
	 * @param int $setId Id of set
	 * @param int $offset
	 * @param int $count
	 * @param bool $isActive
	 * @return Tomato_Model_RecordSet
	 */
	public function getFilesInSet($setId, $offset = null, $count = null, $isActive = null);
	
	/**
	 * Count the number of files belonging to given set
	 * 
	 * @param int $setId Id of set
	 * @param bool $isActive
	 * @return int
	 */
	public function countFilesInSet($setId, $isActive = null);
	
	/**
	 * Remove file or all file from given set
	 * 
	 * @param int $setId Id of set
	 * @param int $fileId Id of file
	 */
	public function removeFromSet($setId, $fileId = null);
	
	/**
	 * Add file to set
	 * 
	 * @param int $setId Id of set
	 * @param int $fileId Id of file
	 */
	public function addToSet($setId, $fileId);
	
	/**
	 * Get list of files tagged by given tag
	 * 
	 * @param int $tagId Id of tag
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getByTag($tagId, $offset, $count);
	
	/**
	 * Get number of files tagged by given tag
	 * 
	 * @param int $tagId Id of tag
	 * @return int
	 */
	public function countByTag($tagId);
}
