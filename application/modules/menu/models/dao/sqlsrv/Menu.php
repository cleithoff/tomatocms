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
 * @version 	$Id: Menu.php 5040 2010-08-28 17:46:09Z huuphuoc $
 * @since		2.0.5
 */

class Menu_Models_Dao_Sqlsrv_Menu extends Tomato_Model_Dao
	implements Menu_Models_Interface_Menu
{
	public function convert($entity) 
	{
		return new Menu_Models_Menu($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'menu 
				WHERE menu_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Menu_Models_Menu($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($menu) 
	{
		$this->_conn->insert($this->_prefix . 'menu', array(
			'name'		   => $menu->name,
			'description'  => $menu->description,
			'user_id'	   => $menu->user_id,
			'user_name'	   => $menu->user_name,
			'created_date' => $menu->created_date,
			/**
			 * @since 2.0.8
			 */
			'language'	   => $menu->language,
			));
		return $this->_conn->lastInsertId($this->_prefix . 'menu');
	}
	
	public function update($menu) 
	{
		$sql = 'UPDATE ' . $this->_prefix . 'menu
				SET name = ?, description = ?, language = ?
				WHERE menu_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$menu->name,
			$menu->description,
			/**
			 * @since 2.0.8
			 */
			$menu->language,
			$menu->menu_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'menu WHERE menu_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getMenus($offset = null, $count = null)
	{
		$sql = 'SELECT * FROM ' . $this->_prefix . 'menu AS m WHERE m.language = ?';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$sql .= ' ORDER BY menu_id DESC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($this->_lang));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count() 
	{
		$sql  = 'SELECT TOP 1 COUNT(*) AS num_menus FROM ' . $this->_prefix . 'menu AS m WHERE m.language = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($this->_lang));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_menus;
	}
	
/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = 'SELECT m.* FROM ' . $this->_prefix . 'menu AS m
				INNER JOIN 
				(
					SELECT tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language 
					FROM ' . $this->_prefix . 'core_translation AS tr1
					INNER JOIN ' . $this->_prefix . 'core_translation AS tr2 
						ON (tr1.item_id = ? AND tr1.source_item_id = tr2.item_id) 
						OR (tr2.item_id = ? AND tr1.item_id = tr2.source_item_id)
						OR (tr1.source_item_id = ? AND tr1.source_item_id = tr2.source_item_id)
					WHERE tr1.item_class = ? AND tr2.item_class = ?
					GROUP BY tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language
				) AS tr
					ON tr.item_id = m.menu_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
							$item->menu_id, $item->menu_id, $item->menu_id, 
							'Menu_Models_Menu', 'Menu_Models_Menu',
						));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return (null == $rows) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "menu AS m
				LEFT JOIN " . $this->_prefix . "core_translation AS tr
					ON tr.source_item_id = m.menu_id
					AND tr.item_class = ? 
					AND tr.language = ? AND tr.item_id IS NULL
				WHERE m.language = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array('Menu_Models_Menu', $lang, $this->_lang));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($menu)
	{
		$sql  = "SELECT TOP 1 * FROM " . $this->_prefix . "menu AS m 
				LEFT JOIN " . $this->_prefix . "core_translation AS tr
					ON m.menu_id = tr.source_item_id
				WHERE tr.item_id = ? AND tr.item_class = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($menu->menu_id, 'Menu_Models_Menu'));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Menu_Models_Menu($row);
	}
}
