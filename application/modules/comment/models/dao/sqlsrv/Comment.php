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
 * @version 	$Id: Comment.php 5027 2010-08-28 16:33:48Z huuphuoc $
 * @since		2.0.5
 */

class Comment_Models_Dao_Sqlsrv_Comment extends Tomato_Model_Dao
	implements Comment_Models_Interface_Comment
{
	public function convert($entity) 
	{
		return new Comment_Models_Comment($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'comment 
				WHERE comment_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Comment_Models_Comment($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($comment) 
	{
		$this->_conn->insert($this->_prefix . 'comment', array(
			'title'			=> $comment->title,
			'content'		=> $comment->content,
			'is_active'		=> $comment->is_active,
			'email'			=> $comment->email,
			'ip'			=> $comment->ip,
			'full_name'		=> $comment->full_name,
			'web_site'		=> $comment->web_site,
			'created_date'	=> $comment->created_date,
			'reply_to'		=> $comment->reply_to,
			'depth'			=> $comment->depth,
			'path'			=> $comment->path,
			'ordering'		=> $comment->ordering,
			'page_url'		=> $comment->page_url,
			'activate_date' => $comment->activate_date,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'comment');				
	}
	
	public function update($comment) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'comment
				SET title = ?, content = ?, is_active = ?, 
					email = ?, ip = ?, full_name = ?, web_site = ?, activate_date = ?
				WHERE comment_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$comment->title,
			$comment->content,
			$comment->is_active,
			$comment->email,
			$comment->ip,
			$comment->full_name,
			$comment->web_site,
			$comment->activate_date,
			$comment->comment_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function reupdateOrderInThread($comment)
	{
		$sql  = 'SELECT TOP 1 MAX(c.ordering) AS max_ordering 
				FROM ' . $this->_prefix . 'comment AS c
				WHERE c.page_url = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($comment->page_url));
		$row = $stmt->fetch();
		$ordering = $row->max_ordering;
		$stmt->closeCursor();	
		$depth = 0;
		$path  = $comment->comment_id . '-';
		if ($comment->reply_to) {
			$replyTo = $this->getById($comment->reply_to); 
			if ($replyTo != null) {
				$sql  = "SELECT TOP 1 MAX(c.ordering) AS max_ordering 
						FROM " . $this->_prefix . "comment AS c
						WHERE c.path LIKE '%?%'";
				$stmt = $this->_conn->prepare($sql);
				$stmt->execute(array($replyTo->path));
				$row = $stmt->fetch();
				if (null == $row) {
					$ordering = $replyTo->ordering;
				} else {
					$ordering = $row->max_ordering;
				}
				$stmt->closeCursor();
				
				$path  = $replyTo->path.$path;
				$depth = $replyTo->depth + 1;
				$sql   = 'UPDATE ' . $this->_prefix . 'comment 
						SET ordering = ordering + 1
						WHERE page_url = ? AND ordering > ?';
				$stmt  = $this->_conn->prepare($sql);
				$stmt->execute(array($comment->page_url, $ordering));
				$stmt->closeCursor();
			}
		}		
		
		$sql  = 'UPDATE ' . $this->_prefix . 'comment
				SET ordering = ?, depth = ?, path = ?
				WHERE comment_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($ordering + 1, $depth, $path, $comment->comment_id));
		$row = $stmt->rowCount();
		$stmt->closeCursor();
		return $row;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'comment WHERE comment_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function toggleActive($comment)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'comment
				SET is_active = 1 - is_active, activate_date = ? 
				WHERE comment_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($comment->activate_date, $comment->comment_id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getLatest($offset, $count, $isActive = null)
	{
		$sql = 'SELECT c.* FROM ' . $this->_prefix . 'comment AS c';
		$params = array();
		if (is_bool($isActive)) {
			$sql .= ' WHERE c.is_active = ?';;
			$params[] = (int)$isActive;
		}
		$sql .= ' ORDER BY c.activate_date DESC';
		if (is_int($offset) && is_int($count)) {
			$sql .= $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getLatestByThread()
	{
		$sql  = 'SELECT c.* FROM ' . $this->_prefix . 'comment AS c 
				WHERE c.comment_id IN (SELECT MAX(c2.comment_id) FROM ' . $this->_prefix . 'comment AS c2 GROUP BY c2.page_url)
				ORDER BY c.comment_id DESC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}

	public function countThreads()
	{
		$sql  = 'SELECT COUNT(DISTINCT page_url) AS num_threads FROM ' . $this->_prefix . 'comment';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_threads;
	}
	
	public function getThreadComments($offset, $count, $url, $isActive = null)
	{
		$sql = 'SELECT c.* FROM ' . $this->_prefix . 'comment AS c WHERE c.page_url = ?';
		if (is_bool($isActive)) {
			$sql .= ' AND c.is_active = ' . (int)$isActive;
		}
		$sql .= ' ORDER BY ordering';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($url));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countThreadComments($url, $isActive = null)
	{
		$sql = 'SELECT COUNT(*) AS num_comments FROM ' . $this->_prefix . 'comment AS c
				WHERE c.page_url = ?';
		if (is_bool($isActive)) {
			$sql .= ' AND c.is_active = ' . (int)$isActive;
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($url));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_comments;
	}
}
