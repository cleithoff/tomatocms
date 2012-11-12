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
 * @version 	$Id: Article.php 5340 2010-09-07 08:50:11Z huuphuoc $
 * @since		2.0.5
 */

class News_Models_Dao_Pdo_Mysql_Article extends Tomato_Model_Dao
	implements News_Models_Interface_Article
{
	public function convert($entity) 
	{
		return new News_Models_Article($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'))
					->where('a.article_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new News_Models_Article($row);
	}
	
	public function add($article) 
	{
		$this->_conn->insert($this->_prefix . 'news_article', 
							array(
								'category_id' 		=> $article->category_id,
								'title' 			=> $article->title,
								'sub_title' 		=> $article->sub_title,
								'slug' 				=> $article->slug,
								'description' 		=> $article->description,
								'content' 			=> $article->content,
								'created_date' 		=> $article->created_date,
								'created_user_id' 	=> $article->created_user_id,
								'created_user_name' => $article->created_user_name,
								'author' 			=> $article->author,
								'allow_comment' 	=> (int)$article->allow_comment,
								'image_square' 		=> $article->image_square,
								'image_thumbnail' 	=> $article->image_thumbnail,
								'image_small' 		=> $article->image_small,
								'image_crop' 		=> $article->image_crop,
								'image_medium' 		=> $article->image_medium,
								'image_large' 		=> $article->image_large,
								'sticky' 			=> (int)$article->sticky,
								'status' 			=> $article->status,
								'icons' 			=> $article->icons,
								/**
								 * @since 2.0.8
								 */
								'language'			=> $article->language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'news_article');
	}
	
	public function update($article) 
	{
		return $this->_conn->update($this->_prefix . 'news_article', 
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
										'article_id = ?' => $article->article_id,
									));
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix . 'news_article'))
						/**
						 * @since 2.0.8
						 */
						->where('a.language = ?', $this->_lang);
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('a.article_id = ?', $exp['article_id']);
			}
			if (isset($exp['category_id'])) {
				$select->where('a.category_id = ?', $exp['category_id']);
			}
			if (isset($exp['created_user_id'])) {
				$select->where('a.created_user_id = ?', $exp['created_user_id']);
			}
			if (isset($exp['status'])) {
				$select->where('a.status = ?', $exp['status']);
			}
			if (isset($exp['keyword'])) {
				$select->where("a.title LIKE '%" . addslashes($exp['keyword']) . "%'");
			}
		}
		$rs = $select->order('a.article_id DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix . 'news_article'), array('num_articles' => 'COUNT(*)'))
						/**
						 * @since 2.0.8
						 */
						->where('a.language = ?', $this->_lang);
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('a.article_id = ?', $exp['article_id']);
			}
			if (isset($exp['category_id'])) {
				$select->where('a.category_id = ?', $exp['category_id']);
			}
			if (isset($exp['created_user_id'])) {
				$select->where('a.created_user_id = ?', $exp['created_user_id']);
			}
			if (isset($exp['status'])) {
				$select->where('a.status = ?', $exp['status']);
			}
			if (isset($exp['keyword'])) {
				$select->where("a.title LIKE '%" . addslashes($exp['keyword']) . "%'");
			}
		}
		return $select->query()->fetch()->num_articles;
	}
	
	public function delete($id = null)
	{
		/**
		 * Remove from collection of hot articles
		 */
		if ($id) {
			$this->_conn->delete($this->_prefix . 'news_article_hot', 
								array(
									'article_id = ?' => $id,
								));
		}
		
		if ($id) {
			return $this->_conn->delete($this->_prefix . 'news_article', 
								array(
									'article_id = ?' => $id,
								));
		} else {
			return $this->_conn->delete($this->_prefix . 'news_article', 
								array(
									'status = ?' => 'deleted',
								));
		}
		
		return 0;		
	}
	
	public function updateStatus($id, $status) 
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		return $this->_conn->update($this->_prefix . 'news_article', 
									array(
										'status' 			 => $status,
										'activate_user_id'   => $user->user_id,
										'activate_user_name' => $user->user_name,
										'activate_date' 	 => date('Y-m-d H:i:s'),
									),
									array(
										'article_id = ?' => $id,
									));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$rs = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 'a.article_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'article_id')
					->where('a.status = ?', 'active')
					/**
					 * @since 2.0.8
					 */
					->where('a.language = ?', $this->_lang)
					->group('a.article_id')
					->order('a.article_id DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);		
	}
	
	public function countByTag($tagId)
	{
		return $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'), array('num_articles' => 'COUNT(article_id)'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 'a.article_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'article_id')
					->where('a.status = ?', 'active')
					/**
					 * @since 2.0.8
					 */
					->where('a.language = ?', $this->_lang)
					->query()
					->fetch()
					->num_articles;		
	}
	
	public function increaseViews($articleId)
	{
		return $this->_conn->update($this->_prefix . 'news_article', 
									array(
										'num_views' => new Zend_Db_Expr('num_views + 1'),
									),
									array(
										'article_id = ?' => $articleId,
									));
	}
	
	public function getByCategory($categoryId, $offset, $count)
	{
		$rs = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'), array('article_id', 'title', 'slug', 'description', 'image_thumbnail', 'image_small', 'image_crop', 'activate_date', 'created_user_name', 'category_id', 'num_views', 'icons'))
					->joinInner(array('ac' => $this->_prefix . 'news_article_category_assoc'), 'a.article_id = ac.article_id', array('category_id'))
					->joinInner(array('c' => $this->_prefix . 'category'), 'a.category_id = c.category_id', array('category_name' => 'name'))
					->where('ac.category_id = ?', $categoryId)
					->where('a.status = ?', 'active')
					->order('a.activate_date DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);		
	}
	
	public function getCategoryIds($articleId)
	{
		$rs = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article_category_assoc'), array('category_id'))
					->where('a.article_id = ?', $articleId)
					->query()
					->fetchAll();
		$categoryIds = array();
		if ($rs) {
			foreach ($rs as $row) {
				$categoryIds[] = $row->category_id;
			}
		}
		return $categoryIds;		
	}
	
	public function addToCategory($articleId, $categoryId, $reset = false)
	{
		if ($reset) {
			$this->_conn->delete($this->_prefix . 'news_article_category_assoc', 
								array(
									'article_id = ?' => $articleId,
								));
		}
		$this->_conn->insert($this->_prefix . 'news_article_category_assoc', 
							array(
								'category_id' => $categoryId,
								'article_id'  => $articleId,
							));
	}
	
	public function addHotArticle($articleId, $reset = false)
	{
		if ($reset) {
			$this->_conn->delete($this->_prefix . 'news_article_hot', 
								array(
									'article_id = ?' => $articleId,
								));
		}
		$this->_conn->insert($this->_prefix . 'news_article_hot', 
							array(
								'article_id'   => $articleId,
								'created_date' => date('Y-m-d H:i:s'),
								'ordering'	   => 1,
							));
	}
	
	public function isHot($articleId)
	{
		$row = $this->_conn
						->select()
						->from(array('h' => $this->_prefix . 'news_article_hot'), array('num_articles' => 'COUNT(*)'))
						->where('h.article_id = ?', $articleId)
						->limit(1)
						->query()
						->fetch();
		return ($row->num_articles > 0);
	}
	
	public function getHotArticles($limit, $status = null)
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix . 'news_article'))
						->joinInner(array('h' => $this->_prefix . 'news_article_hot'), 'a.article_id = h.article_id', array('ordering'))
						/**
						 * @since 2.0.8
						 */
						->where('a.language = ?', $this->_lang);
		if ($status) {
			$select->where('a.status = ?', $status);
		}
		$rs = $select->order('h.ordering')
					->limit($limit)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);	
	}
	
	public function updateHotOrder($order, $articleId = null)
	{
		if (null == $articleId) {
			return $this->_conn->update($this->_prefix . 'news_article_hot', 
										array(
											'ordering' => $order,
										));
		} else {
			return $this->_conn->update($this->_prefix . 'news_article_hot',
										array(
											'ordering' => $order,
										),
										array(
											'article_id = ?' => $articleId,
										));
		}
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$rs = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'),
						'tr.item_class = ?
						AND (tr.item_id = ? OR tr.source_item_id = ?)
						AND (tr.item_id = a.article_id OR tr.source_item_id = a.article_id)',
						array('tr.source_item_id'))
					->group('a.article_id')
					->bind(array(
						'News_Models_Article',
						$item->article_id,
						$item->article_id,
					))
					->query()
					->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang, $count)
	{
		$rs = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'))
					->joinLeft(array('tr' => $this->_prefix . 'core_translation'), 
							'tr.source_item_id = a.article_id 
							AND tr.item_class = ? 
							AND tr.language = ?',
							array('translatable' => '(tr.item_id IS NULL)'))
					->where('a.language = ?', $this->_lang)
					->order('a.article_id DESC')
					->limit($count)
					->bind(array(
						'News_Models_Article', 
						$lang,
					))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($article)
	{
		$row = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'news_article'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'), 'a.article_id = tr.source_item_id', array())
					->where('tr.item_class = ?', 'News_Models_Article')
					->where('tr.item_id = ?', $article->article_id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new News_Models_Article($row);
	}
}
