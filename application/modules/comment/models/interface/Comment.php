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
 * @version 	$Id: Comment.php 4530 2010-08-12 09:44:52Z huuphuoc $
 * @since		2.0.5
 */

interface Comment_Models_Interface_Comment
{
	/**
	 * Get comment by given Id
	 * 
	 * @param int $id Id of comment
	 * @return Comment_Models_Comment
	 */
	public function getById($id);
	
	/**
	 * Add new comment
	 * 
	 * @param Comment_Models_Comment $comment
	 * @return int
	 */
	public function add($comment);
	
	/**
	 * Update comment
	 * 
	 * @param Comment_Models_Comment $comment
	 * @return int
	 */
	public function update($comment);
	
	/**
	 * Update order, depth and path for comment and all comments in same thread
	 * 
	 * @param Comment_Models_Comment $comment
	 * @return int
	 */
	public function reupdateOrderInThread($comment);
	
	/**
	 * Delete comment by Id
	 * 
	 * @param int $id Id of comment
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Toggle activated status of comment
	 * 
	 * @param Comment_Models_Comment $comment
	 * @return int
	 */
	public function toggleActive($comment);
	
	/**
	 * Get latest comments
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param bool $isActive TRUE if you want to get activated comments only
	 * @return Tomato_Model_RecordSet
	 */
	public function getLatest($offset, $count, $isActive = null);
	
	/**
	 * Get latest comments groupped by thread
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getLatestByThread();
	
	/**
	 * Count the number of threaded comments
	 * 
	 * @return int
	 */
	public function countThreads();
	
	/**
	 * Get latest comments in same thread
	 *
	 * @param int $offset
	 * @param int $count
	 * @param string $url Thread URL
	 * @param bool $isActive TRUE if you want to get activated comments only
	 * @return Tomato_Model_RecordSet
	 */
	public function getThreadComments($offset, $count, $url, $isActive = null);
	
	/**
	 * Count number of comments in thread
	 * 
	 * @param string $url Thread URL
	 * @param bool $isActive TRUE if you want to get activated comments only
	 * @return int
	 */
	public function countThreadComments($url, $isActive = null);
}
