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
 * @version 	$Id: Article.php 5097 2010-08-29 19:43:44Z huuphuoc $
 * @since		2.0.5
 */

class News_Widgets_Sticky_Models_Dao_Sqlsrv_Article extends Tomato_Model_Dao
	implements News_Widgets_Sticky_Models_Interface_Article
{
	public function convert($entity)
	{
		return new News_Models_Article($entity);
	}
	
	public function getStickyArticles($limit, $categoryId = null)
	{
		$sql 	= "SELECT a.*
					FROM " . $this->_prefix . "news_article AS a
					WHERE a.status = 'active' AND a.sticky = 1
						AND a.language = ?";
		/**
		 * @since 2.0.8
		 */
		$params = array($this->_lang);
		if (is_numeric($categoryId)) {
			$sql 	 .= ' AND a.category_id = ?';
			$params[] = $categoryId;
		}
		$sql .= ' ORDER BY activate_date DESC';
		if (is_numeric($limit)) {
			$sql = $this->_conn->limit($sql, $limit);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
