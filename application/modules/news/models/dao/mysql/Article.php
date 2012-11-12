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
 * @version 	$Id: Article.php 5454 2010-09-16 17:39:23Z huuphuoc $
 * @since		2.0.5
 */

class News_Models_Dao_Mysql_Article extends Tomato_Model_Dao
	implements News_Models_Interface_Article
{
	public function convert($entity) 
	{
		return new News_Models_Article($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "news_article 
						WHERE article_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new News_Models_Article(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($article) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "news_article (category_id, title, sub_title, slug, description,
							content, created_date, created_user_id, created_user_name, author,
							allow_comment, image_square, image_thumbnail, image_small, image_crop, 
							image_medium, image_large, sticky, status, icons,
							language)
						VALUES ('%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s')",
						mysql_real_escape_string($article->category_id),
						mysql_real_escape_string($article->title),
						mysql_real_escape_string($article->sub_title),
						mysql_real_escape_string($article->slug),
						mysql_real_escape_string($article->description),
						
						mysql_real_escape_string($article->content),
						mysql_real_escape_string($article->created_date),
						mysql_real_escape_string($article->created_user_id),
						mysql_real_escape_string($article->created_user_name),
						mysql_real_escape_string($article->author),
						
						(int)$article->allow_comment,
						mysql_real_escape_string($article->image_square),
						mysql_real_escape_string($article->image_thumbnail),
						mysql_real_escape_string($article->image_small),
						mysql_real_escape_string($article->image_crop),
						
						mysql_real_escape_string($article->image_medium),
						mysql_real_escape_string($article->image_large),
						(int)$article->sticky,
						mysql_real_escape_string($article->status),
						mysql_real_escape_string($article->icons),
						
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($article->language));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($article) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "news_article
						SET category_id = '%s', title = '%s', sub_title = '%s', slug = '%s', description = '%s',
							content = '%s', updated_date = '%s', updated_user_id = '%s', updated_user_name = '%s', author = '%s',
							allow_comment = '%s', image_square = '%s', image_thumbnail = '%s', image_small = '%s', image_crop = '%s', 
							image_medium = '%s', image_large = '%s', sticky = '%s', icons = '%s', language = '%s'
						WHERE article_id = '%s'",
						mysql_real_escape_string($article->category_id),
						mysql_real_escape_string($article->title),
						mysql_real_escape_string($article->sub_title),
						mysql_real_escape_string($article->slug),
						mysql_real_escape_string($article->description),
						
						mysql_real_escape_string($article->content),
						mysql_real_escape_string($article->updated_date),
						mysql_real_escape_string($article->updated_user_id),
						mysql_real_escape_string($article->updated_user_name),
						mysql_real_escape_string($article->author),
						
						(int)$article->allow_comment,
						mysql_real_escape_string($article->image_square),
						mysql_real_escape_string($article->image_thumbnail),
						mysql_real_escape_string($article->image_small),
						mysql_real_escape_string($article->image_crop),
						
						mysql_real_escape_string($article->image_medium),
						mysql_real_escape_string($article->image_large),
						(int)$article->sticky,
						mysql_real_escape_string($article->icons),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($article->language),
						
						mysql_real_escape_string($article->article_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "news_article AS a
						WHERE a.language = '%s'",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
		if ($exp) {
			$where = array();
			
			if (isset($exp['article_id'])) {
				$where[] = sprintf("a.article_id = '%s'", mysql_real_escape_string($exp['article_id']));
			}
			if (isset($exp['category_id'])) {
				$where[] = sprintf("a.category_id = '%s'", mysql_real_escape_string($exp['category_id']));
			}
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("a.created_user_id = '%s'", mysql_real_escape_string($exp['created_user_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("a.status = '%s'", mysql_real_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "a.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " AND " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY a.article_id DESC";
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
	
	public function count($exp = null) 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_articles 
						FROM " . $this->_prefix . "news_article AS a
						WHERE a.language = '%s'",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
		if ($exp) {
			$where = array();
			
			if (isset($exp['article_id'])) {
				$where[] = sprintf("a.article_id = '%s'", mysql_real_escape_string($exp['article_id']));
			}
			if (isset($exp['category_id'])) {
				$where[] = sprintf("a.category_id = '%s'", mysql_real_escape_string($exp['category_id']));
			}
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("a.created_user_id = '%s'", mysql_real_escape_string($exp['created_user_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("a.status = '%s'", mysql_real_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "a.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " AND " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_articles;
	}
	
	public function delete($id = null)
	{
		if ($id) {
			$sql = sprintf("DELETE FROM " . $this->_prefix . "news_article_hot  
							WHERE article_id = '%s'",
							mysql_real_escape_string($id));
			mysql_query($sql);
		}
		
		$sql = null;
		if ($id) {
			$sql = sprintf("DELETE FROM " . $this->_prefix . "news_article 
							WHERE article_id = '%s'",
							mysql_real_escape_string($id));
		} else {
			$sql = "DELETE FROM " . $this->_prefix . "news_article WHERE status = 'deleted'";
		}

		if ($sql) {
			mysql_query($sql);
			return mysql_affected_rows();
		}
		return 0;	
	}
	
	public function updateStatus($id, $status) 
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		$sql  = sprintf("UPDATE " . $this->_prefix . "news_article
						SET status = '%s', activate_user_id = '%s', activate_user_name = '%s', activate_date = '%s'
						WHERE article_id = '%s'",
						mysql_real_escape_string($status),
						mysql_real_escape_string($user->user_id),
						mysql_real_escape_string($user->user_name),
						date('Y-m-d H:i:s'),
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = sprintf("SELECT a.*
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON a.article_id = ti.item_id
						WHERE ti.tag_id = '%s'
							AND ti.item_name = 'article_id'
							AND a.status = 'active'
							AND a.language = '%s'
						GROUP BY a.article_id
						ORDER BY a.article_id DESC",
						mysql_real_escape_string($tagId),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql  = sprintf("SELECT COUNT(article_id) AS num_articles
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON a.article_id = ti.item_id
						WHERE ti.tag_id = '%s'
							AND ti.item_name = 'article_id'
							AND a.status = 'active'
							AND a.language = '%s'",
						mysql_real_escape_string($tagId),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
						
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_articles;	
	}
	
	public function increaseViews($articleId)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "news_article
						SET num_views = num_views + 1
						WHERE article_id = '%s'",
						mysql_real_escape_string($articleId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getByCategory($categoryId, $offset, $count)
	{
		$sql = sprintf("SELECT a.*, c.name AS category_name
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "news_article_category_assoc AS ac
							ON a.article_id = ac.article_id
						INNER JOIN " . $this->_prefix . "category AS c
							ON a.category_id = c.category_id
						WHERE ac.category_id = '%s'
							AND a.status = 'active'
						ORDER BY a.activate_date DESC",
						mysql_real_escape_string($categoryId));
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
	
	public function getCategoryIds($articleId)
	{
		$sql = sprintf("SELECT category_id 
						FROM " . $this->_prefix . "news_article_category_assoc AS a
						WHERE a.article_id = '%s'",
						mysql_real_escape_string($articleId));
						
		$rs  = mysql_query($sql);
		$categoryIds = array();
		while ($row = mysql_fetch_object($rs)) {
			$categoryIds[] = $row->category_id;
		}
		return $categoryIds;
	}
	
	public function addToCategory($articleId, $categoryId, $reset = false)
	{
		if ($reset) {
			$sql = sprintf("DELETE FROM " . $this->_prefix . "news_article_category_assoc WHERE article_id = '%s'", 
						mysql_real_escape_string($articleId));
			mysql_query($sql);
		}
		$sql = sprintf("INSERT INTO " . $this->_prefix . "news_article_category_assoc (category_id, article_id)
						VALUES ('%s', '%s')",
						mysql_real_escape_string($categoryId),
						mysql_real_escape_string($articleId));
		mysql_query($sql);
	}
	
	public function addHotArticle($articleId, $reset = false)
	{
		if ($reset) {
			$sql = sprintf("DELETE FROM " . $this->_prefix . "news_article_hot WHERE article_id = '%s'", 
						mysql_real_escape_string($articleId));
			mysql_query($sql);		
		}
		$sql = sprintf("INSERT INTO " . $this->_prefix . "news_article_hot (article_id, created_date, ordering)
						VALUES ('%s', '%s', '%s')",
						mysql_real_escape_string($articleId),
						date('Y-m-d H:i:s'),
						1);
		mysql_query($sql);
	}
	
	public function isHot($articleId)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_articles
						FROM " . $this->_prefix . "news_article_hot AS h
						WHERE h.article_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($articleId));
						
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return ($row->num_articles > 0);
	}
	
	public function getHotArticles($limit, $status = null)
	{
		$sql = sprintf("SELECT a.*, h.ordering 
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "news_article_hot AS h
							ON a.article_id = h.article_id
						WHERE a.language = '%s'",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
		if ($status) {
			$sql .= sprintf(" AND a.status = '%s'", $status);
		}
		$sql .= " ORDER BY h.ordering";
		if (is_numeric($limit)) {
			$sql .= sprintf(" LIMIT %s", $limit);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);	
	}
	
	public function updateHotOrder($order, $articleId = null)
	{
		if (null == $articleId) {
			$sql = sprintf("UPDATE " . $this->_prefix . "news_article_hot
							SET ordering = '%s'",
							mysql_real_escape_string($order));
			mysql_query($sql);
		} else {
			$sql = sprintf("UPDATE " . $this->_prefix . "news_article_hot
							SET ordering = '%s'
							WHERE article_id = '%s'",
							mysql_real_escape_string($order),
							mysql_real_escape_string($articleId));
			mysql_query($sql);
		}
		return mysql_affected_rows();
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = sprintf('SELECT a.*, tr.source_item_id 
						FROM ' . $this->_prefix . 'news_article AS a
						INNER JOIN ' . $this->_prefix . 'core_translation AS tr
							ON tr.item_class = "%s"
							AND (tr.item_id = "%s" OR tr.source_item_id = "%s")
							AND (tr.item_id = a.article_id OR tr.source_item_id = a.article_id)
						GROUP BY a.article_id',
						'News_Models_Article',
						mysql_real_escape_string($item->article_id),
						mysql_real_escape_string($item->article_id));
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang, $count)
	{
		$sql  = sprintf('SELECT a.*, (tr.item_id IS NULL) AS translatable 
						FROM ' . $this->_prefix . 'news_article AS a
						LEFT JOIN ' . $this->_prefix . 'core_translation AS tr
							ON tr.source_item_id = a.article_id
							AND tr.item_class = "%s"
							AND tr.language = "%s"
						WHERE a.language = "%s"
						ORDER BY a.article_id DESC
						LIMIT %s',
						'News_Models_Article',
						mysql_real_escape_string($lang),
						mysql_real_escape_string($this->_lang),
						mysql_real_escape_string($count));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($article)
	{
		$sql = sprintf('SELECT a.* FROM ' . $this->_prefix . 'news_article AS a
						INNER JOIN ' . $this->_prefix . 'core_translation AS tr
							ON a.article_id = tr.source_item_id
						WHERE tr.item_class = "%s" AND tr.item_id = "%s"
						LIMIT 1',
						'News_Models_Article',
						mysql_real_escape_string($article->article_id));
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new News_Models_Article(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
}
