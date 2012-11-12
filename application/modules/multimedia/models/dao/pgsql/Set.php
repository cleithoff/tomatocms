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
 * @version 	$Id: Set.php 5448 2010-09-15 09:09:11Z leha $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Pgsql_Set extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Set
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Set($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "multimedia_set 
						WHERE set_id = %s
						LIMIT 1", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Multimedia_Models_Set(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($set) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "multimedia_set (title, slug, description, created_date, created_user_id,
						created_user_name, image_medium, image_square, image_thumbnail, image_small, 
						image_crop, image_large, is_active)
					VALUES ('%s', '%s', '%s', '%s', %s,
						'%s', '%s', '%s', '%s', '%s',
						'%s', '%s', '%s')
					RETURNING set_id",
					pg_escape_string($set->title),
					pg_escape_string($set->slug),
					pg_escape_string($set->description),
					pg_escape_string($set->created_date),
					pg_escape_string($set->created_user_id),
					pg_escape_string($set->created_user_name),
					pg_escape_string($set->image_medium),
					pg_escape_string($set->image_square),
					pg_escape_string($set->image_thumbnail),
					pg_escape_string($set->image_small),
					pg_escape_string($set->image_crop),
					pg_escape_string($set->image_large),
					pg_escape_string($set->is_active));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->set_id;	
	}
	
	public function update($set) 
	{
		return pg_update($this->_conn, $this->_prefix . 'multimedia_set', 
						array(
							'title'      	  => $set->title,
							'slug' 			  => $set->slug,
							'description' 	  => $set->description,
							'image_square' 	  => $set->image_square,
							'image_thumbnail' => $set->image_thumbnail,
							'image_small' 	  => $set->image_small,
							'image_crop' 	  => $set->image_crop,
							'image_medium' 	  => $set->image_medium,
							'image_large' 	  => $set->image_large,
						),
						array(
							'set_id' => $set->set_id,
						));				
	}
	
	public function find($offset = null, $count = null, $exp = null) 
	{
		$sql = "SELECT * FROM " . $this->_prefix . "multimedia_set AS s";
		if ($exp) {
			$where = array();
			
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("s.created_user_id = %s", pg_escape_string($exp['created_user_id']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "s.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("s.is_active = %s", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY s.set_id DESC";
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
	
	public function count($exp = null) 
	{
		$sql = "SELECT COUNT(*) AS num_sets FROM " . $this->_prefix . "multimedia_set AS s";
		if ($exp) {
			$where = array();
			
			if (isset($exp['created_user_id'])) {
				$where[] = sprintf("s.created_user_id = %s", pg_escape_string($exp['created_user_id']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "s.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("s.is_active = %s", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_sets;
	}
	
	public function delete($id) 
	{
		$where['set_id'] = $id;
		pg_delete($this->_conn, $this->_prefix . 'multimedia_file_set_assoc', $where);
		return pg_delete($this->_conn, $this->_prefix . 'multimedia_set', $where);
	}

	public function updateDescription($setId, $title, $description = null) 
	{
		$data = array();
		if (null != $title) {
			$data['title'] = $title;
			$data['slug']  = Tomato_Utility_String::removeSign($title, '-', true);
		}
		if (null != $description) {
			$data['description'] = $description;
		} 
		return pg_update($this->_conn, $this->_prefix . 'multimedia_set', 
						$data,
						array(
							'set_id' => $setId,
						));
	}
	
	public function toggleStatus($id) 
	{
		return pg_update($this->_conn, $this->_prefix . 'multimedia_set', 
						array(
							'is_active' => new Zend_Db_Expr('1 - is_active'),
						),
						array(
							'set_id' => $id,
						));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = sprintf("SELECT s.*
						FROM " . $this->_prefix . "multimedia_set AS s
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON s.set_id = ti.item_id
						WHERE ti.tag_id = %s
							AND ti.item_name = 'set_id'
							AND s.is_active = 1
						ORDER BY s.set_id DESC
						LIMIT %s OFFSET %s",
						pg_escape_string($tagId),
						pg_escape_string($count),
						pg_escape_string($offset));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql = sprintf("SELECT COUNT(set_id) AS num_sets
						FROM " . $this->_prefix . "multimedia_set AS s
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON s.set_id = ti.item_id
						WHERE ti.tag_id = %s
							AND ti.item_name = 'set_id'
							AND s.is_active = 1
						LIMIT 1",
						pg_escape_string($tagId));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_sets;
	}
}
