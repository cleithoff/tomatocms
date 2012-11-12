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
 * @version 	$Id: Note.php 5041 2010-08-28 17:49:16Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Mysql_Note extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Note
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Note($entity); 
	}
	
	public function add($note)
	{
		$sql = "INSERT INTO " . $this->_prefix . "multimedia_note (file_id, top, `left`, width, height, content, user_id, user_name, created_date)";
		if ($note->user_id && $note->user_name) {
			$sql .= " VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '".mysql_real_escape_string($note->user_id)."', '".mysql_real_escape_string($note->user_name)."', '%s')";
		} else {
			$sql .= " VALUES ('%s', '%s', '%s', '%s', '%s', '%s', null, null, '%s')";
		}		
		$sql = sprintf($sql,
						mysql_real_escape_string($note->file_id), 
						mysql_real_escape_string($note->top), 
						mysql_real_escape_string($note->left),
						mysql_real_escape_string($note->width), 
						mysql_real_escape_string($note->height), 
						mysql_real_escape_string($note->content),
						date('Y-m-d H:i:s'));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($id)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "multimedia_note WHERE note_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($note)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "multimedia_note
						SET top = '%s', left = '%s', width = '%s', height = '%s', content = '%s', user_id = '%s', user_name = '%s', created_date = '%s'
						WHERE note_id = '%s'",
						mysql_real_escape_string($note->top),
						mysql_real_escape_string($note->left),
						mysql_real_escape_string($note->width),
						mysql_real_escape_string($note->height),
						mysql_real_escape_string($note->content),
						mysql_real_escape_string($note->user_id),
						mysql_real_escape_string($note->user_name),
						date('Y-m-d H:i:s'),
						mysql_real_escape_string($note->note_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$sql = "SELECT n.*, f.image_square, f.image_thumbnail, f.image_small, f.image_crop, f.image_medium, f.image_large 
				FROM " . $this->_prefix . "multimedia_note AS n
				INNER JOIN " . $this->_prefix . "multimedia_file AS f 
					ON n.file_id = f.file_id";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("n.file_id = '%s'", mysql_real_escape_string($exp['file_id']));
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("n.is_active = '%s'", mysql_real_escape_string($exp['is_active']));
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY note_id DESC";
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
		$sql = "SELECT COUNT(*) AS num_notes
				FROM " . $this->_prefix . "multimedia_note AS n";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("n.file_id = '%s'", mysql_real_escape_string($exp['file_id']));
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("n.is_active = '%s'", mysql_real_escape_string($exp['is_active']));
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_notes;
	}
	
	public function updateStatus($id, $status)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "multimedia_note 
						SET is_active = '%s'
						WHERE note_id = '%s'",
						mysql_real_escape_string($status),
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
