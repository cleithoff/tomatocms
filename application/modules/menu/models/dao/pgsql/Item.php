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
 * @version 	$Id: Item.php 5450 2010-09-15 09:14:11Z leha $
 * @since		2.0.5
 */

class Menu_Models_Dao_Pgsql_Item extends Tomato_Model_Dao
	implements Menu_Models_Interface_Item
{
	public function convert($entity) 
	{
		return new Menu_Models_Item($entity); 
	}
	
	public function add($item)
	{
		pg_insert($this->_conn, $this->_prefix . 'menu_item', 
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
		return pg_delete($this->_conn, $this->_prefix . 'menu_item',
						array(
							'menu_id' => $menuId,
						));
	}

	public function getTree($menuId)
	{
		$sql = sprintf("SELECT MAX(node.item_id) AS item_id, MAX(node.label) AS label, MAX(node.link) AS link, (COUNT(parent.item_id) - 1) AS depth,
							MAX(node.left_id) AS left_id, MAX(node.right_id) AS right_id, MAX(node.parent_id) AS parent_id
						FROM " . $this->_prefix . "menu_item AS node,
							" . $this->_prefix . "menu_item AS parent
						WHERE node.menu_id = %s 
							AND parent.menu_id = %s 
							AND node.left_id BETWEEN parent.left_id AND parent.right_id
						GROUP BY node.item_id
						ORDER BY MAX(node.left_id)",
						($menuId) ? $menuId : 'null',
						($menuId) ? $menuId : 'null');
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
