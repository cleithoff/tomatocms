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

class Multimedia_Models_Dao_Sqlsrv_Set extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Set
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Set($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'multimedia_set 
				WHERE set_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$return = (null == $row) ? null : new Multimedia_Models_Set($row);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($set) 
	{
		$this->_conn->insert($this->_prefix . 'multimedia_set', array(
			'title' 			=> $set->title,
			'slug' 				=> $set->slug,
			'description'		=> $set->description,
			'created_date'		=> $set->created_date,
			'created_user_id'	=> $set->created_user_id,
			'created_user_name'	=> $set->created_user_name,
			'image_square'		=> $set->image_square,
			'image_thumbnail'	=> $set->image_thumbnail,
			'image_small'		=> $set->image_small,
			'image_crop'		=> $set->image_crop,
			'image_medium'		=> $set->image_medium,
			'image_large'		=> $set->image_large,
			'is_active'			=> (int)$set->is_active,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'multimedia_set');		
	}
	
	public function update($set) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'multimedia_set
				SET title = ?, slug = ?, description = ?, 
					image_square = ?, image_thumbnail = ?, image_small = ?, image_crop = ?, image_medium = ?, image_large = ?
				WHERE set_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$set->title,
			$set->slug,
			$set->description,
			$set->image_square,
			$set->image_thumbnail,
			$set->image_small,
			$set->image_crop,
			$set->image_medium,
			$set->image_large,
			$set->set_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;			
	}
	
	public function find($offset = null, $count = null, $exp = null) 
	{
		$sql    = 'SELECT * FROM ' . $this->_prefix . 'multimedia_set AS s';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['created_user_id'])) {
				$where[]  = 's.created_user_id = ?';
				$params[] = $exp['created_user_id'];
			}
			if (isset($exp['keyword'])) {
				$where[] = "s.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[]  = 's.is_active = ?';
				$params[] = (int)$exp['is_active'];
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' ORDER BY set_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}

		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql    = 'SELECT COUNT(*) AS num_sets FROM ' . $this->_prefix . 'multimedia_set AS s';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['created_user_id'])) {
				$where[]  = 's.created_user_id = ?';
				$params[] = $exp['created_user_id'];
			}
			if (isset($exp['keyword'])) {
				$where[] = "s.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[]  = 's.is_active = ?';
				$params[] = (int)$exp['is_active'];
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);		
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_sets;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'multimedia_file_set_assoc WHERE set_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$numRows1 = $stmt->rowCount();
		$stmt->execute(array($id));
				
		$sql  = 'DELETE FROM ' . $this->_prefix . 'multimedia_set WHERE set_id = ?'; 
		$stmt = $this->_conn->prepare($sql);
		$numRows2 = $stmt->rowCount();
		$stmt->execute(array($id));
		$stmt->closeCursor();
		return $numRows1 + $numRows2;
	}

	public function updateDescription($setId, $title, $description = null) 
	{
		$sql     = 'UPDATE ' . $this->_prefix . 'multimedia_set SET ';
		$updates = array();
		$params  = array();
		if (null != $title) {
			$updates[] = 'title = ?';
			$params[]  = $title;
			$updates[] = 'slug = ?';
			$params[]  = Tomato_Utility_String::removeSign($title, '-', true);
		}
		if (null != $description) {
			$updates[] = 'description = ?';
			$params[]  = $description;
		}
		$sql     .= implode(',', $updates);
		$sql     .= ' WHERE set_id = ?';
		$params[] = $setId;
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function toggleStatus($id) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'multimedia_set 
				SET is_active = 1 - is_active 
				WHERE set_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = "SELECT s.*
				FROM " . $this->_prefix . "multimedia_set AS s
				INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
					ON s.set_id = ti.item_id
				WHERE ti.tag_id = ?
					AND ti.item_name = 'set_id'
					AND s.is_active = 1
				ORDER BY set_id DESC";
		$sql  = $this->_conn->limit($sql, $count, $offset);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql  = "SELECT COUNT(set_id) AS num_sets
				FROM " . $this->_prefix . "multimedia_set AS s
				INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
					ON s.set_id = ti.item_id
				WHERE ti.tag_id = ?
					AND ti.item_name = 'set_id'
					AND s.is_active = 1";
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_sets;
	}
}
