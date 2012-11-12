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
 * @version 	$Id: File.php 5448 2010-09-15 09:09:11Z leha $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Pgsql_File extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_File
{
	public function convert($entity) 
	{
		return new Multimedia_Models_File($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "multimedia_file 
						WHERE file_id = %s
						LIMIT 1",
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Multimedia_Models_File(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($file) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "multimedia_file (title, slug, description, content, created_date,
							created_user, created_user_name, 
							image_square, image_thumbnail, image_small, image_crop, image_medium, image_large, image_original, 
							url, html_code, file_type, is_active)
						VALUES ('%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s')
						RETURNING file_id",
						pg_escape_string($file->title),
						pg_escape_string($file->slug),
						pg_escape_string($file->description),
						pg_escape_string($file->content),
						pg_escape_string($file->created_date),
						pg_escape_string($file->created_user),
						pg_escape_string($file->created_user_name),
						pg_escape_string($file->image_square),
						pg_escape_string($file->image_thumbnail),
						pg_escape_string($file->image_small),
						pg_escape_string($file->image_crop),
						pg_escape_string($file->image_medium),
						pg_escape_string($file->image_large),
						pg_escape_string($file->image_original),
						pg_escape_string($file->url),
						pg_escape_string($file->html_code),
						pg_escape_string($file->file_type),
						pg_escape_string($file->is_active));
		$rs  = pg_query($sql);						
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->file_id;
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = "SELECT * FROM " . $this->_prefix . "multimedia_file AS f";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("f.file_id = %s", pg_escape_string($exp['file_id']));
			}
			if (isset($exp['created_user'])) {
				$where[] = sprintf("f.created_user = %s", pg_escape_string($exp['created_user']));
			}
			if (isset($exp['file_type'])) {
				$where[] = sprintf("f.file_type = '%s'", pg_escape_string($exp['file_type']));
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
				$where[] = sprintf("f.is_active = %s", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY f.file_id DESC";
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
		$sql = "SELECT COUNT(*) AS num_files FROM " . $this->_prefix . "multimedia_file AS f";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("f.file_id = %s", pg_escape_string($exp['file_id']));
			}
			if (isset($exp['created_user'])) {
				$where[] = sprintf("f.created_user = %s", pg_escape_string($exp['created_user']));
			}
			if (isset($exp['file_type'])) {
				$where[] = sprintf("f.file_type = '%s'", pg_escape_string($exp['file_type']));
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
				$where[] = sprintf("f.is_active = %s", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_files;
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'multimedia_file', 
						array(
							'file_id' => $id,
						));
	}

	public function updateDescription($fileId, $title, $description = null) 
	{
		$data = array();
		if (null != $title) {
			$data['title'] = $title;
			$data['slug']  = Tomato_Utility_String::removeSign($title, '-', true);
		}
		if (null != $description) {
			$data['description'] = $description;
		} 
		return pg_update($this->_conn, $this->_prefix . 'multimedia_file', 
						$data, 
						array(
							'file_id' => $fileId,
						));
	}
	
	public function toggleStatus($id) 
	{
		return pg_update($this->_conn, $this->_prefix . 'multimedia_file', 
						array(
							'is_active' => new Zend_Db_Expr('1 - is_active'),
						),
						array(
							'file_id' => $id,
						));
	}
	
	public function update($file) 
	{
		return pg_update($this->_conn, $this->_prefix . 'multimedia_file', 
						array(
							'title' 		  => $file->title,
							'slug' 			  => $file->slug,
							'description' 	  => $file->description,
							'content' 		  => $file->content,
							'image_square' 	  => $file->image_square,
							'image_thumbnail' => $file->image_thumbnail,
							'image_small' 	  => $file->image_small,
							'image_crop' 	  => $file->image_crop,
							'image_medium' 	  => $file->image_medium,
							'image_large' 	  => $file->image_large,
							'image_original'  => $file->image_original,
							'url' 			  => $file->url,
							'html_code' 	  => $file->html_code,
							'file_type' 	  => $file->file_type,
						), 
						array(
							'file_id' => $file->file_id,
						));
	}
	
	public function getFilesInSet($setId, $offset = null, $count = null, $isActive = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "multimedia_file_set_assoc AS fs
							ON fs.file_id = f.file_id AND fs.set_id = %s",
						pg_escape_string($setId));
		if (is_bool($isActive)) {
			$sql .= sprintf(" WHERE f.is_active = %s", (int)$isActive);
		}
		$sql .= " ORDER BY f.file_id DESC";
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
	
	public function countFilesInSet($setId, $isActive = null)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_files FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "multimedia_file_set_assoc AS fs
							ON fs.file_id = f.file_id AND fs.set_id = %s",
						pg_escape_string($setId));
		if (is_bool($isActive)) {
			$sql .= sprintf(" WHERE f.is_active = %s", (int)$isActive);
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_files;
	}
	
	public function removeFromSet($setId, $fileId = null)
	{
		$where = array();
		$where['set_id'] = $setId;
		if (null != $fileId) {
			$where['file_id'] = $fileId;
		}
		return pg_delete($this->_conn, $this->_prefix . 'multimedia_file_set_assoc', $where);
	}
	
	public function addToSet($setId, $fileId)
	{
		pg_insert($this->_conn, $this->_prefix . 'multimedia_file_set_assoc', 
					array(
						'file_id' => $fileId,
						'set_id'  => $setId,
					));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql = sprintf("SELECT f.*
						FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON f.file_id = ti.item_id
						WHERE ti.tag_id = %s
							AND ti.item_name = 'file_id'
							AND f.is_active = 1
						ORDER BY f.file_id DESC
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
		$sql = sprintf("SELECT COUNT(file_id) AS num_files
						FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON f.file_id = ti.item_id
						WHERE ti.tag_id = %s
							AND ti.item_name = 'file_id'
							AND f.is_active = 1
						LIMIT 1",
						pg_escape_string($tagId));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_files;
	}	
}
