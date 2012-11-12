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
 * @version 	$Id: Note.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.5
 */

interface Multimedia_Models_Interface_Note
{
	/**
	 * Add new note
	 * 
	 * @param Multimedia_Models_Note $note
	 * @return int
	 */
	public function add($note);

	/**
	 * Delete note by given id
	 * 
	 * @param int $id Id of note
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Update note content and position
	 * 
	 * @param Multimedia_Models_Note $note
	 * @return int
	 */
	public function update($note);
	
	/**
	 * Find notes
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Searching conditions includes keys:
	 * - file_id
	 * - is_active
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset = null, $count = null, $exp = null);
	
	/**
	 * Count number of files by searching conditions
	 * 
	 * @param array $exp Searching conditions (@see find)
	 */
	public function count($exp = null);
	
	/**
	 * Update file status
	 * 
	 * @param int $id Id of file
	 * @param string $status New status
	 * @return int
	 */
	public function updateStatus($id, $status);
}
