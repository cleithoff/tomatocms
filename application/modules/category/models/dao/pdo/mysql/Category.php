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
 * @version 	$Id: Category.php 5341 2010-09-07 09:04:47Z huuphuoc $
 * @since		2.0.5
 */

class Category_Models_Dao_Pdo_Mysql_Category extends Tomato_Model_Dao
	implements Category_Models_Interface_Category
{
	public function convert($entity) 
	{
		return new Category_Models_Category($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'category'))
					->where('c.category_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Category_Models_Category($row);
	}
	
	public function getSubCategories($categoryId) 
	{
		$rs = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'category'))
					/**
					 * @since 2.0.8
					 */
					->where('c.language = ?', $this->_lang)
					->where('c.parent_id = ?', $categoryId)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function add($category) 
	{
		$parentId = $category->parent_id;
		
		if ($parentId) {
			$rightId = $this->_conn
							->select()
							->from($this->_prefix . 'category', array('right_id'))
							->where('category_id = ?', $parentId)
							->query()
							->fetch()
							->right_id;
		} else {
			$rightId = $this->_conn
							->select()
							->from($this->_prefix . 'category', array('right_id' => 'MAX(right_id)'))
							->query()
							->fetch()
							->right_id;
			$rightId = $rightId + 1;
		}
		
		if ($rightId != null) {
			$this->_conn->update($this->_prefix . 'category', 
								array(
									'left_id'  => new Zend_Db_Expr('IF(left_id > ' . $this->_conn->quote($rightId) . ', left_id + 2, left_id)'),
									'right_id' => new Zend_Db_Expr('IF(right_id > ' . $this->_conn->quote($rightId) . ', right_id + 2, right_id)'),
								));
			
			$data = array(
				'name'		   => $category->name,
				'slug'		   => $category->slug,
				'meta'		   => $category->meta,
				'created_date' => $category->created_date,
				'user_id'	   => $category->user_id,
				'left_id'	   => $rightId,
				'right_id'	   => $rightId + 1,
				'parent_id'	   => $parentId,
			
				/**
				 * @since 2.0.8
				 */
				'language'     => $category->language,
			);
			
			/**
			 * Keep the category Id in case user update its parent
			 * See Category_CategoryController::editAction()
			 */
			if (isset($category->category_id) && $category->category_id != null) {
				$data['category_id'] = $category->category_id;
				$this->_conn->insert($this->_prefix . 'category', $data);
				return $category->category_id;				
			} else {
				$this->_conn->insert($this->_prefix . 'category', $data);
				return $this->_conn->lastInsertId($this->_prefix . 'category');
			}
		}
	}
	
	public function update($category) 
	{
		$this->_conn->update($this->_prefix . 'category', 
							array(
								'name' 			=> $category->name,
								'slug' 			=> $category->slug,
								'meta' 			=> $category->meta,
								'modified_date' => $category->modified_date,
		
								/**
								 * @since 2.0.8
								 */
								'language'      => $category->language,
							), 
							array(
								'category_id = ?' => $category->category_id,
							));
	}
	
	public function delete($category) 
	{
		$this->_conn->delete($this->_prefix . 'category', 
							array(
								'category_id = ?' => $category->category_id,
							));
		
		$this->_conn->update($this->_prefix . 'category',
							array(
								'left_id'  => new Zend_Db_Expr('left_id - 1'),
								'right_id' => new Zend_Db_Expr('right_id - 1'),
							),
							array(
								'left_id >= ?' => $category->left_id,
								'left_id <= ?' => $category->right_id, 
							));
							
		$this->_conn->update($this->_prefix . 'category',
							array(
								'right_id' => new Zend_Db_Expr('right_id - 2'),
							),
							array(
								'right_id > ?' => $category->right_id,
							));
							
		$this->_conn->update($this->_prefix . 'category',
							array(
								'left_id' => new Zend_Db_Expr('left_id - 2'),
							),
							array(
								'left_id > ?' => $category->right_id,
							));						
	}
	
	public function getTree()
	{
		$rs = $this->_conn
					->select()
					->from(array('node' => $this->_prefix . 'category'))
					->from(array('parent' => $this->_prefix . 'category'), array('depth' => '(COUNT(parent.name) - 1)'))
					->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					/**
					 * @since 2.0.8
					 */
					->where('node.language = ?', $this->_lang)
					->where('parent.language = ?', $this->_lang)
					->group('node.category_id')
					->order('node.left_id')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getParents($categoryId)
	{
		$rs = $this->_conn
					->select()
					->from(array('node' => $this->_prefix . 'category'), array())
					->from(array('parent' => $this->_prefix . 'category'))
					->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					->where('node.category_id = ?', $categoryId)
					->order('parent.left_id')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getParentId($categoryId)
	{
		$rs = $this->_conn
					->select()
					->from(array('c1' => $this->_prefix . 'category'), array())
					->joinInner(array('c2' => $this->_prefix . 'category'), 'c1.left_id BETWEEN c2.left_id AND c2.right_id', array('category_id'))
					->where('c1.category_id = ?', $categoryId)
					->where('c2.category_id <> ?', $categoryId)
					->query()
					->fetchAll();
		return (count($rs) == 0) ? $categoryId : $rs[0]->category_id;		
	}
	
	public function updateOrder($category)
	{
		$this->_conn->update($this->_prefix . 'category', 
							array(
								'parent_id' => $category->parent_id,
								'left_id'   => $category->left_id,
								'right_id'  => $category->right_id,
							), 
							array(
								'category_id = ?' => $category->category_id,
							));
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$rs = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'category'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'),
						'tr.item_class = ?
						AND (tr.item_id = ? OR tr.source_item_id = ?)
						AND (tr.item_id = c.category_id OR tr.source_item_id = c.category_id)',
						array('tr.source_item_id'))
					->group('c.category_id')
					->bind(array(
						'Category_Models_Category',
						$item->category_id,
						$item->category_id,
					))
					->query()
					->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql = 'SELECT c.*, (tr.item_id IS NULL) AS translatable
				FROM
				(
					SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth, 
						node.left_id, node.right_id, node.parent_id
					FROM ' . $this->_prefix . 'category AS node, 
						' . $this->_prefix . 'category AS parent
					WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
						AND node.language = ?
						AND parent.language = ?
					GROUP BY node.category_id
					ORDER BY node.left_id
				) AS c
				LEFT JOIN ' . $this->_prefix . 'core_translation AS tr
					ON tr.source_item_id = c.category_id
					AND tr.item_class = ?
					AND tr.source_language = ?
					AND tr.language = ?';
				
		$rs  = $this->_conn
					->query($sql, 
							array(
								$this->_lang, $this->_lang,
								'Category_Models_Category', 
								$this->_lang, $lang
							))
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($category)
	{
		$row = $this->_conn
					->select()
					->from(array('c' => $this->_prefix . 'category'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'), 'c.category_id = tr.source_item_id', array())
					->where('tr.item_id = ?', $category->category_id)
					->where('tr.item_class = ?', 'Category_Models_Category')
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Category_Models_Category($row);
	}
}
