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
 * @version 	$Id: Category.php 5052 2010-08-28 18:29:07Z huuphuoc $
 * @since		2.0.5
 */

class Category_Models_Dao_Sqlsrv_Category extends Tomato_Model_Dao
	implements Category_Models_Interface_Category
{
	public function convert($entity) 
	{
		return new Category_Models_Category($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'category 
				WHERE category_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Category_Models_Category($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function getSubCategories($categoryId)
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'category 
				WHERE language = ? AND parent_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($this->_lang, $categoryId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($category) 
	{
		$parentId = $category->parent_id;
		if (is_int($parentId)) {
			$sql = 'SELECT TOP 1 right_id FROM ' . $this->_prefix . 'category 
					WHERE category_id = ' . $this->_conn->quote($parentId);
		} else {
			$sql = 'SELECT TOP 1 MAX(right_id) AS right_id FROM ' . $this->_prefix . 'category';
		}
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		$stmt->closeCursor();
		$rightId = ($parentId) ? $row->right_id : $row->right_id + 1;
		
		if ($rightId != null) {
			$sql = 'UPDATE ' . $this->_prefix . 'category
					SET left_id = CASE 
									WHEN left_id > ?
										THEN left_id + 2
									ELSE 
										left_id
								  END, 
						right_id = CASE
									WHEN right_id >= ?
										THEN right_id + 2
									ELSE 
										right_id
								   END';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($rightId, $rightId));
			$stmt->closeCursor();						
											
			$data = array(
				'name'		   => $category->name,
				'slug'		   => $category->slug,
				'meta'		   => $category->meta,
				'created_date' => $category->created_date,
				'user_id'	   => $category->user_id,
				'left_id'	   => $rightId,
				'right_id'	   => $rightId + 1,
				'parent_id'    => $parentId,
			
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
		$sql  = 'UPDATE ' . $this->_prefix . 'category
				SET name = ?, slug = ?, meta = ?, modified_date = ?, language = ?
				WHERE category_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$category->name,
			$category->slug,
			$category->meta,
			$category->modified_date,
			/** 
			 * @since 2.0.8
			 */
		    $category->language, 
			$category->category_id,
		));
		$stmt->closeCursor();
	}
	
	public function delete($category) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'category WHERE category_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($category->category_id));
		
		$sql  = 'UPDATE ' . $this->_prefix . 'category 
				SET left_id = left_id - 1, right_id = right_id - 1
				WHERE left_id BETWEEN ? AND ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($category->left_id, $category->right_id));

		$sql  = 'UPDATE ' . $this->_prefix . 'category
				SET right_id = right_id - 2
				WHERE right_id > ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($category->right_id));
		
		$sql  = 'UPDATE ' . $this->_prefix . 'category 
				SET left_id = left_id - 2
				WHERE left_id > ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($category->right_id));
		$stmt->closeCursor();
	}
	
	public function getTree() 
	{
		$sql  = 'SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth,
					node.left_id, node.right_id
				FROM ' . $this->_prefix . 'category AS node,
					' . $this->_prefix . 'category AS parent
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id 
					AND node.language = ? 
					AND parent.language = ?
				GROUP BY node.category_id, node.name, node.slug, node.left_id, node.right_id
				ORDER BY node.left_id';
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($this->_lang, $this->_lang));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getParents($categoryId)
	{
		$sql  = 'SELECT parent.* 
				FROM ' . $this->_prefix . 'category AS node, ' . $this->_prefix . 'category AS parent
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
					AND node.category_id = ?
				ORDER BY parent.left_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($categoryId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getParentId($categoryId)
	{
		$sql  = 'SELECT c2.category_id
				FROM ' . $this->_prefix . 'category AS c1
				INNER JOIN ' . $this->_prefix . 'category AS c2
					ON c1.left_id BETWEEN c2.left_id AND c2.right_id
				WHERE c1.category_id = ? AND c2.category_id <> ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($categoryId, $categoryId));
		$rs = $stmt->fetch();
		if (null == $rs) {
			return $categoryId;
		}
		$return = $rs->category_id;
		$stmt->closeCursor();
		return $return;
	}
	
	public function updateOrder($category)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'category 
				SET parent_id = ?, left_id = ?, right_id = ?
				WHERE category_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($category->parent_id, $category->left_id, $category->right_id, $category->category_id));
		$stmt->closeCursor();
	}
	
/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = 'SELECT c.* 
				FROM ' . $this->_prefix . 'category AS c
				INNER JOIN 
				(
					SELECT tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language 
					FROM ' . $this->_prefix . 'core_translation AS tr1
					INNER JOIN ' . $this->_prefix . 'core_translation AS tr2 
						ON (tr1.item_id = ? AND tr1.source_item_id = tr2.item_id) 
						OR (tr2.item_id = ? AND tr1.item_id = tr2.source_item_id)
						OR (tr1.source_item_id = ? AND tr1.source_item_id = tr2.source_item_id)
					WHERE tr1.item_class = ? AND tr2.item_class = ?
					GROUP by tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language
				) AS tr
					ON tr.item_id = c.category_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
							$item->category_id, 
							$item->category_id, 
							$item->category_id, 
							'Category_Models_Category', 
							'Category_Models_Category'
							)
						);
		$rs = $stmt->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql  = 'SELECT c.*, ISNULL(tr.item_id, 1) AS translatable
				FROM
				(
					SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth, 
						node.left_id, node.right_id, node.parent_id
					FROM ' . $this->_prefix . 'category AS node, 
						' . $this->_prefix . 'category AS parent
					WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
						AND node.language = ?
						AND parent.language = ?
					GROUP BY node.category_id, node.name, node.slug, node.left_id, node.right_id, node.parent_id
				) AS c
				LEFT JOIN ' . $this->_prefix . 'core_translation AS tr
					ON tr.source_item_id = c.category_id
					AND tr.item_class = ?
					AND tr.source_language = ?
					AND tr.language = ? 
				ORDER BY c.left_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
						$this->_lang, 
						$this->_lang,
						'Category_Models_Category', 
						$this->_lang, 
						$lang));
		$rs = $stmt->fetchAll();		
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($category)
	{
		$sql  = 'SELECT TOP 1 * 
				FROM ' . $this->_prefix . 'category as c 
				INNER JOIN ' . $this->_prefix . 'core_translation AS tr 
					ON c.category_id = tr.source_item_id
				WHERE tr.item_id = ? AND tr.item_class = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($category->category_id, 'Category_Models_Category'));
		$row = $stmt->fetch();
		return (null == $row) ? null : new Category_Models_Category($row);
	}
}
