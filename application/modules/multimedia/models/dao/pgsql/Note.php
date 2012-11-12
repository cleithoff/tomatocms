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
 * @version 	$Id: Note.php 5448 2010-09-15 09:09:11Z leha $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Pgsql_Note extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Note
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Note($entity); 
	}
	
	public function add($note)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "multimedia_note (file_id, top, left, width, height, content, user_id, user_name, created_date)
						VALUES (%s, %s, %s, %s, %s, '%s', %s, '%s', '%s')
						RETURNING note_id",
						pg_escape_string($note->file_id), 
						pg_escape_string($note->top), 
						pg_escape_string($note->left),
						pg_escape_string($note->width), 
						pg_escape_string($note->height), 
						pg_escape_string($note->content),
						pg_escape_string($note->user_id), 
						pg_escape_string($note->user_name), 
						date('Y-m-d H:i:s'));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->note_id;
	}
	
	public function delete($id)
	{
		return pg_delete($this->_conn, $this->_prefix . 'multimedia_note', 
						array(
							'note_id' => $id,
						));
	}
	
	public function update($note)
	{
		return pg_update($this->_conn, $this->_prefix . 'multimedia_note', 
						array(
							'top' 		   => $note->top,
							'left' 		   => $note->left,
							'width' 	   => $note->width,
							'height' 	   => $note->height,
							'content' 	   => $note->content,
							'user_id' 	   => $note->user_id,
							'user_name'    => $note->user_name,
							'created_date' => date('Y-m-d H:i:s'),
						),
						array(
							'note_id' => $note->note_id,
						));
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$sql = "SELECT n.*, f.image_thumbnail, f.image_medium, f.image_crop, f.image_small, f.image_square, f.image_large 
				FROM " . $this->_prefix . "multimedia_note AS n
				INNER JOIN " . $this->_prefix . "multimedia_file AS f 
					ON n.file_id = f.file_id";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("n.file_id = %s", pg_escape_string($exp['file_id']));
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("n.is_active = '%s'", pg_escape_string($exp['is_active']));
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY note_id DESC";
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
		$sql = "SELECT COUNT(*) AS num_notes
				FROM " . $this->_prefix . "multimedia_note AS n";
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[] = sprintf("n.file_id = %s", pg_escape_string($exp['file_id']));
			}
			if (isset($exp['is_active'])) {
				$where[] = sprintf("n.is_active = '%s'", pg_escape_string($exp['is_active']));
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_notes;
	}
	
	public function updateStatus($id, $status)
	{
		return pg_update($this->_conn, $this->_prefix . 'multimedia_note', 
						array(
							'is_active' => $status,
						),
						array(
							'note_id' => $id,
						));
	}	
}
