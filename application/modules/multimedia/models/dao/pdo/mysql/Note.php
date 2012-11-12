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
 * @version 	$Id: Note.php 5339 2010-09-07 08:38:12Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Pdo_Mysql_Note extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Note
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Note($entity); 
	}
	
	public function add($note)
	{
		$this->_conn->insert($this->_prefix . 'multimedia_note', 
							array(
								'file_id' 	   => $note->file_id,
								'top' 		   => $note->top,
								'left' 		   => $note->left,
								'width' 	   => $note->width,
								'height' 	   => $note->height,
								'content' 	   => $note->content,
								'user_id' 	   => $note->user_id,
								'user_name'    => $note->user_name,
								'created_date' => date('Y-m-d H:i:s'),
							));
		return $this->_conn->lastInsertId($this->_prefix . 'multimedia_note');
	}
	
	public function delete($id)
	{
		return $this->_conn->delete($this->_prefix . 'multimedia_note', 
									array(
										'note_id = ?' => $id,
									));
	}
	
	public function update($note)
	{
		return $this->_conn->update($this->_prefix . 'multimedia_note', 
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
							'note_id = ?' => $note->note_id,
						));
	}
	
	public function find($offset = null, $count = null, $exp = null)
	{
		$select = $this->_conn
						->select()
						->from(array('n' => $this->_prefix . 'multimedia_note'))
						->joinInner(array('f' => $this->_prefix . 'multimedia_file'), 'n.file_id = f.file_id', 
							array('image_square', 'image_thumbnail', 'image_small', 'image_crop', 'image_medium', 'image_large'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('n.file_id = ?', $exp['file_id']);
			}
			if (isset($exp['is_active'])) {
				$select->where('n.is_active = ?', $exp['is_active']);
			}
		}
		$select->order('note_id DESC');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll(); 
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp = null)
	{
		$select = $this->_conn
						->select()
						->from(array('n' => $this->_prefix . 'multimedia_note'), array('num_notes' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('file_id = ?', $exp['file_id']);
			}
			if (isset($exp['is_active'])) {
				$select->where('is_active = ?', $exp['is_active']);
			}
		}
		return $select->query()->fetch()->num_notes;
	}
	
	public function updateStatus($id, $status)
	{
		return $this->_conn->update($this->_prefix . 'multimedia_note', 
									array(
										'is_active' => $status,
									),
									array(
										'note_id = ?' => $id,
									));
	}	
}
