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
 * @version 	$Id: Article.php 4895 2010-08-24 20:20:43Z huuphuoc $
 * @since		2.0.5
 */

class News_Widgets_Newer_Models_Dao_Mysql_Article extends Tomato_Model_Dao
	implements News_Widgets_Newer_Models_Interface_Article
{
	public function convert($entity)
	{
		return new News_Models_Article($entity);
	}
	
	public function getNewerArticles($limit, $article = null, $categoryId = null)
	{
		$sql = sprintf("SELECT article_id, category_id, title, slug, activate_date, image_square, icons
						FROM " . $this->_prefix . "news_article AS a
						WHERE a.status = 'active'
							AND a.language = '%s'",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
		if (is_numeric($categoryId)) {
			$sql .= sprintf(" AND a.category_id = '%s'", $categoryId);
		}
		if ($article != null && $article->activate_date != null) {
			$sql .= sprintf(" AND a.activate_date > '%s'", $article->activate_date);
		}
		$sql .= " ORDER BY a.activate_date DESC";
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
}
