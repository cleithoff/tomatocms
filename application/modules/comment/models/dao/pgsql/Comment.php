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
 * @version 	$Id: Comment.php 5420 2010-09-14 08:17:02Z leha $
 * @since		2.0.5
 */

class Comment_Models_Dao_Pgsql_Comment extends Tomato_Model_Dao
	implements Comment_Models_Interface_Comment
{
	public function convert($entity) 
	{
		return new Comment_Models_Comment($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "comment 
						WHERE comment_id = %s
						LIMIT 1", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Comment_Models_Comment(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($comment) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "comment (title, content, is_active, 
							email, ip, full_name, web_site, created_date, 
							reply_to, depth, path, ordering, page_url, activate_date)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, %s, '%s', '%s', '%s', %s)
						RETURNING comment_id",
						pg_escape_string($comment->title),
						pg_escape_string($comment->content),
						pg_escape_string($comment->is_active),
						pg_escape_string($comment->email),
						pg_escape_string($comment->ip),
						pg_escape_string($comment->full_name),
						pg_escape_string($comment->web_site),
						pg_escape_string($comment->created_date),
						pg_escape_string($comment->reply_to),
						pg_escape_string($comment->depth),
						pg_escape_string($comment->path),
						pg_escape_string($comment->ordering),
						pg_escape_string($comment->page_url),
						($comment->activate_date) ? "'" . pg_escape_string($comment->activate_date) . "'" : 'null');
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->comment_id;			
	}
	
	public function update($comment) 
	{
		return pg_update($this->_conn, $this->_prefix . 'comment', 
						array(
							'title'			=> $comment->title,
							'content'		=> $comment->content,
							'is_active'		=> $comment->is_active,
							'email'			=> $comment->email,
							'ip'			=> $comment->ip,
							'full_name'		=> $comment->full_name,
							'web_site'		=> $comment->web_site,
							'activate_date'	=> $comment->activate_date,
						), 
						array(
							'comment_id'    => $comment->comment_id,
						));
	}
	
	public function reupdateOrderInThread($comment)
	{
		$sql = sprintf("SELECT MAX(c.ordering) AS max_ordering 
						FROM " . $this->_prefix . "comment AS c
						WHERE c.page_url = '%s'
						LIMIT 1",
						pg_escape_string($comment->page_url));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		$ordering = $row->max_ordering;
		pg_free_result($rs);
		
		$depth = 0;
		$path  = $comment->comment_id . '-';
		if ($comment->reply_to) {
			$replyTo = $this->getById($comment->reply_to); 
			if ($replyTo != null) {
				$sql = sprintf("SELECT MAX(c.ordering) AS max_ordering 
								FROM " . $this->_prefix . "comment AS c
								WHERE c.path LIKE '%s%'
								LIMIT 1",
								pg_escape_string($replyTo->path));
				$rs  = pg_query($sql);
				if (0 == pg_num_rows($rs)) {
					$ordering = $replyTo->ordering;
				} else {
					$row = pg_fetch_object($rs);
					$ordering = $row->max_ordering;
				}
				pg_free_result($rs);
				
				$path  = $replyTo->path.$path;
				$depth = $replyTo->depth + 1;
				$sql   = sprintf("UPDATE " . $this->_prefix . "comment 
								SET ordering = ordering + 1
								WHERE page_url = '%s' AND ordering > %s",
								pg_escape_string($comment->page_url),
								pg_escape_string($ordering));
				pg_query($sql);
			}
		}		
		
		return pg_update($this->_conn, $this->_prefix . 'comment', 
						array(
							'ordering' => $ordering + 1,
							'depth'	   => $depth,
							'path'	   => $path,
						), array(
							'comment_id' => $comment->comment_id,
						));
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'comment', 
						array(
							'comment_id' => $id,
						));
	}
	
	public function toggleActive($comment)
	{
		return pg_update($this->_conn, $this->_prefix . 'comment', 
						array(
							'is_active' 	=> new Zend_Db_Expr('1 - is_active'),
							'activate_date' => $comment->activate_date,
						),
						array(
							'comment_id'    => $comment->comment_id,
						));
	}
	
	public function getLatest($offset, $count, $isActive = null)
	{
		$sql = 'SELECT * FROM ' . $this->_prefix . 'comment AS c';
		if (is_bool($isActive)) {
			$sql .= sprintf(' WHERE c.is_active = %s', (int)$isActive);
		}
		$sql .= ' ORDER BY c.activate_date DESC';
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(' LIMIT %s OFFSET %s', $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getLatestByThread()
	{
		$sql = 'SELECT c.* FROM ' . $this->_prefix . 'comment AS c 
				WHERE c.comment_id IN (SELECT MAX(c2.comment_id) FROM ' . $this->_prefix . 'comment AS c2 GROUP BY c2.page_url)
				ORDER BY c.comment_id DESC';
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}

	public function countThreads()
	{
		$sql = 'SELECT COUNT(DISTINCT page_url) AS num_threads FROM ' . $this->_prefix . 'comment';
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_threads;
	}
	
	public function getThreadComments($offset, $count, $url, $isActive = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "comment AS c
						WHERE c.page_url = '%s'",
						pg_escape_string($url));
		if (is_bool($isActive)) {
			$sql .= sprintf(' AND c.is_active = %s', (int)$isActive);
		}
		$sql .= ' ORDER BY ordering';
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(' LIMIT %s OFFSET %s', $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countThreadComments($url, $isActive = null)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_comments FROM " . $this->_prefix . "comment AS c
						WHERE c.page_url = '%s'",
						pg_escape_string($url));
		if (is_bool($isActive)) {
			$sql .= sprintf(' AND c.is_active = %s', (int)$isActive);
		}
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_comments;
	}
}
