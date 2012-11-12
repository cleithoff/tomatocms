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
 * @version 	$Id: Item.php 5037 2010-08-28 17:42:20Z huuphuoc $
 * @since		2.0.5
 */


class Menu_Models_Dao_Mysql_Item extends Tomato_Model_Dao
	implements Menu_Models_Interface_Item
{
	public function convert($entity) 
	{
		return new Menu_Models_Item($entity); 
	}
	
	public function add($item)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "menu_item (item_id, label, link, menu_id, left_id, right_id, parent_id)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($item->item_id),
						mysql_real_escape_string($item->label),
						mysql_real_escape_string($item->link),
						mysql_real_escape_string($item->menu_id),
						mysql_real_escape_string($item->left_id),
						mysql_real_escape_string($item->right_id),
						mysql_real_escape_string($item->parent_id));
						
		mysql_query($sql);
		return mysql_insert_id();	
	}
	
	public function delete($menuId)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "menu_item 
						WHERE menu_id = '%s'", 
						mysql_real_escape_string($menuId));
						
		mysql_query($sql);
		return mysql_affected_rows();
	}

	public function getTree($menuId)
	{
		$sql = sprintf("SELECT node.item_id, node.label, node.link, (COUNT(parent.item_id) - 1) AS depth,
							node.left_id, node.right_id, node.parent_id
						FROM " . $this->_prefix . "menu_item AS node,
							" . $this->_prefix . "menu_item AS parent
						WHERE node.menu_id = '%s' 
							AND parent.menu_id = '%s' 
							AND node.left_id BETWEEN parent.left_id AND parent.right_id
						GROUP BY node.item_id
						ORDER BY node.left_id",
						$menuId,
						$menuId);
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);		
	}
}
