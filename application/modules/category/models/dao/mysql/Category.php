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
 * @version 	$Id: Category.php 4955 2010-08-25 18:06:13Z huuphuoc $
 * @since		2.0.5
 */

class Category_Models_Dao_Mysql_Category extends Tomato_Model_Dao
	implements Category_Models_Interface_Category
{
	public function convert($entity) 
	{
		return new Category_Models_Category($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "category 
						WHERE category_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Category_Models_Category(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function getSubCategories($categoryId) 
	{
		$sql  = sprintf("SELECT * FROM " . $this->_prefix . "category 
						WHERE parent_id = '%s' AND language = '%s'",
						mysql_real_escape_string($categoryId),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function add($category) 
	{
		$parentId = $category->parent_id;
		if ($parentId) {
			$sql = sprintf("SELECT right_id FROM " . $this->_prefix . "category 
							WHERE category_id = '%s'
							LIMIT 1",
							mysql_real_escape_string($parentId));
		} else {
			$sql = "SELECT MAX(right_id) AS right_id FROM " . $this->_prefix . "category LIMIT 1";
		}
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		$rightId = ($parentId) ? $row->right_id : $row->right_id + 1;
		
		if ($rightId != null) {
			$sql = sprintf("UPDATE " . $this->_prefix . "category
							SET left_id = IF(left_id > %s, left_id + 2, left_id), 
								right_id = IF(right_id >= %s, right_id + 2, right_id)", 
							mysql_real_escape_string($rightId),
							mysql_real_escape_string($rightId));
			mysql_query($sql);							
			
			/**
			 * Keep the category Id in case user update its parent
			 * See Category_CategoryController::editAction()
			 */
			if (isset($category->category_id) && $category->category_id != null) {
				$sql = sprintf("INSERT INTO " . $this->_prefix . "category (category_id, name, slug, meta, created_date, user_id, left_id, right_id, parent_id, language)
								VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
								mysql_real_escape_string($category->category_id),
								mysql_real_escape_string($category->name),
								mysql_real_escape_string($category->slug),
								mysql_real_escape_string($category->meta),
								mysql_real_escape_string($category->created_date),
								mysql_real_escape_string($category->user_id),
								mysql_real_escape_string($rightId),
								mysql_real_escape_string($rightId + 1),
								mysql_real_escape_string($parentId),
								/**
								 * @since 2.0.8
								 */
								mysql_real_escape_string($category->language));
							
				mysql_query($sql);
				return $category->category_id;				
			} else {
				$sql = sprintf("INSERT INTO " . $this->_prefix . "category (name, slug, meta, created_date, user_id, left_id, right_id, parent_id, language)
								VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
								mysql_real_escape_string($category->name),
								mysql_real_escape_string($category->slug),
								mysql_real_escape_string($category->meta),
								mysql_real_escape_string($category->created_date),
								mysql_real_escape_string($category->user_id),
								mysql_real_escape_string($rightId),
								mysql_real_escape_string($rightId + 1),
								mysql_real_escape_string($parentId),
								/**
								 * @since 2.0.8
								 */
								mysql_real_escape_string($category->language));
				
				mysql_query($sql);
				return mysql_insert_id();
			}
		}
	}
	
	public function update($category) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "category
						SET name = '%s', slug = '%s', meta = '%s', modified_date = '%s', language = '%s'
						WHERE category_id = '%s'",
						mysql_real_escape_string($category->name),
						mysql_real_escape_string($category->slug),
						mysql_real_escape_string($category->meta),
						mysql_real_escape_string($category->modified_date),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($category->language),
						mysql_real_escape_string($category->category_id));
		mysql_query($sql);
	}
	
	public function delete($category) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "category
						WHERE category_id = '%s'", 
						mysql_real_escape_string($category->category_id));
		mysql_query($sql);
		
		$sql = sprintf("UPDATE " . $this->_prefix . "category 
						SET left_id = left_id - 1, right_id = right_id - 1
						WHERE left_id BETWEEN %s AND %s",
						mysql_real_escape_string($category->left_id), 
						mysql_real_escape_string($category->right_id));
		mysql_query($sql);

		$sql = sprintf("UPDATE " . $this->_prefix . "category
						SET right_id = right_id - 2
						WHERE right_id > %s",
						mysql_real_escape_string($category->right_id));
		mysql_query($sql);
		
		$sql = sprintf("UPDATE " . $this->_prefix . "category 
						SET left_id = left_id - 2
						WHERE left_id > %s",
						mysql_real_escape_string($category->right_id));
		mysql_query($sql);
	}
	
	public function getTree() 
	{
		$sql  = sprintf("SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth,
							node.left_id, node.right_id
						FROM " . $this->_prefix . "category AS node,
							" . $this->_prefix . "category AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.language = '%s' 
							AND parent.language = '%s'
						GROUP BY node.category_id
						ORDER BY node.left_id",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang),
						mysql_real_escape_string($this->_lang));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getParents($categoryId)
	{
		$sql  = sprintf("SELECT parent.* 
						FROM " . $this->_prefix . "category AS node, " . $this->_prefix . "category AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.category_id = %s
						ORDER BY parent.left_id",
						mysql_real_escape_string($categoryId));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getParentId($categoryId)
	{
		$sql = sprintf("SELECT c2.category_id
						FROM " . $this->_prefix . "category AS c1
						INNER JOIN " . $this->_prefix . "category AS c2
							ON c1.left_id BETWEEN c2.left_id AND c2.right_id
						WHERE c1.category_id = %s AND c2.category_id <> %s",
						mysql_real_escape_string($categoryId),
						mysql_real_escape_string($categoryId));
						
		$rs  = mysql_query($sql);
		if (0 == mysql_num_rows($rs)) {
			return $categoryId;
		}
		$row = mysql_fetch_object($rs);
		$return = $row->category_id;
		mysql_free_result($rs);
		return $return;		
	}
	
	public function updateOrder($category)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "category
						SET parent_id = %s, left_id = %s, right_id = %s
						WHERE category_id = %s",
						mysql_real_escape_string($category->parent_id),
						mysql_real_escape_string($category->left_id),
						mysql_real_escape_string($category->right_id),
						mysql_real_escape_string($category->category_id));
		mysql_query($sql);
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = sprintf("SELECT c.* FROM " . $this->_prefix . "category AS c
						INNER JOIN 
						(
							SELECT tr1.* FROM " . $this->_prefix . "core_translation AS tr1
							INNER JOIN " . $this->_prefix . "core_translation AS tr2 
								ON (tr1.item_id = '%s' AND tr1.source_item_id = tr2.item_id) 
								OR (tr2.item_id = '%s' AND tr1.item_id = tr2.source_item_id)
								OR (tr1.source_item_id = '%s' AND tr1.source_item_id = tr2.source_item_id)
							WHERE tr1.item_class = '%s' AND tr2.item_class = '%s'
							GROUP by tr1.translation_id
						) AS tr
							ON tr.item_id = c.category_id", 
						mysql_real_escape_string($item->category_id), 
						mysql_real_escape_string($item->category_id), 
						mysql_real_escape_string($item->category_id),
						'Category_Models_Category',
						'Category_Models_Category');
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql = sprintf("SELECT c.*, (tr.item_id IS NULL) AS translatable
						FROM
						(
							SELECT node.category_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth, 
								node.left_id, node.right_id, node.parent_id
							FROM " . $this->_prefix . "category AS node, 
								" . $this->_prefix . "category AS parent
							WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
								AND node.language = '%s'
								AND parent.language = '%s'
							GROUP BY node.category_id
							ORDER BY node.left_id
						) AS c
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON tr.source_item_id = c.category_id
							AND tr.item_class = '%s'
							AND tr.source_language = '%s'
							AND tr.language = '%s'",
						mysql_real_escape_string($this->_lang), 
						mysql_real_escape_string($this->_lang), 
						'Category_Models_Category',
						mysql_real_escape_string($this->_lang),
						mysql_real_escape_string($lang));
							
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($category)
	{
		$sql = sprintf("SELECT c.* 
						FROM " . $this->_prefix . "category AS c
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON c.category_id = tr.source_item_id
						WHERE tr.item_id = '%s' AND tr.item_class = '%s'
						LIMIT 1", 
						mysql_real_escape_string($category->category_id),
						'Category_Models_Category');
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Category_Models_Category(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
}
