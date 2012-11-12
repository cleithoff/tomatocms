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
 * @version 	$Id: Menu.php 5218 2010-08-30 18:25:31Z huuphuoc $
 * @since		2.0.5
 */

class Menu_Models_Dao_Mysql_Menu extends Tomato_Model_Dao
	implements Menu_Models_Interface_Menu
{
	public function convert($entity) 
	{
		return new Menu_Models_Menu($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "menu 
						WHERE menu_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Menu_Models_Menu(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($menu) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "menu (name, description, user_id, user_name, created_date, language)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($menu->name),
						mysql_real_escape_string($menu->description),
						mysql_real_escape_string($menu->user_id),
						mysql_real_escape_string($menu->user_name),
						mysql_real_escape_string($menu->created_date),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($menu->language));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($menu) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "menu
						SET name = '%s', description = '%s', language = '%s'
						WHERE menu_id = '%s'",
						mysql_real_escape_string($menu->name),
						mysql_real_escape_string($menu->description),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($menu->language),
						mysql_real_escape_string($menu->menu_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "menu WHERE menu_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getMenus($offset = null, $count = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "menu
						WHERE language = '%s'
						ORDER BY menu_id DESC",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count() 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_menus
						FROM " . $this->_prefix . "menu 
						WHERE language = '%s' 
						LIMIT 1",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
						
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_menus;
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = sprintf("SELECT m.* FROM " . $this->_prefix . "menu AS m
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
							ON tr.item_id = m.menu_id", 
						mysql_real_escape_string($item->menu_id), 
						mysql_real_escape_string($item->menu_id), 
						mysql_real_escape_string($item->menu_id),
						'Menu_Models_Menu',
						'Menu_Models_Menu');
						
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
		$sql = sprintf("SELECT m.*, (tr.item_id IS NULL) AS translatable
						FROM " . $this->_prefix . "menu AS m
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON tr.source_item_id = m.menu_id
							AND tr.item_class = '%s'
							AND tr.language = '%s'
						WHERE m.language = '%s'",
						'Menu_Models_Menu',
						mysql_real_escape_string($lang),
						mysql_real_escape_string($this->_lang));

		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($menu)
	{
		$sql = sprintf("SELECT m.* 
						FROM " . $this->_prefix . "menu AS m
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON m.menu_id = tr.source_item_id
						WHERE tr.item_id = '%s' AND tr.item_class = '%s'
						LIMIT 1", 
						mysql_real_escape_string($menu->menu_id),
						'Menu_Models_Menu');
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Menu_Models_Menu(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}	
}
