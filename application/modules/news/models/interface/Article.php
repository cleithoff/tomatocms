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
 * @version 	$Id: Article.php 4653 2010-08-15 18:06:31Z huuphuoc $
 * @since		2.0.5
 */

interface News_Models_Interface_Article
{
	/**
	 * Get article by given Id
	 * 
	 * @param int $id Id of article
	 * @return News_Models_Article
	 */
	public function getById($id);

	/**
	 * Add new article
	 * 
	 * @param News_Models_Article $article
	 * @return int
	 */
	public function add($article);
	
	/**
	 * Update article
	 * 
	 * @param News_Models_Article $article
	 * @return int
	 */
	public function update($article);
	
	/**
	 * Search for articles by collection of conditions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Searching conditions. An array contain various conditions, keys including:
	 * - article_id
	 * - category_id
	 * - created_user_id
	 * - status
	 * - keyword
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset, $count, $exp = null);
	
	/**
	 * Count the number of articles by collection of conditions
	 * 
	 * @param array $exp Searching conditions (@see find)
	 * @return int
	 */
	public function count($exp = null);
	
	/**
	 * Delete article by given Id
	 * 
	 * @param int $id Id of article. If $id is null, it will delete all articles which has status of 'deleted' 
	 * @return int
	 */
	public function delete($id = null);
	
	/**
	 * Update article status
	 * 
	 * @param int $id Id of article
	 * @param string $status New status
	 * @return int
	 */
	public function updateStatus($id, $status);
	
	/**
	 * Get list of articles tagged by given tag
	 * 
	 * @param int $tagId Id of tag
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getByTag($tagId, $offset, $count);
	
	/**
	 * Get number of articles tagged by given tag
	 * 
	 * @param int $tagId Id of tag
	 * @return int
	 */
	public function countByTag($tagId);
	
	/**
	 * Increase number of article views
	 * 
	 * @param int $articleId Id of article
	 */
	public function increaseViews($articleId);
	
	/**
	 * Get latest articles in category
	 * 
	 * @param int $categoryId Id of category
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getByCategory($categoryId, $offset, $count);
	
	/**
	 * Get array of article category Ids
	 * 
	 * @param int $articleId Id of article
	 * @return array
	 */
	public function getCategoryIds($articleId);
	
	/**
	 * Add the article to category
	 * 
	 * @param int $articleId Id of article
	 * @param int $categoryId Id of category
	 * @param bool $reset If $reset is true, remove article from all categories before adding
	 */
	public function addToCategory($articleId, $categoryId, $reset = false);
	
	/**
	 * Add article to collection of hot articles
	 * 
	 * @param int $articleId Id of article
	 * @param bool $reset If $reset is true, remove article from hot collection before adding
	 */
	public function addHotArticle($articleId, $reset = false);
	
	/**
	 * Check whether article is hot or not
	 * 
	 * @param int $articleId Id of article
	 * @return bool
	 */
	public function isHot($articleId);
	
	/**
	 * Get hot articles
	 * 
	 * @param int $limit
	 * @param string $status
	 * @return Tomato_Model_RecordSet
	 */
	public function getHotArticles($limit, $status = null);
	
	/**
	 * Update order of hot article
	 * 
	 * @param int $order
	 * @param int $articleId Id of article
	 * @return int
	 */
	public function updateHotOrder($order, $articleId = null);
	
	/**
	 * Get translable items which haven't been translated of the default language
	 * 
	 * @since 2.0.8
	 * @param string $lang
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getTranslatable($lang, $count);
	
	/**
	 * Get translation item which was translated to given category
	 * 
	 * @since 2.0.8
	 * @param News_Models_Article $article
	 * @return News_Models_Article
	 */
	public function getSource($article);
}
