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
 * @version 	$Id: Article.php 4714 2010-08-17 02:32:22Z huuphuoc $
 * @since		2.0.5
 */

class News_Widgets_MostViewed_Models_Dao_Pdo_Mysql_Article
	extends Tomato_Model_Dao
	implements News_Widgets_MostViewed_Models_Interface_Article
{
	public function convert($entity)
	{
		return new News_Models_Article($entity);
	}
	
	public function getMostViewedArticles($limit, $categoryId = null)
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix . 'news_article'), array('article_id', 'category_id', 'title', 'slug', 'activate_date', 'num_views'))
						->joinLeft(array('c' => $this->_prefix . 'category'), 'a.category_id = c.category_id', array('category_name' => 'name', 'category_slug' => 'slug'));
		if ($categoryId != null && $categoryId != '') {
			$select->where('a.category_id = ?', $categoryId);
		}						
		$select->where('a.status = ?', 'active')
				/**
				 * @since 2.0.8
				 */
				->where('a.language = ?', $this->_lang)
				->order('a.num_views DESC')
				->limit($limit);
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);		
	}
}
