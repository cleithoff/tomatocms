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
 * @version 	$Id: Note.php 5044 2010-08-28 18:03:33Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Sqlsrv_Note extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Note
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Note($entity); 
	}
	
	public function add($note)
	{
		$this->_conn->insert($this->_prefix . 'multimedia_note', array(
			'file_id' 	   => $note->file_id,
			'top'		   => $note->top,
			'left'		   => $note->left,
			'width'		   => $note->width,
			'height'	   => $note->height,
			'content'	   => $note->content,
			'user_id'	   => $note->user_id,
			'user_name'	   => $note->user_name,
			'created_date' => date('Y-m-d H:i:s'),
		));
		return $this->_conn->lastInsertId($this->_prefix . 'multimedia_note');
	}
	
	public function delete($id)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'multimedia_note WHERE note_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $numRows;
	}
	
	public function update($note)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'multimedia_note
				SET top = ?, left = ?, width = ?, height = ?, content = ?, user_id = ?, user_name = ?, created_date = ?
				WHERE note_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$note->top,
			$note->left,
			$note->width,
			$note->height,
			$note->content,
			$note->user_id,
			$note->user_name,
			date('Y-m-d H:i:s'),
			$note->note_id
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$sql    = 'SELECT n.*, f.image_square, f.image_thumbnail, f.image_small, f.image_crop, f.image_medium, f.image_large 
					FROM ' . $this->_prefix . 'multimedia_note AS n
					INNER JOIN ' . $this->_prefix . 'multimedia_file AS f 
						ON n.file_id = f.file_id';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[]  = 'n.file_id = ?';
				$params[] = $exp['file_id'];
			}
			if (isset($exp['is_active'])) {
				$where[]  = 'n.is_active = ?';
				$params[] = $exp['is_active'];
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' ORDER BY note_id DESC';
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
		$sql    = 'SELECT COUNT(*) AS num_notes
					FROM ' . $this->_prefix . 'multimedia_note AS n';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['file_id'])) {
				$where[]  = 'n.file_id = ?';
				$params[] = $exp['file_id'];
			}
			if (isset($exp['is_active'])) {
				$where[]  = 'n.is_active = ?';
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
		return $row->num_notes;
	}
	
	public function updateStatus($id, $status)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'multimedia_note 
				SET is_active = ? 
				WHERE note_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($status, $id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();						
		return $numRows;
	}	
}
