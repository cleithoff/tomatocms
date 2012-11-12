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
 * @version 	$Id: Menu.php 5449 2010-09-15 09:13:41Z leha $
 * @since		2.0.5
 */

class Menu_Models_Dao_Pdo_Mysql_Menu
	extends Tomato_Model_Dao
	implements Menu_Models_Interface_Menu
{
	public function convert($entity) 
	{
		return new Menu_Models_Menu($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('m' => $this->_prefix . 'menu'))
					->where('m.menu_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Menu_Models_Menu($row);
	}
	
	public function add($menu) 
	{
		$this->_conn->insert($this->_prefix . 'menu', 
							array(
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
		return $this->_conn->update($this->_prefix . 'menu', 
									array(
										'name'		  => $menu->name,
										'description' => $menu->description,
		
										/**
										 * @since 2.0.8
										 */
										'language'	  => $menu->language,
									),
									array(
										'menu_id = ?' => $menu->menu_id,
									));
	}
	
	public function delete($id) 
	{
		$where['menu_id = ?'] = $id;
		
		/**
		 * @since 2.0.7
		 */
		$this->_conn->delete($this->_prefix . 'menu_item', $where);
		
		return $this->_conn->delete($this->_prefix . 'menu', $where);
	}
	
	public function getMenus($offset = null, $count = null)
	{
		$select = $this->_conn
						->select()
						->from(array('m' => $this->_prefix . 'menu'))
						/**
						 * @since 2.0.8
						 */
						->where('m.language = ?', $this->_lang)
						->order('m.menu_id DESC');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count()
	{
		return $this->_conn
					->select()
					->from(array('m' => $this->_prefix . 'menu'), array('num_menus' => 'COUNT(*)'))
					/**
					 * @since 2.0.8
					 */
					->where('m.language = ?', $this->_lang)
					->limit(1)
					->query()
					->fetch()
					->num_menus;
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$rs = $this->_conn
					->select()
					->from(array('m' => $this->_prefix . 'menu'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'),
						'tr.item_class = ?
						AND (tr.item_id = ? OR tr.source_item_id = ?)
						AND (tr.item_id = m.menu_id OR tr.source_item_id = m.menu_id)',
						array('tr.source_item_id'))
					->group('m.menu_id')
					->bind(array(
						'Menu_Models_Menu',
						$item->menu_id,
						$item->menu_id,
					))
					->query()
					->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang)
	{
		$rs = $this->_conn
					->select()
					->from(array('m' => $this->_prefix . 'menu'))
					->joinLeft(array('tr' => $this->_prefix . 'core_translation'), 
							'tr.source_item_id = m.menu_id 
							AND tr.item_class = ? 
							AND tr.language = ?',
							array('translatable' => '(tr.item_id IS NULL)'))
					->where('m.language = ?', $this->_lang)
					->bind(array('Menu_Models_Menu', $lang))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($menu)
	{
		$row = $this->_conn
					->select()
					->from(array('m' => $this->_prefix . 'menu'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'), 'm.menu_id = tr.source_item_id', array())
					->where('tr.item_id = ?', $menu->menu_id)
					->where('tr.item_class = ?', 'Menu_Models_Menu')
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Menu_Models_Menu($row);
	}
}
