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
 * @version 	$Id: Item.php 5338 2010-09-07 08:27:25Z huuphuoc $
 * @since		2.0.7
 */

class Menu_Models_Dao_Pdo_Mysql_Item extends Tomato_Model_Dao
	implements Menu_Models_Interface_Item
{
	public function convert($entity) 
	{
		return new Menu_Models_Item($entity); 
	}
	
	public function add($item)
	{
		$this->_conn->insert($this->_prefix . 'menu_item', 
							array(
								'item_id' 	=> $item->item_id,
								'label' 	=> $item->label,
								'link' 		=> $item->link,
								'menu_id'	=> $item->menu_id,
								'left_id'	=> $item->left_id,
								'right_id'  => $item->right_id,
								'parent_id' => $item->parent_id,
							));
	}
	
	public function delete($menuId)
	{
		return $this->_conn->delete($this->_prefix . 'menu_item',
									array(
										'menu_id = ?' => $menuId,
									));
	}

	public function getTree($menuId)
	{
		$rs = $this->_conn
					->select()
					->from(array('node' => $this->_prefix . 'menu_item'), array(
							'node.item_id', 'node.label', 'node.link', 'node.left_id', 'node.right_id', 
							'node.parent_id',
						))
					->from(array('parent' => $this->_prefix . 'menu_item'), array('depth' => '(COUNT(parent.item_id) - 1)'))
					->where('node.menu_id = ?', $menuId)
					->where('parent.menu_id = ?', $menuId)
					->where('node.left_id BETWEEN parent.left_id AND parent.right_id')
					->group('node.item_id')
					->order('node.left_id')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
}
