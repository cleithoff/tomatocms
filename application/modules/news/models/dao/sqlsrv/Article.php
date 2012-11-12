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
 * @version 	$Id: Article.php 5318 2010-09-07 04:07:24Z huuphuoc $
 * @since		2.0.5
 */

class News_Models_Dao_Sqlsrv_Article extends Tomato_Model_Dao
	implements News_Models_Interface_Article
{
	public function convert($entity) 
	{
		return new News_Models_Article($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = "SELECT TOP 1 * FROM " . $this->_prefix . "news_article WHERE article_id = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new News_Models_Article($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($article) 
	{
		$this->_conn->insert($this->_prefix . 'news_article', array(
			'category_id' 		=> $article->category_id,
			'title'				=> $article->title,
			'sub_title' 		=> $article->sub_title,
			'slug'				=> $article->slug,
			'description'		=> $article->description,
			'content'			=> $article->content,
			'created_date'		=> $article->created_date,
			'created_user_id'	=> $article->created_user_id,
			'created_user_name'	=> $article->created_user_name,
			'author'			=> $article->author,
			'allow_comment'		=> (int)$article->allow_comment,
			'image_square'		=> $article->image_square,
			'image_thumbnail'	=> $article->image_thumbnail,
			'image_small'		=> $article->image_small,
			'image_crop'		=> $article->image_crop,
			'image_medium'		=> $article->image_medium,
			'image_large'		=> $article->image_large,			
			'sticky'			=> (int)$article->sticky,
			'status'			=> $article->status,
			'icons'				=> $article->icons,
			/**
			 * @since 2.0.8
			 */
			'language'			=> $article->language,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'news_article');
	}
	
	public function update($article) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'news_article
				SET category_id = ?, title = ?, sub_title = ?, slug = ?, description = ?,
					content = ?, updated_date = ?, updated_user_id = ?, updated_user_name = ?, author = ?,
					allow_comment = ?, image_square = ?, image_thumbnail = ?,  
					image_small = ?, image_crop = ?, image_medium = ?, image_large = ?, sticky = ?, icons = ?, language = ?
				WHERE article_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$article->category_id,
			$article->title,
			$article->sub_title,
			$article->slug,
			$article->description,
			$article->content,
			$article->updated_date,
			$article->updated_user_id,
			$article->updated_user_name,
			$article->author,
			(int)$article->allow_comment,
			$article->image_square,
			$article->image_thumbnail,
			$article->image_small,
			$article->image_crop,
			$article->image_medium,
			$article->image_large,			
			(int)$article->sticky,
			$article->icons,
			$article->language,
			$article->article_id,
		));
		$numRows = $stmt->rowCount();	
		$stmt->closeCursor();			
		return $numRows;
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql   	= "SELECT * FROM " . $this->_prefix . "news_article AS a
					WHERE a.language = ?";
		/**
		 * @since 2.0.8
		 */
		$params = array($this->_lang);
		if ($exp) {
			$where = array();
			
			if (isset($exp['article_id'])) {
				$where[]  = "a.article_id = ?";
				$params[] = $exp['article_id'];
			}
			if (isset($exp['category_id'])) {
				$where[]  = "a.category_id = ?";
				$params[] = $exp['category_id'];
			}
			if (isset($exp['created_user_id'])) {
				$where[]  = "a.created_user_id = ?";
				$params[] = $exp['created_user_id'];
			}
			if (isset($exp['status'])) {
				$where[]  = "a.status = ?";
				$params[] = $exp['status'];
			}
			if (isset($exp['keyword'])) {
				$where[] = "a.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= ' AND ' . implode('AND ', $where);
			}
		}
		$sql .= ' ORDER BY article_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql = "SELECT COUNT(*) AS num_articles FROM " . $this->_prefix . "news_article AS a 
				WHERE a.language = ?";
		/**
		 * @since 2.0.8
		 */
		$params = array($this->_lang);
		if ($exp) {
			$where = array();
			
			if (isset($exp['article_id'])) {
				$where[]  = "a.article_id = ?";
				$params[] = $exp['article_id'];
			}
			if (isset($exp['category_id'])) {
				$where[]  = "a.category_id = ?";
				$params[] = $exp['category_id'];
			}
			if (isset($exp['created_user_id'])) {
				$where[]  = "a.created_user_id = ?";
				$params[] = $exp['created_user_id'];
			}
			if (isset($exp['status'])) {
				$where[]  = "a.status = ?";
				$params[] = $exp['status'];
			}
			if (isset($exp['keyword'])) {
				$where[] = "a.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= ' AND ' . implode(' AND ', $where);
			}
		}
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_articles;
	}
	
	public function delete($id = null)
	{
		if ($id) {
			$sql  = "DELETE FROM " . $this->_prefix . "news_article_hot 
					WHERE article_id = ?";
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($id));
			$numRows = $stmt->rowCount(); 
			$stmt->closeCursor();
		}
		
		$sql    = null;
		$params = array();
		if ($id) {
			$sql = "DELETE FROM " . $this->_prefix . "news_article 
					WHERE article_id = ?";
			$params = array($id);
		} else {
			$sql = "DELETE FROM " . $this->_prefix . "news_article WHERE status = 'deleted'";
		}
		
		if ($sql) {		
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute($params);
			$numRows = $stmt->rowCount();
			$stmt->closeCursor();
			return $numRows;
		}
		return 0;
	}
	
	public function updateStatus($id, $status) 
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$sql  = 'UPDATE ' . $this->_prefix . 'news_article
				SET status = ?, activate_user_id = ?, activate_user_name = ?, activate_date = ?
				WHERE article_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$status,
			$user->user_id,
			$user->user_name,
			date('Y-m-d H:i:s'),
			$id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = "SELECT article_id, category_id, title, slug, CONVERT(varchar(500),description) AS description, image_thumbnail, icons, created_date
				FROM " . $this->_prefix . "news_article AS a
				INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
					ON a.article_id = ti.item_id
				WHERE ti.tag_id = ?
					AND ti.item_name = 'article_id'
					AND a.status = 'active'
					AND a.language = ?
				GROUP BY article_id, category_id, title, slug, CONVERT(varchar(500),description), image_thumbnail, icons
				ORDER BY a.article_id DESC";
		$sql  = $this->_conn->limit($sql, $count, $offset);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId, $this->_lang));
		
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql  = "SELECT COUNT(article_id) AS num_articles
				FROM " . $this->_prefix . "news_article AS a
				INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
					ON a.article_id = ti.item_id
				WHERE ti.tag_id = ?
					AND ti.item_name = 'article_id'
					AND a.status = 'active'
					AND a.language = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId, $this->_lang));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_articles;	
	}
	
	public function increaseViews($articleId)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'news_article
				SET num_views = num_views + 1
				WHERE article_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($articleId));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getByCategory($categoryId, $offset, $count)
	{
		$sql = "SELECT a.*, c.name AS category_name
				FROM " . $this->_prefix . "news_article AS a
				INNER JOIN " . $this->_prefix . "news_article_category_assoc AS ac
					ON a.article_id = ac.article_id
				INNER JOIN " . $this->_prefix . "category AS c
					ON a.category_id = c.category_id
				WHERE ac.category_id = ?
					AND a.status = 'active'
				ORDER BY activate_date DESC";
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($categoryId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);	
	}
	
	public function getCategoryIds($articleId)
	{
		$sql  = 'SELECT category_id 
				FROM ' . $this->_prefix . 'news_article_category_assoc AS a
				WHERE a.article_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($articleId));
		$rows = $stmt->fetchAll();
		$categoryIds = array();
		foreach ($rows as $row) {
			$categoryIds[] = $row->category_id;
		}
		$stmt->closeCursor();
		return $categoryIds;
	}
	
	public function addToCategory($articleId, $categoryId, $reset = false)
	{
		if ($reset) {
			$sql  = 'DELETE FROM ' . $this->_prefix . 'news_article_category_assoc WHERE article_id = ?';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($articleId));
			$stmt->closeCursor();
		}
		$this->_conn->insert($this->_prefix . 'news_article_category_assoc', array(
			'category_id' => $categoryId,
			'article_id'  => $articleId,
		));
	}
	
	public function addHotArticle($articleId, $reset = false)
	{
		if ($reset) {
			$sql  = 'DELETE FROM ' . $this->_prefix . 'news_article_hot WHERE article_id = ?';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($articleId));
			$stmt->closeCursor();
		}
		$this->_conn->insert($this->_prefix . 'news_article_hot', array(
			'article_id'   => $articleId,
			'created_date' => date('Y-m-d H:i:s'),
			'ordering' 	   => 1,
		));
	}
	
	public function isHot($articleId)
	{
		$sql  = 'SELECT TOP 1 COUNT(*) AS num_articles
				FROM ' . $this->_prefix . 'news_article_hot AS h
				WHERE h.article_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($articleId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return ($row->num_articles > 0);
	}
	
	public function getHotArticles($limit, $status = null)
	{
		$sql = 'SELECT a.*, h.ordering as ordering_hot FROM ' . $this->_prefix . 'news_article AS a
			   	INNER JOIN ' . $this->_prefix . 'news_article_hot AS h
					ON a.article_id = h.article_id
				WHERE a.language = ?';
		if ($status) {
			$sql .= ' AND a.status = ?';
		}
		$sql .= ' ORDER BY ordering_hot';
		if (is_numeric($limit)) {
			$sql = $this->_conn->limit($sql, $limit);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($this->_lang, $status));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor(); 
		return new Tomato_Model_RecordSet($rows, $this);	
	}
	
	public function updateHotOrder($order, $articleId = null)
	{
		$row = null;
		if (null == $articleId) {
			$sql  = 'UPDATE ' . $this->_prefix . 'news_article_hot
					SET ordering = ?';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($order));
			$numRows = $stmt->rowCount();
			$stmt->closeCursor();
		} else {
			$sql  = 'UPDATE ' . $this->_prefix . 'news_article_hot
					SET ordering = ?
					WHERE article_id = ?';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($order, $articleId));
			$numRows = $stmt->rowCount();
			$stmt->closeCursor();
		}
		return $numRows;
	}
	
/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = 'SELECT a.*, tr.source_item_id FROM ' . $this->_prefix . 'news_article AS a
				INNER JOIN ' . $this->_prefix . 'core_translation AS tr
					ON tr.item_class = ?
					AND (tr.item_id = ? OR tr.source_item_id = ?)
					AND (tr.item_id = a.article_id OR tr.source_item_id = a.article_id)
				GROUP BY a.article_id';
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array('News_Models_Article', $item->article_id, $item->article_id));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor(); 
		return (null == $rows) ? null : new Tomato_Model_RecordSet($rows, $this);	
	}
	
	public function getTranslatable($lang, $count)
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'news_article AS a
				LEFT JOIN ' . $this->_prefix . 'core_translation AS tr 
					ON tr.source_item_id = a.article_id
					AND tr.item_class = ? 
					AND tr.language = ? AND tr.item_id IS NULL 
				WHERE a.language = ?
				ORDER BY a.article_id DESC';
		
		$sql  = $this->_conn->limit($sql, $count);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array('News_Models_Article', $lang, $this->_lang));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor(); 
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($article)
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'news_article AS a
				INNER JOIN ' . $this->_prefix . 'core_translation AS tr
					ON a.article_id = tr.source_item_id
				WHERE tr.item_class = ? AND tr.item_id = ?';
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array('News_Models_Article', $article->article_id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new News_Models_Article($row);
	}
}
