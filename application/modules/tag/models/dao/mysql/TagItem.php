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
 * @version 	$Id: TagItem.php 5058 2010-08-28 18:42:00Z huuphuoc $
 * @since		2.0.5
 */

class Tag_Models_Dao_Mysql_TagItem extends Tomato_Model_Dao
	implements Tag_Models_Interface_TagItem
{
	public function convert($entity) 
	{
		return new Tag_Models_TagItem($entity);
	}
	
	public function delete($item)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "tag_item_assoc
						WHERE item_id = '%s' AND item_name = '%s' AND route_name = '%s' AND details_route_name = '%s'",
						mysql_real_escape_string($item->item_id),
						mysql_real_escape_string($item->item_name),
						mysql_real_escape_string($item->route_name),
						mysql_real_escape_string($item->details_route_name));
		mysql_query($sql);
		return mysql_affected_rows();	
	}
	
	public function add($item)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "tag_item_assoc (tag_id, item_id, item_name, route_name, details_route_name, params)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($item->tag_id),
						mysql_real_escape_string($item->item_id),
						mysql_real_escape_string($item->item_name),
						mysql_real_escape_string($item->route_name),
						mysql_real_escape_string($item->details_route_name),
						mysql_real_escape_string($item->item_name . ':' . $item->item_id));
		mysql_query($sql);
	}
	
	public function getTagCloud($routeName, $limit = null)
	{
		$sql = sprintf("SELECT ti.details_route_name, t.tag_id, t.tag_text, COUNT(*) AS num_items
						FROM " . $this->_prefix . "tag_item_assoc AS ti
						INNER JOIN " . $this->_prefix . "tag AS t
							ON ti.tag_id = t.tag_id
						WHERE ti.route_name = '%s'
						GROUP BY tag_text",
						mysql_real_escape_string($routeName));
		if (is_numeric($limit)) {
			$sql .= sprintf(" LIMIT %s", $limit);		
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
