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

class News_Models_Dao_Sqlsrv_Revision extends Tomato_Model_Dao
	implements News_Models_Interface_Revision
{
	public function convert($entity) 
	{
		return new News_Models_Revision($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'news_article_revision 
				WHERE revision_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new News_Models_Revision($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($revision) 
	{
		$this->_conn->insert($this->_prefix . 'news_article_revision', array(
			'article_id' 		=> $revision->article_id,
			'category_id'		=> $revision->category_id,
			'title'				=> $revision->title,
			'sub_title'			=> $revision->sub_title,
			'slug'				=> $revision->slug,
			'description'		=> $revision->description,
			'content'			=> $revision->content,
			'created_date'		=> $revision->created_date,
			'created_user_id'	=> $revision->created_user_id,
			'created_user_name'	=> $revision->created_user_name,
			'author'			=> $revision->author,
			'icons'				=> $revision->icons,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'news_article_revision');
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql    = 'SELECT * FROM ' . $this->_prefix . 'news_article_revision';
		$params = array();
		if ($exp) {
			if (isset($exp['article_id'])) {
				$sql     .= ' WHERE article_id = ?';
				$params[] = $exp['article_id'];
			}
		}
		$sql .= ' ORDER BY created_date DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql    = 'SELECT COUNT(*) AS num_revisions FROM ' . $this->_prefix . 'news_article_revision';
		$params = array();
		if ($exp) {
			if (isset($exp['article_id'])) {
				$sql     .= ' WHERE article_id = ?';
				$params[] = $exp['article_id'];
			}
		}
		$this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_revisions;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'news_article_revision WHERE revision_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $numRows;
	}	
}
