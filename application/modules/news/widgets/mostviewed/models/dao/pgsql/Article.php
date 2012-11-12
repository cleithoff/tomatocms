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
 * @version 	$Id: Article.php 4894 2010-08-24 20:18:16Z huuphuoc $
 * @since		2.0.5
 */

class News_Widgets_MostViewed_Models_Dao_Pgsql_Article
	extends Tomato_Model_Dao
	implements News_Widgets_MostViewed_Models_Interface_Article
{
	public function convert($entity)
	{
		return new News_Models_Article($entity);
	}
	
	public function getMostViewedArticles($limit, $categoryId = null)
	{
		$sql = sprintf("SELECT a.article_id, a.category_id, a.title, a.slug, a.activate_date, a.num_views, c.name AS category_name, c.slug AS category_slug
						FROM " . $this->_prefix . "news_article AS a
						LEFT JOIN " . $this->_prefix . "category AS c
							ON a.category_id = c.category_id
						WHERE a.status = 'active' AND a.language = '%s'",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
		if (is_numeric($categoryId)) {
			$sql .= sprintf(" AND a.category_id = %s", $categoryId);
		}
		$sql .= " ORDER BY a.num_views DESC";
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
}
