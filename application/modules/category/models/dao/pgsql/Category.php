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
 * @version 	$Id: Category.php 5419 2010-09-14 08:16:27Z leha $
 * @since		2.0.5
 */

class Category_Models_Dao_Pgsql_Category extends Tomato_Model_Dao
	implements Category_Models_Interface_Category
{
	public function convert($entity) 
	{
		return new Category_Models_Category($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf('SELECT * FROM ' . $this->_prefix . 'category 
						WHERE category_id = %s
						LIMIT 1', 
						pg_escape_string($id));
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Category_Models_Category(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function getSubCategories($categoryId) 
	{
		$sql  = sprintf("SELECT * FROM " . $this->_prefix . "category 
						WHERE parent_id = %s AND language = '%s'", 
						($categoryId) ? pg_escape_string($categoryId) : '0',
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($category) 
	{
		$parentId = $category->parent_id;
		if ($parentId) {
			$sql = sprintf('SELECT right_id FROM ' . $this->_prefix . 'category 
							WHERE category_id = %s
							LIMIT 1',
							pg_escape_string($parentId));
		} else {
			$sql = 'SELECT MAX(right_id) AS right_id FROM ' . $this->_prefix . 'category LIMIT 1';
		}
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		$rightId = ($parentId) ? $row->right_id : $row->right_id + 1;
		
		if ($rightId != null) {
			$sql = sprintf('UPDATE ' . $this->_prefix . 'category
							SET left_id = CASE WHEN left_id > %s THEN left_id + 2 ELSE left_id END, 
								right_id = CASE WHEN right_id >= %s THEN right_id + 2 ELSE right_id END', 
							pg_escape_string($rightId),
							pg_escape_string($rightId));
			pg_query($sql);							
			
			/**
			 * Keep the category Id in case user update its parent
			 * See Category_CategoryController::editAction()
			 */
			if (isset($category->category_id) && $category->category_id != null) {
				$sql = sprintf("INSERT INTO " . $this->_prefix . "category (name, slug, meta, created_date, user_id, left_id, right_id, parent_id, category_id, language)
								VALUES ('%s', '%s', '%s', %s, %s, %s, %s, %s, %s, '%s')
								RETURNING category_id",
								pg_escape_string($category->name),
								pg_escape_string($category->slug),
								pg_escape_string($category->meta),
								($category->created_date) ? "'" . pg_escape_string($category->created_date) . "'" : 'null',
								($category->user_id) ? pg_escape_string($category->user_id) : 'null',
								pg_escape_string($rightId),
								pg_escape_string($rightId + 1),
								pg_escape_string($parentId),
								pg_escape_string($category->category_id),
								/**
								 * @since 2.0.8
								 */
								pg_escape_string($category->language));
								
				$rs  = pg_query($sql);
				$row = pg_fetch_object($rs);
				pg_free_result($rs);		
				return $category->category_id;
			} else {
				$sql = sprintf("INSERT INTO " . $this->_prefix . "category (name, slug, meta, created_date, user_id, left_id, right_id, parent_id, language)
								VALUES ('%s', '%s', '%s', %s, %s, %s, %s, %s, '%s')
								RETURNING category_id",
								pg_escape_string($category->name),
								pg_escape_string($category->slug),
								pg_escape_string($category->meta),
								($category->created_date) ? "'" . pg_escape_string($category->created_date) . "'" : 'null',
								($category->user_id) ? pg_escape_string($category->user_id) : 'null',
								pg_escape_string($rightId),
								pg_escape_string($rightId + 1),
								pg_escape_string($parentId),								
								/**
								 * @since 2.0.8
								 */
								pg_escape_string($category->language));
								
				$rs  = pg_query($sql);
				$row = pg_fetch_object($rs);
				pg_free_result($rs);
				return $row->category_id;
			}
		}
	}
	
	public function update($category) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "category
						SET name = '%s', slug = '%s', meta = '%s', modified_date = '%s', language = '%s'
						WHERE category_id = %s",
						pg_escape_string($category->name),
						pg_escape_string($category->slug),
						pg_escape_string($category->meta),
						pg_escape_string($category->modified_date),
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($category->language),
						pg_escape_string($category->category_id));
		pg_query($sql);
	}
	
	public function delete($category) 
	{
		pg_delete($this->_conn, $this->_prefix . 'category', 
					array(
						'category_id' => $category->category_id,
					));
		
		$sql = sprintf('UPDATE ' . $this->_prefix . 'category 
						SET left_id = left_id - 1, right_id = right_id - 1
						WHERE left_id BETWEEN %s AND %s',
						pg_escape_string($category->left_id), 
						pg_escape_string($category->right_id));
		pg_query($sql);

		$sql = sprintf('UPDATE ' . $this->_prefix . 'category
						SET right_id = right_id - 2
						WHERE right_id > %s',
						pg_escape_string($category->right_id));
		pg_query($sql);
		
		$sql = sprintf('UPDATE ' . $this->_prefix . 'category 
						SET left_id = left_id - 2
						WHERE left_id > %s',
						pg_escape_string($category->right_id));
		pg_query($sql);
	}
	
	public function getTree() 
	{
		$sql  = sprintf("SELECT node.category_id, MAX(node.name) AS name, MAX(node.slug) AS slug, (COUNT(parent.name) - 1) AS depth,
							MAX(node.left_id) AS left_id, MAX(node.right_id) AS right_id
						FROM " . $this->_prefix . "category AS node,
								" . $this->_prefix . "category AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.language = '%s'
							AND parent.language = '%s'
						GROUP BY node.category_id
						ORDER BY MAX(node.left_id)",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang),
						pg_escape_string($this->_lang));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getParents($categoryId)
	{		
		$categoryId = ($categoryId) ? $categoryId : 0;
		$sql  = sprintf('SELECT parent.* FROM ' . $this->_prefix . 'category AS node, ' . $this->_prefix . 'category AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.category_id = %s
						ORDER BY parent.left_id',
						pg_escape_string($categoryId));
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getParentId($categoryId)
	{
		$sql = sprintf('SELECT c2.category_id
						FROM ' . $this->_prefix . 'category AS c1
						INNER JOIN ' . $this->_prefix . 'category AS c2
							ON c1.left_id BETWEEN c2.left_id AND c2.right_id
						WHERE c1.category_id = %s AND c2.category_id <> %s',
						pg_escape_string($categoryId),
						pg_escape_string($categoryId));
		$rs  = pg_query($sql);
		if (0 == pg_num_rows($rs)) {
			return $categoryId;
		}
		$row = pg_fetch_object($rs);
		$return = $row->category_id;
		pg_free_result($rs);
		return $return;		
	}
	
	public function updateOrder($category)
	{
		pg_update($this->_conn, $this->_prefix . 'category', 
					array(
						'parent_id' => $category->parent_id,
						'left_id'   => $category->left_id,
						'right_id'  => $category->right_id,
					), 
					array(
						'category_id' => $category->category_id,
					));
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = sprintf("SELECT c.* FROM " . $this->_prefix . "category AS c
						INNER JOIN 
						(
							SELECT tr1.translation_id, MAX(tr1.item_id) AS item_id, 
								MAX(tr1.item_class) AS item_class, MAX(tr1.source_item_id) AS source_item_id, 
								MAX(tr1.language) AS language, MAX(tr1.source_language) AS source_language 
							FROM " . $this->_prefix . "core_translation AS tr1
							INNER JOIN " . $this->_prefix . "core_translation AS tr2 
								ON (tr1.item_id = %s AND tr1.source_item_id = tr2.item_id) 
								OR (tr2.item_id = %s AND tr1.item_id = tr2.source_item_id)
								OR (tr1.source_item_id = %s AND tr1.source_item_id = tr2.source_item_id)
							WHERE tr1.item_class = '%s' AND tr2.item_class = '%s' 
							GROUP by tr1.translation_id
						) AS tr
							ON tr.item_id = c.category_id",
						pg_escape_string($item->category_id),
						pg_escape_string($item->category_id),
						pg_escape_string($item->category_id),
						'Category_Models_Category','Category_Models_Category');
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql  = sprintf("SELECT c.*, (tr.item_id IS NULL) AS translatable
						FROM
						(	SELECT node.category_id, MAX(node.name) AS name, MAX(node.slug) AS slug, (COUNT(parent.name) - 1) AS depth, 
								MAX(node.left_id) AS left_id, MAX(node.right_id) AS right_id, MAX(node.parent_id) AS parent_id
							FROM " . $this->_prefix . "category AS node, 
								" . $this->_prefix . "category AS parent
							WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
								AND node.language = '%s'
								AND parent.language = '%s'
							GROUP BY node.category_id
							ORDER BY MAX(node.left_id)
						) AS c
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON tr.source_item_id = c.category_id
							AND tr.item_class = '%s'
							AND tr.source_language = '%s'
							AND tr.language = '%s'",
						pg_escape_string($this->_lang),
						pg_escape_string($this->_lang),
						'Category_Models_Category',
						pg_escape_string($this->_lang),
						pg_escape_string($lang));
				
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($category)
	{
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "category AS c
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON c.category_id = tr.source_item_id
						WHERE tr.item_id = %s 
							AND tr.item_class = '%s'",
						pg_escape_string($category->category_id),
						'Category_Models_Category');
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Category_Models_Category(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
}
