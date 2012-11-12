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
 * @version 	$Id: Set.php 5152 2010-08-30 07:25:57Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Mysql_Set extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Set
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Set($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "multimedia_set 
						WHERE set_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Multimedia_Models_Set(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($set) 
	{
	$sql = sprintf("INSERT INTO " . $this->_prefix . "multimedia_set (title, slug, description, created_date, created_user_id,
					created_user_name, 
					image_square, image_thumbnail, image_small, image_crop, image_medium, image_large, is_active)
					VALUES ('%s', '%s', '%s', '%s', '%s',
					'%s', '%s', '%s', '%s', '%s',
					'%s', '%s', '%s')",
					mysql_real_escape_string($set->title),
					mysql_real_escape_string($set->slug),
					mysql_real_escape_string($set->description),
					mysql_real_escape_string($set->created_date),
					mysql_real_escape_string($set->created_user_id),
					mysql_real_escape_string($set->created_user_name),
					mysql_real_escape_string($set->image_square),
					mysql_real_escape_string($set->image_thumbnail),
					mysql_real_escape_string($set->image_small),
					mysql_real_escape_string($set->image_crop),
					mysql_real_escape_string($set->image_medium),
					mysql_real_escape_string($set->image_large),
					mysql_real_escape_string($set->is_active));
		mysql_query($sql);
		return mysql_insert_id();		
	}
	
	public function update($set) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "multimedia_set
						SET title = '%s', slug = '%s', description = '%s', 
							image_square = '%s', image_thumbnail = '%s', image_small = '%s', image_crop = '%s', image_medium = '%s', image_large = '%s'
						WHERE set_id = '%s'",
						mysql_real_escape_string($set->title),
						mysql_real_escape_string($set->slug),
						mysql_real_escape_string($set->description),
						mysql_real_escape_string($set->image_square),
						mysql_real_escape_string($set->image_thumbnail),
						mysql_real_escape_string($set->image_small),
						mysql_real_escape_string($set->image_crop),
						mysql_real_escape_string($set->image_medium),
						mysql_real_escape_string($set->image_large),
						mysql_real_escape_string($set->set_id));
		mysql_query($sql);
		return mysql_affected_rows();			
	}
	
	public function find($offset = null, $count = null, $exp = null) 
	{
		$sql = "SELECT * FROM " . $this->_prefix . "multimedia_set AS s";
		if ($exp) {
			$where = array();
			
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("s.created_user_id = '%s'", mysql_real_escape_string($exp['created_user_id']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "s.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("s.is_active = '%s'", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY s.set_id DESC";
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
	
	public function count($exp = null) 
	{
		$sql = "SELECT COUNT(*) AS num_sets FROM " . $this->_prefix . "multimedia_set AS s";
		if ($exp) {
			$where = array();
			
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("s.created_user_id = '%s'", mysql_real_escape_string($exp['created_user_id']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "s.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("s.is_active = '%s'", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE ".implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_sets;
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "multimedia_file_set_assoc WHERE set_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		
		$sql = sprintf("DELETE FROM " . $this->_prefix . "multimedia_set WHERE set_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}

	public function updateDescription($setId, $title, $description = null) 
	{
		$sql = "UPDATE " . $this->_prefix . "multimedia_set SET ";
		$updates = array();
		if (null != $title) {
			$updates[] = sprintf("title = '%s'", mysql_real_escape_string($title));
			$updates[] = sprintf("slug = '%s'",
								mysql_real_escape_string(Tomato_Utility_String::removeSign($title, '-', true)));
		}
		if (null != $description) {
			$updates[] = sprintf("description = '%s'", mysql_real_escape_string($description));
		}
		$sql .= implode(',', $updates);
		$sql .= sprintf(" WHERE set_id = '%s'", mysql_real_escape_string($setId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function toggleStatus($id) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "multimedia_set 
						SET is_active = 1 - is_active 
						WHERE set_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = sprintf("SELECT s.*
						FROM " . $this->_prefix . "multimedia_set AS s
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON s.set_id = ti.item_id
						WHERE ti.tag_id = '%s'
							AND ti.item_name = 'set_id'
							AND s.is_active = 1
						ORDER BY s.set_id DESC
						LIMIT %s, %s",
						mysql_real_escape_string($tagId),
						mysql_real_escape_string($offset),
						mysql_real_escape_string($count));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql = sprintf("SELECT COUNT(set_id) AS num_sets
						FROM " . $this->_prefix . "multimedia_set AS s
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON s.set_id = ti.item_id
						WHERE ti.tag_id = '%s'
							AND ti.item_name = 'set_id'
							AND s.is_active = 1
						LIMIT 1",
						mysql_real_escape_string($tagId));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_sets;
	}
}
