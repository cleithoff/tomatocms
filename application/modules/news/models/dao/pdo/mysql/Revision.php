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

class News_Models_Dao_Pdo_Mysql_Revision extends Tomato_Model_Dao
	implements News_Models_Interface_Revision
{
	public function convert($entity) 
	{
		return new News_Models_Revision($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('r' => $this->_prefix . 'news_article_revision'))
					->where('r.revision_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new News_Models_Revision($row); 
	}
	
	public function add($revision) 
	{
		$this->_conn->insert($this->_prefix . 'news_article_revision', 
							array(
								'article_id'		=> $revision->article_id,
								'category_id' 		=> $revision->category_id,
								'title' 			=> $revision->title,
								'sub_title' 		=> $revision->sub_title,
								'slug' 				=> $revision->slug,
								'description' 		=> $revision->description,
								'content' 			=> $revision->content,
								'created_date' 		=> $revision->created_date,
								'created_user_id' 	=> $revision->created_user_id,
								'created_user_name' => $revision->created_user_name,
								'author' 			=> $revision->author,
								'icons' 			=> $revision->icons,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'news_article_revision');
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('r' => $this->_prefix . 'news_article_revision'));
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('r.article_id = ?', $exp['article_id']);
			}
		}
		$rs = $select->order('r.created_date DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('r' => $this->_prefix . 'news_article_revision'), array('num_revisions' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['article_id'])) {
				$select->where('r.article_id = ?', $exp['article_id']);
			}
		}
		return $select->query()->fetch()->num_revisions;
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'news_article_revision',
									array(
										'revision_id = ?' => $id,
									));
	}	
}
