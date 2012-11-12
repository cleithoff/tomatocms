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
 * @version 	$Id: Item.php 5040 2010-08-28 17:46:09Z huuphuoc $
 * @since		2.0.7
 */

class Menu_Models_Dao_SqlSrv_Item extends Tomato_Model_Dao
	implements Menu_Models_Interface_Item
{
	public function convert($entity) 
	{
		return new Menu_Models_Item($entity); 
	}
	
	public function add($item)
	{
		$this->_conn->insert($this->_prefix . 'menu_item', array(
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
		$sql  = 'DELETE FROM ' . $this->_prefix . 'menu_item WHERE menu_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($menuId));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}

	public function getTree($menuId)
	{
		$sql = 'SELECT node.item_id, node.label, CONVERT(varchar(500), node.link) AS link, (COUNT(parent.item_id) - 1) AS depth,
					node.left_id, node.right_id, node.parent_id
				FROM ' . $this->_prefix . 'menu_item AS node,
					' . $this->_prefix . 'menu_item AS parent
				WHERE node.menu_id = ? 
					AND parent.menu_id = ? 
					AND node.left_id BETWEEN parent.left_id AND parent.right_id
				GROUP BY node.item_id, node.label, CONVERT(varchar(500), node.link), node.left_id, node.right_id, node.parent_id
				ORDER BY node.left_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($menuId, $menuId));
		$rs = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rs, $this);
	}
}
