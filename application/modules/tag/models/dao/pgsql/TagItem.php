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
 * @version 	$Id: TagItem.php 5442 2010-09-15 08:22:57Z leha $
 * @since		2.0.5
 */

class Tag_Models_Dao_Pgsql_TagItem extends Tomato_Model_Dao
	implements Tag_Models_Interface_TagItem
{
	public function convert($entity) 
	{
		return new Tag_Models_TagItem($entity);
	}
	
	public function delete($item)
	{
		return pg_delete($this->_conn, $this->_prefix . 'tag_item_assoc', 
						array(
							'item_id' 			 => $item->item_id,
							'item_name' 		 => $item->item_name,
							'route_name' 		 => $item->route_name,
							'details_route_name' => $item->details_route_name,
						));	
	}
	
	public function add($item)
	{
		pg_insert($this->_conn, $this->_prefix . 'tag_item_assoc', 
					array(
						'tag_id' 			 => $item->tag_id,
						'item_id' 			 => $item->item_id,
						'item_name' 		 => $item->item_name,
						'route_name' 		 => $item->route_name,
						'details_route_name' => $item->details_route_name,
						'params' 			 => $item->item_name . ':' . $item->item_id,
					));
	}
	
	public function getTagCloud($routeName, $limit = null)
	{
		$sql = sprintf("SELECT MAX(ti.details_route_name) AS details_route_name, MAX(t.tag_id) AS tag_id, t.tag_text, COUNT(*) AS num_items
						FROM " . $this->_prefix . "tag_item_assoc AS ti
						INNER JOIN " . $this->_prefix . "tag AS t
							ON ti.tag_id = t.tag_id
						WHERE ti.route_name = '%s'
						GROUP BY tag_text",
						pg_escape_string($routeName));
		if (is_numeric($limit)) {
			$sql .= sprintf(" LIMIT %s", $limit);	
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
