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
 * @version 	$Id: Tag.php 5443 2010-09-15 08:26:42Z leha $
 * @since		2.0.5
 */

class Tag_Models_Dao_Pgsql_Tag extends Tomato_Model_Dao
	implements Tag_Models_Interface_Tag
{
	public function convert($entity) 
	{
		return new Tag_Models_Tag($entity);
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "tag 
						WHERE tag_id = %s
						LIMIT 1",
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Tag_Models_Tag(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function exist($text)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_tags
						FROM " . $this->_prefix . "tag AS t
						WHERE t.tag_text = '%s'
						LIMIT 1",
						pg_escape_string($text));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return ($row->num_tags > 0);
	}
	
	public function add($tag) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "tag (tag_text) VALUES ('%s') RETURNING tag_id",
						pg_escape_string($tag->tag_text));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->tag_id;
	}
	
	public function delete($tagId) 
	{
		pg_delete($this->_conn, $this->_prefix . 'tag_item_assoc', 
					array(
						'tag_id' => $tagId,
					));
					
		return pg_delete($this->_conn, $this->_prefix . 'tag', 
					array(
						'tag_id' => $tagId,
					));
	}
	
	public function find($keyword, $offset, $count)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "tag AS t";
		if ($keyword != '') {
			$sql .= " WHERE t.tag_text LIKE '%" . addslashes($keyword) . "%'";
		}
		$sql .= " ORDER BY tag_id";
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
	
	public function count($keyword)
	{
		$sql = "SELECT COUNT(*) AS num_tags FROM " . $this->_prefix . "tag AS t";
		if ($keyword != '') {
			$sql .= " WHERE t.tag_text LIKE '%" . addslashes($keyword) . "%'";
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_tags;
	}
	
	public function getByItem($item)
	{
		$sql = sprintf("SELECT t.tag_id, t.tag_text AS tag_text, ti.details_route_name AS details_route_name
						FROM " . $this->_prefix . "tag AS t
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON t.tag_id = ti.tag_id
						WHERE ti.item_id = %s
							AND ti.item_name = '%s'
							AND ti.route_name = '%s'
							AND ti.details_route_name = '%s'
						GROUP BY t.tag_id, t.tag_text, ti.details_route_name",
						pg_escape_string($item->item_id),
						pg_escape_string($item->item_name),
						pg_escape_string($item->route_name),
						pg_escape_string($item->details_route_name));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);		
	}
	
	public function getByRoute($item, $limit = null)
	{
		$sql = sprintf("SELECT t.tag_id, t.tag_text, ti.details_route_name
						FROM " . $this->_prefix . "tag AS t
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON t.tag_id = ti.tag_id
						WHERE ti.route_name = '%s'
							AND POSITION('|' || ti.params || '|' IN '%s') > 0",
						pg_escape_string($item->route_name),
						pg_escape_string($item->params));
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
