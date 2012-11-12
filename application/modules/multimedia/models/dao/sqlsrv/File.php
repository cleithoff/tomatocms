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
 * @version 	$Id: File.php 5152 2010-08-30 07:25:57Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Sqlsrv_File extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_File
{
	public function convert($entity) 
	{
		return new Multimedia_Models_File($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'multimedia_file 
				WHERE file_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Multimedia_Models_File($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($file) 
	{
		$this->_conn->insert($this->_prefix . 'multimedia_file', array(
			'title'		 		=> $file->title,
			'slug'				=> $file->slug,
			'description'		=> $file->description,
			'content'			=> $file->content,
			'created_date'		=> $file->created_date,
			'created_user'		=> $file->created_user,
			'created_user_name' => $file->created_user_name,
			'image_square'		=> $file->image_square,
			'image_thumbnail'	=> $file->image_thumbnail,
			'image_small'		=> $file->image_small,
			'image_crop'		=> $file->image_crop,
			'image_medium'		=> $file->image_medium,
			'image_large'		=> $file->image_large,
			'image_original'	=> $file->image_original,
			'url'				=> $file->url,
			'html_code'			=> $file->html_code,
			'file_type'			=> $file->file_type,
			'is_active'			=> (int)$file->is_active,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'multimedia_file');
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql    = 'SELECT * FROM ' . $this->_prefix . 'multimedia_file AS f';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[]  = 'f.file_id = ?';
				$params[] = $exp['file_id'];
			}
			if (isset($exp['created_user'])) {
				$where[]  = 'f.created_user = ?';
				$params[] = $exp['created_user'];
			}
			if (isset($exp['file_type'])) {
				$where[]  = 'f.file_type = ?';
				$params[] = $exp['file_type'];
			}
			
			/**
			 * TODO: Make a simpler check
			 */
			if ((isset($exp['photo']) && '1' == $exp['photo']) && (isset($exp['clip']) && '1' == $exp['clip'])) {
				$where[] = "(f.file_type = 'image' OR f.file_type = 'video')";
			} elseif (isset($exp['photo']) && '1' == $exp['photo']) {
				$where[] = "f.file_type = 'image'";
			} elseif (isset($exp['clip']) && '1' == $exp['clip']) {
				$where[] = "f.file_type = 'video'";
			}
			if (isset($exp['keyword'])) {
				$where[] = "f.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[]  = 'f.is_active = ?';
				$params[] = (int)$exp['is_active'];
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' ORDER BY file_id DESC';
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
		$sql    = 'SELECT COUNT(*) AS num_files FROM ' . $this->_prefix . 'multimedia_file AS f';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[]  = 'f.file_id = ?';
				$params[] = $exp['file_id'];
			}
			if (isset($exp['created_user'])) {
				$where[]  = 'f.created_user = ?';
				$params[] = $exp['created_user'];
			}
			if (isset($exp['file_type'])) {
				$where[]  = 'f.file_type = ?';
				$params[] = $exp['file_type'];
			}
			
			/**
			 * TODO: Make a simpler check
			 */
			if ((isset($exp['photo']) && '1' == $exp['photo']) && (isset($exp['clip']) && '1' == $exp['clip'])) {
				$where[] = "(f.file_type = 'image' OR f.file_type = 'video')";
			} elseif (isset($exp['photo']) && '1' == $exp['photo']) {
				$where[] = "f.file_type = 'image'";
			} elseif (isset($exp['clip']) && '1' == $exp['clip']) {
				$where[] = "f.file_type = 'video'";
			}
			if (isset($exp['keyword'])) {
				$where[] = "f.title LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			if (isset($exp['is_active'])) {
				$where[]  = 'f.is_active = ?';
				$params[] = $exp['is_active'];
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
		return $row->num_files;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'multimedia_file WHERE file_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}

	public function updateDescription($fileId, $title, $description = null) 
	{
		$sql     = 'UPDATE ' . $this->_prefix . 'multimedia_file SET ';
		$updates = array();
		$papams  = array();
		if (null != $title) {
			$updates[] = "title = ?";
			$updates[] = "slug = ?";
			
			$papams[]  = $title;
			$papams[]  = Tomato_Utility_String::removeSign($title, '-', true);
		}
		if (null != $description) {
			$updates[] = "description = ?";
			$papams[]  = $description;
		}
		$sql     .= implode(',', $updates);
		$sql     .= ' WHERE file_id = ?';
		$papams[] = $fileId;
		
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($papams);
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function toggleStatus($id) 
	{
		$sql = 'UPDATE ' . $this->_prefix . 'multimedia_file 
				SET is_active = 1 - is_active 
				WHERE file_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function update($file) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'multimedia_file
				SET title = ?, slug = ?, description = ?, content = ?, 
					image_square = ?, image_thumbnail = ?, image_small = ?, image_crop = ?, image_medium = ?, image_large = ?, image_original = ?, 
					url = ?, html_code = ?, file_type = ?
				WHERE file_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$file->title,
			$file->slug,
			$file->description,
			$file->content,
			$file->image_square,
			$file->image_thumbnail,
			$file->image_small,
			$file->image_crop,
			$file->image_medium,
			$file->image_large,
			$file->image_original,
			$file->url,
			$file->html_code,
			$file->file_type,
			$file->file_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;		
	}
	
	public function getFilesInSet($setId, $offset = null, $count = null, $isActive = null)
	{
		$sql = 'SELECT f.* FROM ' . $this->_prefix . 'multimedia_file AS f
				INNER JOIN ' . $this->_prefix . 'multimedia_file_set_assoc AS fs
					ON fs.file_id = f.file_id AND fs.set_id = ?';
		if (is_bool($isActive)) {
			$sql .= ' WHERE f.is_active = ' . (int)$isActive;
		}
		$sql .= ' ORDER BY file_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($setId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countFilesInSet($setId, $isActive = null)
	{
		$sql = 'SELECT COUNT(*) AS num_files FROM ' . $this->_prefix . 'multimedia_file AS f
				INNER JOIN ' . $this->_prefix . 'multimedia_file_set_assoc AS fs
					ON fs.file_id = f.file_id AND fs.set_id = ?';
		if (is_bool($isActive)) {
			$sql .= ' WHERE f.is_active = ' . (int)$isActive;
		}
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($setId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_files;
	}
	
	public function removeFromSet($setId, $fileId = null)
	{
		$sql    = 'DELETE FROM ' . $this->_prefix . 'multimedia_file_set_assoc WHERE set_id = ?';
		$params = array($setId);
		if ($fileId != null) {
			$sql 	 .= ' AND file_id = ?';
			$params[] = $fileId;
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->rowCount();		
		$stmt->closeCursor();
		return $row;
	}
	
	public function addToSet($setId, $fileId)
	{
		$this->_conn->insert($this->_prefix . 'multimedia_file_set_assoc', array(
			'file_id' => $fileId,
			'set_id'  => $setId,
		));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql  = "SELECT f.*
				FROM " . $this->_prefix . "multimedia_file AS f
				INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
					ON f.file_id = ti.item_id
				WHERE ti.tag_id = ?
					AND ti.item_name = 'file_id'
					AND f.is_active = 1
				ORDER BY f.file_id DESC";
		$sql  = $this->_conn->limit($sql, $count, $offset);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countByTag($tagId)
	{
		$sql  = "SELECT TOP 1 COUNT(file_id) AS num_files
				FROM " . $this->_prefix . "multimedia_file AS f
				INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
					ON f.file_id = ti.item_id
				WHERE ti.tag_id = ?
					AND ti.item_name = 'file_id'
					AND f.is_active = 1";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_files;
	}	
}
