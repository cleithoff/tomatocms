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

class Multimedia_Models_Dao_Mysql_File extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_File
{
	public function convert($entity) 
	{
		return new Multimedia_Models_File($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "multimedia_file 
						WHERE file_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Multimedia_Models_File(mysql_fetch_object($rs));
		mysql_free_result($rs);
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
							'%s', '%s', '%s')",
						mysql_real_escape_string($file->title),
						mysql_real_escape_string($file->slug),
						mysql_real_escape_string($file->description),
						mysql_real_escape_string($file->content),
						mysql_real_escape_string($file->created_date),
						mysql_real_escape_string($file->created_user),
						mysql_real_escape_string($file->created_user_name),
						mysql_real_escape_string($file->image_square),
						mysql_real_escape_string($file->image_thumbnail),
						mysql_real_escape_string($file->image_small),
						mysql_real_escape_string($file->image_crop),
						mysql_real_escape_string($file->image_medium),
						mysql_real_escape_string($file->image_large),
						mysql_real_escape_string($file->image_original),
						mysql_real_escape_string($file->url),
						mysql_real_escape_string($file->html_code),
						mysql_real_escape_string($file->file_type),
						mysql_real_escape_string($file->is_active));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = "SELECT * FROM " . $this->_prefix . "multimedia_file AS f";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("f.file_id = '%s'", mysql_real_escape_string($exp['file_id']));
			}
			if (isset($exp['created_user'])) {
				$where[] = sprintf("f.created_user = '%s'", mysql_real_escape_string($exp['created_user']));
			}
			if (isset($exp['file_type'])) {
				$where[] = sprintf("f.file_type = '%s'", mysql_real_escape_string($exp['file_type']));
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
				$where[] = sprintf("f.is_active = '%s'", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY f.file_id DESC";
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
		$sql = "SELECT COUNT(*) AS num_files FROM " . $this->_prefix . "multimedia_file AS f";
		if ($exp) {
		$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("f.file_id = '%s'", mysql_real_escape_string($exp['file_id']));
			}
			if (isset($exp['created_user'])) {
				$where[] = sprintf("f.created_user = '%s'", mysql_real_escape_string($exp['created_user']));
			}
			if (isset($exp['file_type'])) {
				$where[] = sprintf("f.file_type = '%s'", mysql_real_escape_string($exp['file_type']));
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
				$where[] = sprintf("f.is_active = '%s'", (int)$exp['is_active']);
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_files;
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "multimedia_file WHERE file_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}

	public function updateDescription($fileId, $title, $description = null) 
	{
		$sql = "UPDATE " . $this->_prefix . "multimedia_file SET ";
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
		$sql .= sprintf(" WHERE file_id = '%s'", mysql_real_escape_string($fileId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function toggleStatus($id) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "multimedia_file 
						SET is_active = 1 - is_active 
						WHERE file_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($file) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "multimedia_file
						SET title = '%s', slug = '%s', description = '%s', content = '%s', 
							image_square = '%s', image_thumbnail = '%s', image_small = '%s', image_crop = '%s', image_medium = '%s', image_large = '%s', image_original = '%s', 
							url = '%s', html_code = '%s', file_type = '%s'
						WHERE file_id = '%s'",
						mysql_real_escape_string($file->title),
						mysql_real_escape_string($file->slug),
						mysql_real_escape_string($file->description),
						mysql_real_escape_string($file->content),
						mysql_real_escape_string($file->image_square),
						mysql_real_escape_string($file->image_thumbnail),
						mysql_real_escape_string($file->image_small),
						mysql_real_escape_string($file->image_crop),
						mysql_real_escape_string($file->image_medium),
						mysql_real_escape_string($file->image_large),
						mysql_real_escape_string($file->image_original),
						mysql_real_escape_string($file->url),
						mysql_real_escape_string($file->html_code),
						mysql_real_escape_string($file->file_type),
						mysql_real_escape_string($file->file_id));
		mysql_query($sql);
		return mysql_affected_rows();		
	}
	
	public function getFilesInSet($setId, $offset = null, $count = null, $isActive = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "multimedia_file_set_assoc AS fs
							ON fs.file_id = f.file_id AND fs.set_id = '%s'",
						mysql_real_escape_string($setId));
		if (is_bool($isActive)) {
			$sql .= sprintf(" WHERE f.is_active = '%s'", (int)$isActive);
		}
		$sql .= " ORDER BY f.file_id DESC";
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
	
	public function countFilesInSet($setId, $isActive = null)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_files FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "multimedia_file_set_assoc AS fs
						ON fs.file_id = f.file_id AND fs.set_id = '%s'",
						mysql_real_escape_string($setId));
		if (is_bool($isActive)) {
			$sql .= sprintf(" WHERE f.is_active = '%s'", (int)$isActive);
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_files;
	}
	
	public function removeFromSet($setId, $fileId = null)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "multimedia_file_set_assoc WHERE set_id = '%s'", 
						mysql_real_escape_string($setId));
		if ($fileId != null) {
			$sql .= sprintf(" AND file_id = '%s'", mysql_real_escape_string($fileId));
		}
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function addToSet($setId, $fileId)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "multimedia_file_set_assoc (file_id, set_id)
						VALUES ('%s', '%s')",
						mysql_real_escape_string($fileId),
						mysql_real_escape_string($setId));
		mysql_query($sql);
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$sql = sprintf("SELECT f.*
						FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON f.file_id = ti.item_id
						WHERE ti.tag_id = '%s'
							AND ti.item_name = 'file_id'
							AND f.is_active = 1
						ORDER BY f.file_id DESC
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
		$sql = sprintf("SELECT COUNT(file_id) AS num_files
						FROM " . $this->_prefix . "multimedia_file AS f
						INNER JOIN " . $this->_prefix . "tag_item_assoc AS ti
							ON f.file_id = ti.item_id
						WHERE ti.tag_id = '%s'
							AND ti.item_name = 'file_id'
							AND f.is_active = 1
						LIMIT 1",
						mysql_real_escape_string($tagId));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_files;
	}
}
