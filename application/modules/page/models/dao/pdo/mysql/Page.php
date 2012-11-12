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
 * @version 	$Id: Page.php 5342 2010-09-07 09:05:07Z huuphuoc $
 * @since		2.0.7
 */

class Page_Models_Dao_Pdo_Mysql_Page extends Tomato_Model_Dao
	implements Page_Models_Interface_Page
{
	public function convert($entity) 
	{
		return new Page_Models_Page($entity); 
	}
	
	public function getById($id)
	{
		$row = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'page'))
					->where('p.page_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Page_Models_Page($row);
	}
	
	public function add($page)
	{
		/**
		 * Calculate the right Id
		 */
		$parentId = $page->parent_id;
		if ($parentId) {
			$rightId = $this->_conn
							->select()
							->from($this->_prefix . 'page', array('right_id'))
							->where('page_id = ?', $parentId)
							->query()
							->fetch()
							->right_id;
		} else {
			$rightId = $this->_conn
							->select()
							->from($this->_prefix . 'page', array('right_id' => 'MAX(right_id)'))
							->query()
							->fetch()
							->right_id;
			$rightId = $rightId + 1;
		}
		
		if ($rightId != null) {
			$this->_conn->update($this->_prefix . 'page', 
								array(
									'left_id'  => new Zend_Db_Expr('IF(left_id > ' . $this->_conn->quote($rightId) . ', left_id + 2, left_id)'),
									'right_id' => new Zend_Db_Expr('IF(right_id > ' . $this->_conn->quote($rightId) . ', right_id + 2, right_id)'),
								));
			
			$data = array(
				'name'		   => $page->name,
				'slug'		   => $page->slug,
				'description'  => $page->description,
				'content'	   => $page->content,
				'created_date' => $page->created_date,
				'num_views'	   => $page->num_views,
				'user_id'	   => $page->user_id,
				'left_id'	   => $rightId,
				'right_id'	   => $rightId + 1,
				'parent_id'	   => $parentId,
			
				/**
				 * @since 2.0.8
				 */
				'language'     => $page->language,
			);
			
			if (isset($page->page_id) && $page->page_id != null) {
				$data['page_id'] = $page->page_id;
				$this->_conn->insert($this->_prefix . 'page', $data);
				return $page->page_id;
			} else {
				$this->_conn->insert($this->_prefix . 'page', $data);
				return $this->_conn->lastInsertId($this->_prefix . 'page');
			}
		}
	}
	
	public function update($page)
	{
		$this->_conn->update($this->_prefix . 'page', 
							array(
								'name' 			=> $page->name,
								'slug' 			=> $page->slug,
								'description'   => $page->description,
								'content'	    => $page->content,
								'modified_date' => $page->modified_date,
								/**
								 * @since 2.0.8
								 */
								'language'      => $page->language,
							),
							array(
								'page_id = ?' => $page->page_id,
							));
	}
	
	public function updateOrder($page)
	{
		$this->_conn->update($this->_prefix . 'page', 
							array(
								'parent_id' => $page->parent_id,
								'left_id'   => $page->left_id,
								'right_id'  => $page->right_id,
							),
							array(
								'page_id = ?' => $page->page_id,
							));
	}
	
	public function delete($page)
	{
		$this->_conn->delete($this->_prefix . 'page', 
							array(
								'page_id = ?' => $page->page_id,
							));
		
		$this->_conn->update($this->_prefix . 'page',
							array(
								'left_id'  => new Zend_Db_Expr('left_id - 1'),
								'right_id' => new Zend_Db_Expr('right_id - 1'),
							),
							array(
								'left_id >= ?' => $page->left_id,
								'left_id <= ?' => $page->right_id, 
							));
							
		$this->_conn->update($this->_prefix . 'page',
							array(
								'right_id' => new Zend_Db_Expr('right_id - 2'),
							),
							array(
								'right_id > ?' => $page->right_id,
							));
							
		$this->_conn->update($this->_prefix . 'page',
							array(
								'left_id' => new Zend_Db_Expr('left_id - 2'),
							),
							array(
								'left_id > ?' => $page->right_id,
							));	
	}
	
	public function getTree()
	{
		$rs = $this->_conn
					->select()
					->from(array('node' => $this->_prefix . 'page'))
					->from(array('parent' => $this->_prefix . 'page'), array('depth' => '(COUNT(parent.name) - 1)'))
					->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					/**
					 * @since 2.0.8
					 */
					->where('node.language = ?', $this->_lang)
					->where('parent.language = ?', $this->_lang)
					->group('node.page_id')
					->order('node.left_id')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getParents($pageId)
	{
		$rs = $this->_conn
					->select()
					->from(array('node' => $this->_prefix . 'page'), array())
					->from(array('parent' => $this->_prefix . 'page'))
					->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					->where('node.page_id = ?', $pageId)
					->order('parent.left_id')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$rs = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'page'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'),
						'tr.item_class = ?
						AND (tr.item_id = ? OR tr.source_item_id = ?)
						AND (tr.item_id = p.page_id OR tr.source_item_id = p.page_id)',
						array('tr.source_item_id'))
					->group('p.page_id')
					->bind(array(
						'Page_Models_Page',
						$item->page_id,
						$item->page_id,
					))
					->query()
					->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql = 'SELECT p.*, (tr.item_id IS NULL) AS translatable
				FROM
				(
					SELECT node.page_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth, 
						node.left_id, node.right_id, node.parent_id
					FROM ' . $this->_prefix . 'page AS node, 
						' . $this->_prefix . 'page AS parent
					WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
						AND node.language = ?
						AND parent.language = ?
					GROUP BY node.page_id
					ORDER BY node.left_id
				) AS p
				LEFT JOIN ' . $this->_prefix . 'core_translation AS tr
					ON tr.source_item_id = p.page_id
					AND tr.item_class = ?
					AND tr.source_language = ?
					AND tr.language = ?';
				
		$rs  = $this->_conn
					->query($sql, 
							array(
								$this->_lang, $this->_lang,
								'Page_Models_Page', 
								$this->_lang, $lang
							))
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($page)
	{
		$row = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'page'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'), 'p.page_id = tr.source_item_id', array())
					->where('tr.item_id = ?', $page->page_id)
					->where('tr.item_class = ?', 'Page_Models_Page')
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Page_Models_Page($row);
	}	
}
