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
 * @version 	$Id: Comment.php 5335 2010-09-07 07:32:01Z huuphuoc $
 * @since		2.0.5
 */

class Comment_Models_Dao_Pdo_Mysql_Comment extends Tomato_Model_Dao
	implements Comment_Models_Interface_Comment
{
	public function convert($entity) 
	{
		return new Comment_Models_Comment($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'comment'))
					->where('c.comment_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Comment_Models_Comment($row); 
	}
	
	public function add($comment)
	{
		$this->_conn->insert($this->_prefix . 'comment', 
							array(
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
								'activate_date'	=> $comment->activate_date,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'comment');
	}
	
	public function update($comment) 
	{
		return $this->_conn->update($this->_prefix . 'comment', 
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
										'comment_id = ?' => $comment->comment_id,
									));
	}
	
	public function reupdateOrderInThread($comment)
	{
		$ordering = $this->_conn
						->select()
						->from(array('c' => $this->_prefix . 'comment'), array('max_ordering' => 'MAX(c.ordering)'))
						->where('c.page_url = ?', $comment->page_url)
						->query()
						->fetch()
						->max_ordering;
		$depth = 0;
		$path  = $comment->comment_id . '-';
		if ($comment->reply_to) {
			$replyTo = $this->getById($comment->reply_to); 
			if ($replyTo != null) {
				$row = $this->_conn
							->select()
							->from(array('c' => $this->_prefix . 'comment'), array('max_ordering' => 'MAX(c.ordering)'))
							->where('c.path LIKE ?', $replyTo->path . '%')
							->query()
							->fetch();
							
				$ordering = (null == $row) ? $replyTo->ordering : $row->max_ordering;
				$path     = $replyTo->path . $path;
				$depth    = $replyTo->depth + 1;
				
				$this->_conn->update($this->_prefix . 'comment',
									array(
										'ordering' => new Zend_Db_Expr('ordering + 1'),
									),
									array(
										'page_url = ?' => $comment->page_url,
										'ordering > ?' => $ordering,
									));
			}
		}		
		
		return $this->_conn->update($this->_prefix . 'comment', 
									array(
										'ordering' => $ordering + 1,
										'depth'	   => $depth,
										'path'	   => $path,
									), array(
										'comment_id = ?' => $comment->comment_id,
									));
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'comment', 
									array(
										'comment_id = ?' => $id,
									));
	}
	
	public function toggleActive($comment)
	{
		return $this->_conn->update($this->_prefix . 'comment', 
									array(
										'is_active' 	=> new Zend_Db_Expr('1 - is_active'),
										'activate_date' => $comment->activate_date,
									),
									array(
										'comment_id = ?' => $comment->comment_id,
									));
	}
	
	public function getLatest($offset, $count, $isActive = null)
	{
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix . 'comment'));
		if (is_bool($isActive)) {
			$select->where('c.is_active = ?', (int)$isActive);
		}
		$rs = $select->order('c.activate_date DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getLatestByThread()
	{
		$rs = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'comment'))
					->where('c.comment_id IN (SELECT MAX(c2.comment_id) FROM ' . $this->_prefix . 'comment AS c2 GROUP BY c2.page_url)')
					->order('c.comment_id DESC')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}

	public function countThreads()
	{
		return $this->_conn
					->select()
					->from($this->_prefix . 'comment', array('num_threads' => 'COUNT(DISTINCT page_url)'))
					->query()
					->fetch()
					->num_threads;
	}
	
	public function getThreadComments($offset, $count, $url, $isActive = null)
	{
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix . 'comment'))
						->where('c.page_url = ?', $url);
		if (is_bool($isActive)) {
			$select->where('c.is_active = ?', (int)$isActive);
		}
		$rs = $select->order('c.ordering')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function countThreadComments($url, $isActive = null)
	{
		$select = $this->_conn
						->select()
						->from(array('c' => $this->_prefix . 'comment'), array('num_comments' => 'COUNT(*)'))
						->where('c.page_url = ?', $url);
		if (is_bool($isActive)) {
			$select->where('c.is_active = ?', (int)$isActive);
		}
		return $select->limit(1)->query()->fetch()->num_comments;
	}
}
