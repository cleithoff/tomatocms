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
 * @version 	$Id: TagItem.php 5061 2010-08-28 18:48:02Z huuphuoc $
 * @since		2.0.5
 */

class Tag_Models_Dao_Sqlsrv_TagItem extends Tomato_Model_Dao
	implements Tag_Models_Interface_TagItem
{
	public function convert($entity) 
	{
		return new Tag_Models_TagItem($entity);
	}
	
	public function delete($item)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'tag_item_assoc
				WHERE item_id = ? AND item_name = ? AND route_name = ? AND details_route_name = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$item->item_id,
			$item->item_name,
			$item->route_name,
			$item->details_route_name,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function add($item)
	{
		$this->_conn->insert($this->_prefix . 'tag_item_assoc', array(
			'tag_id'			 => $item->tag_id,
			'item_id'			 => $item->item_id,
			'item_name'			 => $item->item_name,
			'route_name'		 => $item->route_name,
			'details_route_name' => $item->details_route_name,
			'params'			 => $item->item_name . ':' . $item->item_id,
		));
	}
	
	public function getTagCloud($routeName, $limit = null)
	{
		$sql = 'SELECT ti.details_route_name, t.tag_id, t.tag_text, COUNT(*) AS num_items
				FROM ' . $this->_prefix . 'tag_item_assoc AS ti
				INNER JOIN ' . $this->_prefix . 'tag AS t
					ON ti.tag_id = t.tag_id
				WHERE ti.route_name = ?
				GROUP BY ti.details_route_name, t.tag_id, t.tag_text, tag_text';
		if (is_numeric($limit)) {
			$sql = $this->_conn->limit($sql, $limit);	
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($routeName));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
