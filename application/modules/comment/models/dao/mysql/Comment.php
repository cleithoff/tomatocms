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

class Comment_Models_Dao_Mysql_Comment extends Tomato_Model_Dao
	implements Comment_Models_Interface_Comment
{
	public function convert($entity) 
	{
		return new Comment_Models_Comment($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "comment 
						WHERE comment_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Comment_Models_Comment(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($comment) 
	{
		$sql = sprintf("INSERT " . $this->_prefix . "comment (title, content, is_active, 
							email, ip, full_name, web_site, created_date, 
							reply_to, depth, path, ordering, page_url, activate_date)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s)",
						mysql_real_escape_string($comment->title),
						mysql_real_escape_string($comment->content),
						mysql_real_escape_string($comment->is_active),
						mysql_real_escape_string($comment->email),
						mysql_real_escape_string($comment->ip),
						mysql_real_escape_string($comment->full_name),
						mysql_real_escape_string($comment->web_site),
						mysql_real_escape_string($comment->created_date),
						mysql_real_escape_string($comment->reply_to),
						mysql_real_escape_string($comment->depth),
						mysql_real_escape_string($comment->path),
						mysql_real_escape_string($comment->ordering),
						mysql_real_escape_string($comment->page_url),
						($comment->activate_date) ? "'" . mysql_real_escape_string($comment->activate_date) . "'" : 'null');
		mysql_query($sql);
		return mysql_insert_id();				
	}
	
	public function update($comment) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "comment
						SET title = '%s', content = '%s', is_active = '%s', 
							email = '%s', ip = '%s', full_name = '%s', web_site = '%s', activate_date = '%s'
						WHERE comment_id = '%s'",
						mysql_real_escape_string($comment->title),
						mysql_real_escape_string($comment->content),
						mysql_real_escape_string($comment->is_active),
						mysql_real_escape_string($comment->email),
						mysql_real_escape_string($comment->ip),
						mysql_real_escape_string($comment->full_name),
						mysql_real_escape_string($comment->web_site),
						mysql_real_escape_string($comment->activate_date),
						mysql_real_escape_string($comment->comment_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function reupdateOrderInThread($comment)
	{
		$sql = sprintf("SELECT MAX(c.ordering) AS max_ordering 
						FROM " . $this->_prefix . "comment AS c
						WHERE c.page_url = '%s'
						LIMIT 1",
						mysql_real_escape_string($comment->page_url));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		$ordering = $row->max_ordering;
		mysql_free_result($rs);
		
		$depth = 0;
		$path = $comment->comment_id . '-';
		if ($comment->reply_to) {
			$replyTo = $this->getById($comment->reply_to); 
			if ($replyTo != null) {
				$sql = sprintf("SELECT MAX(c.ordering) AS max_ordering 
								FROM " . $this->_prefix . "comment AS c
								WHERE c.path LIKE '%s%'
								LIMIT 1",
								mysql_real_escape_string($replyTo->path));
				$rs  = mysql_query($sql);
				if (0 == mysql_num_rows($rs)) {
					$ordering = $replyTo->ordering;
				} else {
					$row = mysql_fetch_object($rs);
					$ordering = $row->max_ordering;
				}
				mysql_free_result($rs);
				
				$path  = $replyTo->path.$path;
				$depth = $replyTo->depth + 1;
				$sql   = sprintf("UPDATE " . $this->_prefix . "comment 
								SET ordering = ordering + 1
								WHERE page_url = '%s' AND ordering > %s",
								mysql_real_escape_string($comment->page_url),
								mysql_real_escape_string($ordering));
				mysql_query($sql);
			}
		}		
		
		$sql = sprintf("UPDATE " . $this->_prefix . "comment
						SET ordering = '%s', depth = '%s', path = '%s'
						WHERE comment_id = '%s'",
						mysql_real_escape_string($ordering + 1),
						mysql_real_escape_string($depth),
						mysql_real_escape_string($path),
						mysql_real_escape_string($comment->comment_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "comment WHERE comment_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function toggleActive($comment)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "comment
						SET is_active = 1 - is_active, activate_date = '%s' 
						WHERE comment_id = '%s'",
						mysql_real_escape_string($comment->activate_date),
						mysql_real_escape_string($comment->comment_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getLatest($offset, $count, $isActive = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "comment AS c";
		if (is_bool($isActive)) {
			$sql .= sprintf(" WHERE c.is_active = '%s'", (int)$isActive);
		}
		$sql .= " ORDER BY c.activate_date DESC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getLatestByThread()
	{
		$sql  = "SELECT c.* FROM " . $this->_prefix . "comment AS c 
				WHERE c.comment_id IN (SELECT MAX(c2.comment_id) FROM " . $this->_prefix . "comment AS c2 GROUP BY c2.page_url)
				ORDER BY c.comment_id DESC";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}

	public function countThreads()
	{
		$sql = "SELECT COUNT(DISTINCT page_url) AS num_threads FROM " . $this->_prefix . "comment";
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_threads;
	}
	
	public function getThreadComments($offset, $count, $url, $isActive = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "comment AS c
						WHERE c.page_url = '%s'",
						mysql_real_escape_string($url));
		if (is_bool($isActive)) {
			$sql .= sprintf(" AND c.is_active = '%s'", (int)$isActive);
		}
		$sql .= " ORDER BY ordering";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countThreadComments($url, $isActive = null)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_comments FROM " . $this->_prefix . "comment AS c
						WHERE c.page_url = '%s'",
						mysql_real_escape_string($url));
		if (is_bool($isActive)) {
			$sql .= sprintf(" AND c.is_active = '%s'", (int)$isActive);
		}
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_comments;
	}
}
