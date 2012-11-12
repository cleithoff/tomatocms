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
 * @version 	$Id: Page.php 4891 2010-08-24 20:06:55Z huuphuoc $
 * @since		2.0.5
 */

class Page_Models_Dao_Mysql_Page extends Tomato_Model_Dao
	implements Page_Models_Interface_Page
{
	public function convert($entity) 
	{
		return new Page_Models_Page($entity); 
	}
	
	public function getById($id)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "page 
						WHERE page_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Page_Models_Page(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($page)
	{
		/**
		 * Calculate the right Id
		 */
		$parentId = $page->parent_id;
		if ($parentId) {
			$sql = 'SELECT right_id FROM ' . $this->_prefix . 'page WHERE page_id = ' . mysql_real_escape_string($parentId);
		} else {
			$sql = 'SELECT MAX(right_id) AS right_id FROM ' . $this->_prefix . 'page';
		}
		$rs = mysql_query($sql);
		$right   = (0 == mysql_num_rows($rs)) ? null : new Page_Models_Page(mysql_fetch_object($rs));
		$rightId = ($parentId) ? $right->right_id : $right->right_id + 1;
		if ($rightId != null) {
			$sql = 'UPDATE ' . $this->_prefix . 'page
					SET left_id = IF(left_id > ' . mysql_real_escape_string($rightId) . ', left_id + 2, left_id), 
						right_id = IF(right_id >= ' . mysql_real_escape_string($rightId) . ', right_id + 2, right_id)';
			mysql_query($sql);
			
			
							
			if (isset($page->page_id) && $page->page_id != null) {
				$sql = sprintf("INSERT INTO " . $this->_prefix . "page (page_id, name, slug, description, content, created_date,
									num_views, user_id, left_id, right_id, parent_id, language)
								VALUES ('%s', '%s', '%s', '%s', '%s', '%s',
									'%s', '%s', '%s', '%s', '%s', '%s')",
								mysql_real_escape_string($page->page_id),
								mysql_real_escape_string($page->name),
								mysql_real_escape_string($page->slug),
								mysql_real_escape_string($page->description),
								mysql_real_escape_string($page->content),
								mysql_real_escape_string($page->created_date),
								mysql_real_escape_string($page->num_views),
								mysql_real_escape_string($page->user_id),
								$rightId,
								$rightId + 1,
								mysql_real_escape_string($page->parent_id),
								/**
								 * @since 2.0.8
								 */
								mysql_real_escape_string($page->language));
						
				mysql_query($sql);
				return $page->page_id;	
			} else {
				$sql = sprintf("INSERT INTO " . $this->_prefix . "page (name, slug, description, content, created_date,
								num_views, user_id, left_id, right_id, parent_id, language)
								VALUES ('%s', '%s', '%s', '%s', '%s',
									'%s', '%s', '%s', '%s', '%s', '%s')",
								mysql_real_escape_string($page->name),
								mysql_real_escape_string($page->slug),
								mysql_real_escape_string($page->description),
								mysql_real_escape_string($page->content),
								mysql_real_escape_string($page->created_date),
								mysql_real_escape_string($page->num_views),
								mysql_real_escape_string($page->user_id),
								$rightId,
								$rightId + 1,
								mysql_real_escape_string($page->parent_id),
								/**
								 * @since 2.0.8
								 */
								mysql_real_escape_string($page->language));
				
				mysql_query($sql);
				return mysql_insert_id();
			}
		}
	}
	
	public function update($page)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "page
						SET name = '%s', slug = '%s', description = '%s', content = '%s', modified_date = '%s', language = '%s'
						WHERE page_id = '%s'",
						mysql_real_escape_string($page->name),
						mysql_real_escape_string($page->slug),
						mysql_real_escape_string($page->description),
						mysql_real_escape_string($page->content),
						mysql_real_escape_string($page->modified_date),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($page->language),
						mysql_real_escape_string($page->page_id));
		mysql_query($sql);
	}
	
	public function updateOrder($page)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "page
						SET parent_id = '%s', left_id = '%s', right_id = '%s'
						WHERE page_id = '%s'",
						mysql_real_escape_string($page->parent_id),
						mysql_real_escape_string($page->left_id),
						mysql_real_escape_string($page->right_id),
						mysql_real_escape_string($page->page_id));
		mysql_query($sql);
	}
	
	public function delete($page)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "page  
						WHERE page_id = '%s'",
						mysql_real_escape_string($page->page_id));
		mysql_query($sql);
		
		$sql = sprintf("UPDATE " . $this->_prefix . "page
						SET left_id = left_id - 1, right_id = right_id - 1
						WHERE left_id BETWEEN '%s' AND '%s'",
						mysql_real_escape_string($page->left_id),
						mysql_real_escape_string($page->right_id));
		mysql_query($sql);

		$sql = sprintf("UPDATE " . $this->_prefix . "page 
						SET right_id = right_id - 2
						WHERE right_id > '%s'",
						mysql_real_escape_string($page->right_id));
		mysql_query($sql);
		
		$sql = sprintf("UPDATE " . $this->_prefix . "page
						SET left_id = left_id - 2
						WHERE left_id > '%s'",
						mysql_real_escape_string($page->right_id));
		mysql_query($sql);
	}
	
	public function getTree()
	{
		$sql = sprintf("SELECT node.page_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth,
							node.left_id, node.right_id, node.parent_id
						FROM " . $this->_prefix . "page AS node,
							" . $this->_prefix . "page AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.language = '%s'
							AND parent.language = '%s'
						GROUP BY node.page_id
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
	

	public function getParents($pageId)
	{
		$sql = sprintf("SELECT parent.* 
						FROM " . $this->_prefix . "page AS node, " . $this->_prefix . "page AS parent
						WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
							AND node.page_id = '%s'
						ORDER BY parent.left_id",
						mysql_real_escape_string($pageId));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = sprintf('SELECT p.* FROM ' . $this->_prefix . 'page AS p
						INNER JOIN 
						(
							SELECT tr1.* FROM ' . $this->_prefix . 'core_translation AS tr1
							INNER JOIN ' . $this->_prefix . 'core_translation AS tr2 
								ON (tr1.item_id = "%s" AND tr1.source_item_id = tr2.item_id) 
								OR (tr2.item_id = "%s" AND tr1.item_id = tr2.source_item_id)
								OR (tr1.source_item_id = "%s" AND tr1.source_item_id = tr2.source_item_id)
							WHERE tr1.item_class = "%s" AND tr2.item_class = "%s"
							GROUP by tr1.translation_id
						) AS tr
							ON tr.item_id = p.page_id',
						mysql_real_escape_string($item->page_id),
						mysql_real_escape_string($item->page_id),
						mysql_real_escape_string($item->page_id),
						'Page_Models_Page',
						'Page_Models_Page');
		
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
		$sql = sprintf('SELECT p.*, (tr.item_id IS NULL) AS translatable
						FROM
						(
							SELECT node.page_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth, 
								node.left_id, node.right_id, node.parent_id
							FROM ' . $this->_prefix . 'page AS node, 
								' . $this->_prefix . 'page AS parent
							WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
								AND node.language = "%s"
								AND parent.language = "%s"
							GROUP BY node.page_id
							ORDER BY node.left_id
						) AS p
						LEFT JOIN ' . $this->_prefix . 'core_translation AS tr
							ON tr.source_item_id = p.page_id
							AND tr.item_class = "%s"
							AND tr.source_language = "%s"
							AND tr.language = "%s"',
						mysql_real_escape_string($this->_lang),
						mysql_real_escape_string($this->_lang),
						'Page_Models_Page',
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
	
	public function getSource($page)
	{
		$sql = sprintf("SELECT p.* 
						FROM " . $this->_prefix . "page AS p
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON p.page_id = tr.source_item_id
						WHERE tr.item_id = '%s' AND tr.item_class = '%s'
						LIMIT 1",
						mysql_real_escape_string($page->page_id),
						'Page_Models_Page');
		
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Page_Models_Page(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
}
