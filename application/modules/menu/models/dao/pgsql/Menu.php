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
 * @version 	$Id: Menu.php 5450 2010-09-15 09:14:11Z leha $
 * @since		2.0.5
 */

class Menu_Models_Dao_Pgsql_Menu extends Tomato_Model_Dao
	implements Menu_Models_Interface_Menu
{
	public function convert($entity) 
	{
		return new Menu_Models_Menu($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "menu 
						WHERE menu_id = %s
						LIMIT 1",
						pg_escape_string($id));
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Menu_Models_Menu(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($menu) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "menu (name, description, user_id, user_name, created_date, language)
						VALUES ('%s', '%s', %s, '%s', '%s', '%s')
						RETURNING menu_id",
						pg_escape_string($menu->name),
						pg_escape_string($menu->description),
						pg_escape_string($menu->user_id),
						pg_escape_string($menu->user_name),
						pg_escape_string($menu->created_date),
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($menu->language));
						
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->menu_id;
	}
	
	public function update($menu) 
	{
		return pg_update($this->_conn, $this->_prefix . 'menu',
						array(
							'name'		  => $menu->name,
							'description' => $menu->description,

							/**
							 * @since 2.0.8
							 */
							'language'	  => $menu->language,
						),
						array(
							'menu_id' => $menu->menu_id,
						));
	}
	
	public function delete($id) 
	{
		$where['menu_id'] = $id;
		
		/**
		 * @since 2.0.7
		 */
		pg_delete($this->_conn, $this->_prefix . 'menu_item', $where);
		
		return pg_delete($this->_conn, $this->_prefix . 'menu', $where);
	}
	
	public function getMenus($offset = null, $count = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "menu AS m
						WHERE m.language = '%s'
						ORDER BY menu_id DESC",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s OFFSET %s", $count, $offset);
		}		
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count() 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_menus FROM " . $this->_prefix . "menu AS m
						WHERE m.language = '%s' 
						LIMIT 1",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
						
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_menus;
	}
		
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = sprintf("SELECT m.* FROM " . $this->_prefix . "menu AS m
						INNER JOIN 
						(
							SELECT tr1.translation_id, MAX(tr1.item_id) AS item_id, MAX(tr1.item_class) AS item_class, MAX(tr1.source_item_id) AS source_item_id, MAX(tr1.language) AS language, MAX(tr1.source_language) AS source_language FROM " . $this->_prefix . "core_translation AS tr1
							INNER JOIN " . $this->_prefix . "core_translation AS tr2 
								ON (tr1.item_id = %s AND tr1.source_item_id = tr2.item_id) 
								OR (tr2.item_id = %s AND tr1.item_id = tr2.source_item_id)
								OR (tr1.source_item_id = %s AND tr1.source_item_id = tr2.source_item_id)
							WHERE tr1.item_class = '%s' AND tr2.item_class = '%s'
							GROUP by tr1.translation_id
						) AS tr
							ON tr.item_id = m.menu_id",
						pg_escape_string($item->menu_id),
						pg_escape_string($item->menu_id),
						pg_escape_string($item->menu_id),
						'Menu_Models_Menu', 
						'Menu_Models_Menu');

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
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "menu AS m
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON m.menu_id = tr.source_item_id
							AND tr.item_class = '%s' 
							AND tr.language = '%s'
						WHERE m.language = '%s'",
						'Menu_Models_Menu',
						pg_escape_string($lang),
						pg_escape_string($this->_lang));
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($menu)
	{
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "menu AS m
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON m.menu_id = tr.source_item_id
						WHERE tr.item_id = %s 
							AND tr.item_class = '%s'",
						pg_escape_string($menu->menu_id),
						'Menu_Models_Menu');
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Category_Models_Category(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
}
