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
 * @version 	$Id: Article.php 5447 2010-09-15 08:52:25Z leha $
 * @since		2.0.5
 */

class News_Models_Dao_Pgsql_Article extends Tomato_Model_Dao
	implements News_Models_Interface_Article
{
	public function convert($entity) 
	{
		return new News_Models_Article($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "news_article 
						WHERE article_id = %s
						LIMIT 1", 
						($id) ? pg_escape_string($id) : 'null');
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new News_Models_Article(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($article) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "news_article (category_id, title, sub_title, slug, description,
							content, created_date, created_user_id, created_user_name, author,
							allow_comment, image_square, image_thumbnail, image_small, image_crop, 
							image_medium, image_large, sticky, status, icons, language)
						VALUES (%s, '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', %s, %s,
							'%s', '%s')
						RETURNING article_id",
						pg_escape_string($article->category_id),
						pg_escape_string($article->title),
						pg_escape_string($article->sub_title),
						pg_escape_string($article->slug),
						pg_escape_string($article->description),
						pg_escape_string($article->content),
						pg_escape_string($article->created_date),
						pg_escape_string($article->created_user_id),
						pg_escape_string($article->created_user_name),
						pg_escape_string($article->author),
						(int)$article->allow_comment,
						pg_escape_string($article->image_square),
						pg_escape_string($article->image_thumbnail),
						pg_escape_string($article->image_small),
						pg_escape_string($article->image_crop),
						pg_escape_string($article->image_medium),
						pg_escape_string($article->image_large),
						(int)$article->sticky,
						($article->status) ? "'" . pg_escape_string($article->status) . "'" : 'null',
						pg_escape_string($article->icons),
						
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($article->language));
						
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->article_id;
	}
	
	public function update($article) 
	{
		return pg_update($this->_conn, $this->_prefix . 'news_article', 
						array(
							'category_id' 		=> $article->category_id,
							'title' 			=> $article->title,
							'sub_title' 		=> $article->sub_title,
							'slug' 				=> $article->slug,
							'description' 		=> $article->description,
							'content' 			=> $article->content,
							'updated_date' 		=> $article->updated_date,
							'updated_user_id' 	=> $article->updated_user_id,
							'updated_user_name' => $article->updated_user_name,
							'author' 			=> $article->author,
							'allow_comment' 	=> (int)$article->allow_comment,
							'image_square' 		=> $article->image_square,
							'image_thumbnail' 	=> $article->image_thumbnail,
							'image_small' 		=> $article->image_small,
							'image_crop' 		=> $article->image_crop,
							'image_medium' 		=> $article->image_medium,
							'image_large' 		=> $article->image_large,				
							'sticky' 			=> (int)$article->sticky,
							'icons' 			=> $article->icons,
							/**
							 * @since 2.0.8
							 */
							'language'			=> $article->language,
						),
						array(
							'article_id' 		=> $article->article_id,
						));
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "news_article AS a
						WHERE a.language = '%s'",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
		if ($exp) {
			$where = array();
			
			if (isset($exp['article_id'])) {
				$where[] = sprintf("a.article_id = %s", pg_escape_string($exp['article_id']));
			}
			if (isset($exp['category_id'])) {
				$where[] = sprintf("a.category_id = %s", pg_escape_string($exp['category_id']));
			}
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("a.created_user_id = %s", pg_escape_string($exp['created_user_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("a.status = '%s'", pg_escape_string($exp['status']));
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
			$sql .= sprintf(" LIMIT %s OFFSET %s", $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
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
						pg_escape_string($this->_lang));
		
		if ($exp) {
			$where = array();
			
			if (isset($exp['article_id'])) {
				$where[] = sprintf("a.article_id = %s", pg_escape_string($exp['article_id']));
			}
			if (isset($exp['category_id'])) {
				$where[] = sprintf("a.category_id = %s", pg_escape_string($exp['category_id']));
			}
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("a.created_user_id = %s", pg_escape_string($exp['created_user_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("a.status = '%s'", pg_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "a.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " AND " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_articles;
	}
	
	public function delete($id = null)
	{
		if ($id) {
			pg_delete($this->_conn, $this->_prefix . 'news_article_hot', 
						array(
							'article_id' => $id,
						));
		}
		
		$sql = null;
		if ($id) {
			return pg_delete($this->_conn, $this->_prefix . 'news_article', 
							array(
								'article_id' => $id,
							));
		} else {
			return pg_delete($this->_conn, $this->_prefix . 'news_article', 
					array(
						'status' => 'deleted',
					));
		}
		
		if ($sql) {
			$rs = pg_query($sql);
			return pg_affected_rows($rs);
		}
		return 0;
	}
	
	public function updateStatus($id, $status) 
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		return pg_update($this->_conn, $this->_prefix . 'news_article', 
						array(
							'status' 			 => $status,
							'activate_user_id'   => $user->user_id,
							'activate_user_name' => $user->user_name,
							'activate_date' 	 => date('Y-m-d H:i:s'),
						),
						array(
							'article_id' => $id,
						));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = sprintf("SELECT MAX(a.article_id),
								MAX(a.title),
								MAX(a.sub_title),
								MAX(a.slug),
								MAX(a.description),		
								MAX(a.content),
								MAX(a.icons),
								MAX(a.image_square),
								MAX(a.image_thumbnail),
								MAX(a.image_small),
								MAX(a.image_crop),
								MAX(a.image_medium),
								MAX(a.image_large),
								MAX(a.status),
								MAX(a.num_views),
								MAX(a.created_date),
								MAX(a.created_user_id),
								MAX(a.created_user_name),
								MAX(a.updated_date),
								MAX(a.updated_user_id),
								MAX(a.updated_user_name),
								MAX(a.activate_date),
								MAX(a.activate_user_id),
								MAX(a.activate_user_name),
								MAX(a.author),
								MAX(a.allow_comment),
								MAX(a.sticky),
								MAX(a.language)
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON a.article_id = ti.item_id
						WHERE ti.tag_id = %s
							AND ti.item_name = 'article_id'
							AND a.status = 'active'
							AND a.language = '%s'
						GROUP BY a.article_id
						ORDER BY a.article_id DESC",
						pg_escape_string($tagId),
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));

		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql = sprintf("SELECT COUNT(article_id) AS num_articles
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON a.article_id = ti.item_id
						WHERE ti.tag_id = %s
							AND ti.item_name = 'article_id'
							AND a.status = 'active'
							AND a.language = '%s'",
						pg_escape_string($tagId),
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
						
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_articles;	
	}
	
	public function increaseViews($articleId)
	{
		return pg_update($this->_conn, $this->_prefix . 'news_article', 
						array(
							'num_views' => new Zend_Db_Expr('num_views + 1'),
						),
						array(
							'article_id' => $articleId,
						));
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
						pg_escape_string($categoryId));
						
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s OFFSET %s", $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);	
	}
	
	public function getCategoryIds($articleId)
	{
		$sql = sprintf("SELECT category_id 
						FROM " . $this->_prefix . "news_article_category_assoc AS a
						WHERE a.article_id = %s",
						pg_escape_string($articleId));
						
		$rs  = pg_query($sql);
		$categoryIds = array();
		while ($row = pg_fetch_object($rs)) {
			$categoryIds[] = $row->category_id;
		}
		return $categoryIds;
	}
	
	public function addToCategory($articleId, $categoryId, $reset = false)
	{
		if ($reset) {
			pg_delete($this->_conn, $this->_prefix . 'news_article_category_assoc', 
						array(
							'article_id' => $articleId,
						));
		}
		pg_insert($this->_conn, $this->_prefix . 'news_article_category_assoc', 
					array(
						'category_id' => $categoryId,
						'article_id'  => $articleId,
					));
	}
	
	public function addHotArticle($articleId, $reset = false)
	{
		if ($reset) {
			pg_delete($this->_conn, $this->_prefix . 'news_article_hot', 
						array(
							'article_id' => $articleId,
						));
		}
		pg_insert($this->_conn, $this->_prefix . 'news_article_hot', 
					array(
						'article_id'   => $articleId,
						'created_date' => date('Y-m-d H:i:s'),
						'ordering'	   => 1,
					));
	}
	
	public function isHot($articleId)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_articles
						FROM " . $this->_prefix . "news_article_hot AS h
						WHERE h.article_id = %s
						LIMIT 1",
						pg_escape_string($articleId));
						
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return ($row->num_articles > 0);
	}
	
	public function getHotArticles($limit, $status = null)
	{
		$sql = sprintf("SELECT a.*, h.ordering 
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "news_article_hot AS h
							ON a.article_id = h.article_id WHERE a.language = '%s'", $this->_lang);
		if ($status) {
			$sql .= sprintf(" AND a.status = '%s'", pg_escape_string($status));
		}
		$sql .= " ORDER BY h.ordering";
		if (is_numeric($limit)) {
			$sql .= sprintf(" LIMIT %s", $limit);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);	
	}
	
	public function updateHotOrder($order, $articleId = null)
	{
		if (null == $articleId) {
			return pg_update($this->_conn, $this->_prefix . 'news_article_hot', 
							array(
								'ordering' => $order,
							));
		} else {
			return pg_update($this->_conn, $this->_prefix . 'news_article_hot',
							array(
								'ordering' => $order,
							),
							array(
								'article_id' => $articleId,
							));
		}
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = sprintf("SELECT MAX(a.article_id),
								MAX(a.title),
								MAX(a.sub_title),
								MAX(a.slug),
								MAX(a.description),		
								MAX(a.content),
								MAX(a.icons),
								MAX(a.image_square),
								MAX(a.image_thumbnail),
								MAX(a.image_small),
								MAX(a.image_crop),
								MAX(a.image_medium),
								MAX(a.image_large),
								MAX(a.status),
								MAX(a.num_views),
								MAX(a.created_date),
								MAX(a.created_user_id),
								MAX(a.created_user_name),
								MAX(a.updated_date),
								MAX(a.updated_user_id),
								MAX(a.updated_user_name),
								MAX(a.activate_date),
								MAX(a.activate_user_id),
								MAX(a.activate_user_name),
								MAX(a.author),
								MAX(a.allow_comment),
								MAX(a.sticky),
								MAX(a.language), 
								MAX(tr.source_item_id) 
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "core_translation AS tr 
							ON tr.item_class = '%s'
							AND (tr.item_id = %s OR tr.source_item_id = %s)
							AND (tr.item_id = a.article_id OR tr.source_item_id = a.article_id)
						GROUP BY a.article_id",
						'News_Models_Article',
						pg_escape_string($item->article_id),
						pg_escape_string($item->article_id));
								
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang, $count)
	{
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "news_article AS a
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON a.article_id = tr.source_item_id
							AND tr.item_class = '%s'
							AND tr.language = '%s'
						WHERE a.language = '%s'",
						'News_Models_Article',
						pg_escape_string($lang),
						pg_escape_string($this->_lang));
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($article)
	{
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "news_article AS a
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON a.article_id = tr.source_item_id
						WHERE tr.item_class = '%s' AND tr.item_id = %s",
						'News_Models_Article',
						pg_escape_string($article->article_id));
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Category_Models_Category(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
}
