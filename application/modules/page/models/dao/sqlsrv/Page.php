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
 * @version 	$Id: Page.php 5053 2010-08-28 18:29:22Z huuphuoc $
 * @since		2.0.7
 */

class Page_Models_Dao_Sqlsrv_Page extends Tomato_Model_Dao
	implements Page_Models_Interface_Page
{
	public function convert($entity) 
	{
		return new Page_Models_Page($entity); 
	}
	
	public function getById($id)
	{
		$sql  = 'SELECT TOP 1 p.* FROM '.$this->_prefix.'page AS p 
				WHERE p.page_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Page_Models_Page($row);
	}
	
	public function add($page)
	{
		/**
		 * Calculate the right Id
		 */
		$parentId = $page->parent_id;
		if ($parentId) {
			$sql = 'SELECT right_id FROM ' . $this->_prefix . 'page WHERE page_id = ' . $this->_conn->quote($parentId);
		} else {
			$sql = 'SELECT MAX(right_id) AS right_id FROM ' . $this->_prefix . 'page';
		}
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$right = $stmt->fetch();
		$stmt->closeCursor();
		$rightId = ($parentId) ? $right->right_id : $right->right_id + 1;
		if ($rightId != null) {
			$sql  = 'UPDATE ' . $this->_prefix . 'page
					SET left_id = CASE WHEN left_id > ? THEN left_id + 2 ELSE left_id END, 
						right_id = CASE WHEN right_id >= ? THEN right_id + 2 ELSE right_id END';
			$stmt = $this->_conn->prepare($sql, array($rightId, $rightId));
			$stmt->execute();
			$stmt->closeCursor();
			
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
				'parent_id'	   => $page->parent_id,
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
		$sql = 'UPDATE ' . $this->_prefix . 'page 
				SET name = ?, slug = ?, description = ?, content = ?, modified_date = ?, language = ? 
				WHERE page_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$page->name,
			$page->slug,
			$page->description,
			$page->content,
			$page->modified_date,
			/**
			 * @since 2.0.8
			 */
			$page->language,
			$page->page_id,
		));
		$stmt->closeCursor();
	}
	
	public function updateOrder($page)
	{
		$sql  = 'UPDATE '. $this->_prefix . 'page 
				SET parent_id = ?, left_id = ?, right_id = ?
				WHERE page_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$page->parent_id,
			$page->left_id,
			$page->right_id,
			$page->page_id,
		));
		$stmt->closeCursor();
	}
	
	public function delete($page)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'page WHERE page_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($page->page_id));
		$stmt->closeCursor(); 
		
		$sql  = 'UPDATE ' . $this->_prefix . 'page
				SET left_id = left_id - 1, right_id = right_id - 1
				WHERE left_id BETWEEN ? AND ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($page->left_id, $page->right_id));
		$stmt->closeCursor(); 
		
		$sql  = 'UPDATE ' . $this->_prefix . 'page 
				SET right_id = right_id - 2
				WHERE right_id > ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($page->right_id));
		$stmt->closeCursor(); 
				
		$sql  = 'UPDATE ' . $this->_prefix . 'page 
				SET left_id = left_id - 2
				WHERE left_id > ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($page->right_id));
		$stmt->closeCursor(); 
	}
	
	public function getTree()
	{
		$sql  = 'SELECT node.page_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth,
					node.left_id, node.right_id, node.parent_id
				FROM ' . $this->_prefix . 'page AS node,
					' . $this->_prefix . 'page AS parent
				WHERE node.left_id BETWEEN parent.left_id 
					AND parent.right_id 
					AND node.language = ? 
					AND parent.language = ?
				GROUP BY node.page_id, node.name, node.slug, node.left_id, node.right_id, node.parent_id
				ORDER BY node.left_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($this->_lang, $this->_lang));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getParents($pageId)
	{
		$sql  = 'SELECT parent.* FROM ' . $this->_prefix . 'page AS node, ' . $this->_prefix . 'page AS parent
				WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
					AND node.page_id = ?
				ORDER BY parent.left_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($pageId));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = 'SELECT p.* 
				FROM ' . $this->_prefix . 'page AS p
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
					ON tr.item_id = p.page_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($item->page_id, $item->page_id, $item->page_id, 
							'Page_Models_Page', 'Page_Models_Page'));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql  = 'SELECT p.*, ISNULL(tr.item_id, 1) AS translatable
				FROM
				(
					SELECT node.page_id, node.name, node.slug, (COUNT(parent.name) - 1) AS depth, 
						node.left_id, node.right_id, node.parent_id
					FROM ' . $this->_prefix . 'page AS node, 
						' . $this->_prefix . 'page AS parent
					WHERE node.left_id BETWEEN parent.left_id AND parent.right_id
						AND node.language = ?
						AND parent.language = ?
					GROUP BY node.page_id, node.name, node.slug, node.left_id, node.right_id, node.parent_id
				) AS p
				LEFT JOIN ' . $this->_prefix . 'core_translation AS tr
					ON tr.source_item_id = p.page_id
					AND tr.item_class = ?
					AND tr.source_language = ?
					AND tr.language = ?
				ORDER BY p.left_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
						$this->_lang, 
						$this->_lang,
						'Page_Models_Page', 
						$this->_lang, 
						$lang,
					));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($page)
	{
		$sql  = "SELECT TOP 1 * FROM " . $this->_prefix . "page AS p 
				INNER JOIN " . $this->_prefix . "core_translation AS tr
					ON p.page_id = tr.source_item_id
				WHERE tr.item_id = ? AND tr.item_class = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($page->page_id, 'Page_Models_Page'));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Page_Models_Page($row);
	}	
}
