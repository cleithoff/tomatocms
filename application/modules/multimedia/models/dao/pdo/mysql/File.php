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
 * @version 	$Id: File.php 5339 2010-09-07 08:38:12Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Pdo_Mysql_File extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_File
{
	public function convert($entity) 
	{
		return new Multimedia_Models_File($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('f' => $this->_prefix . 'multimedia_file'))
					->where('f.file_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Multimedia_Models_File($row);
	}
	
	public function add($file) 
	{
		$this->_conn->insert($this->_prefix . 'multimedia_file', 
							array(
								'title' 			=> $file->title,
								'slug' 				=> $file->slug,
								'description' 		=> $file->description,
								'content' 			=> $file->content,
								'created_date' 		=> $file->created_date,
								'created_user' 		=> $file->created_user,
								'created_user_name' => $file->created_user_name,
								'image_square' 		=> $file->image_square,
								'image_thumbnail' 	=> $file->image_thumbnail,
								'image_small' 		=> $file->image_small,
								'image_crop' 		=> $file->image_crop,
								'image_medium' 		=> $file->image_medium,
								'image_large' 		=> $file->image_large,
								'image_original' 	=> $file->image_original,
								'url' 				=> $file->url,
								'html_code' 		=> $file->html_code,
								'file_type' 		=> $file->file_type,
								'is_active' 		=> $file->is_active,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'multimedia_file');
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('f' => $this->_prefix . 'multimedia_file'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('f.file_id = ?', $exp['file_id']);
			}
			if (isset($exp['created_user'])) {
				$select->where('f.created_user = ?', $exp['created_user']);
			}
			if (isset($exp['file_type'])) {
				$select->where('f.file_type = ?', $exp['file_type']);
			}
			
			/**
			 * TODO: Make a simpler check
			 */
			if ((isset($exp['photo']) && '1' == $exp['photo']) && (isset($exp['clip']) && '1' == $exp['clip'])) {
				$select->where("(f.file_type = 'image' OR f.file_type = 'video')");
			} elseif (isset($exp['photo']) && '1' == $exp['photo']) {
				$select->where('f.file_type = ?', 'image');
			} elseif (isset($exp['clip']) && '1' == $exp['clip']) {
				$select->where('f.file_type = ?', 'video');
			}
			if (isset($exp['keyword'])) {
				$select->where("f.title LIKE '%" . addslashes($exp['keyword']) . "%'");
			}
			if (isset($exp['is_active'])) {
				$select->where('f.is_active = ?', (int)$exp['is_active']);
			}
		}
		$rs = $select->order('f.file_id DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('f' => $this->_prefix . 'multimedia_file'), array('num_files' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['file_id'])) {
				$select->where('f.file_id = ?', $exp['file_id']);
			}
			if (isset($exp['created_user'])) {
				$select->where('f.created_user = ?', $exp['created_user']);
			}
			if (isset($exp['file_type'])) {
				$select->where('f.file_type = ?', $exp['file_type']);
			}
			
			/**
			 * TODO: Make a simpler check
			 */
			if ((isset($exp['photo']) && '1' == $exp['photo']) && (isset($exp['clip']) && '1' == $exp['clip'])) {
				$select->where("(f.file_type = 'image' OR f.file_type = 'video')");
			} elseif (isset($exp['photo']) && '1' == $exp['photo']) {
				$select->where('f.file_type = ?', 'image');
			} elseif (isset($exp['clip']) && '1' == $exp['clip']) {
				$select->where('f.file_type = ?', 'video');
			}
			if (isset($exp['keyword'])) {
				$select->where("f.title LIKE '%" . addslashes($exp['keyword']) . "%'");
			}
			if (isset($exp['is_active'])) {
				$select->where('f.is_active = ?', (int)$exp['is_active']);
			}
		}
		return $select->query()
					->fetch()
					->num_files;
	}
	
	public function delete($id) 
	{
		return $this->_conn->delete($this->_prefix . 'multimedia_file', 
									array(
										'file_id = ?' => $id,
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
		return $this->_conn->update($this->_prefix . 'multimedia_file', 
									$data, 
									array(
										'file_id = ?' => $fileId,
									));
	}
	
	public function toggleStatus($id) 
	{
		return $this->_conn->update($this->_prefix . 'multimedia_file', 
									array(
										'is_active' => new Zend_Db_Expr('1 - is_active'),
									),
									array(
										'file_id = ?' => $id,
									));
	}
	
	public function update($file) 
	{
		return $this->_conn->update($this->_prefix . 'multimedia_file', 
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
										'file_id = ?' => $file->file_id,
									));	
	}
	
	public function getFilesInSet($setId, $offset = null, $count = null, $isActive = null)
	{
		$select = $this->_conn
						->select()
						->from(array('f' => $this->_prefix . 'multimedia_file'))
						->joinInner(array('fs' => $this->_prefix . 'multimedia_file_set_assoc'), 'fs.file_id = f.file_id AND fs.set_id = ' . $this->_conn->quote($setId), array());
		if (is_bool($isActive)) {
			$select->where('f.is_active = ?', (int)$isActive);
		}
		$select->order('f.file_id DESC');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function countFilesInSet($setId, $isActive = null)
	{
		$select = $this->_conn
						->select()
						->from(array('f' => $this->_prefix . 'multimedia_file'), array('num_files' => 'COUNT(*)'))
						->joinInner(array('fs' => $this->_prefix . 'multimedia_file_set_assoc'), 'fs.file_id = f.file_id AND fs.set_id = ' . $this->_conn->quote($setId), array());
		if (is_bool($isActive)) {
			$select->where('f.is_active = ?', (int)$isActive);
		}
		return $select->limit(1)->query()->fetch()->num_files;
	}
	
	public function removeFromSet($setId, $fileId = null)
	{
		$where['set_id = ?'] = $setId;
		if ($fileId != null) {
			$where['file_id = ?'] = $fileId;
		}
		return $this->_conn->delete($this->_prefix . 'multimedia_file_set_assoc', $where);
	}
	
	public function addToSet($setId, $fileId)
	{
		$this->_conn->insert($this->_prefix . 'multimedia_file_set_assoc', 
							array(
								'file_id' => $fileId,
								'set_id'  => $setId,
							));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$rs = $this->_conn
					->select()
					->from(array('f' => $this->_prefix . 'multimedia_file'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 'f.file_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'file_id')
					->where('f.is_active = ?', 1)
					//->group('f.file_id')
					->order('f.file_id DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function countByTag($tagId)
	{
		return $this->_conn
					->select()
					->from(array('f' => $this->_prefix . 'multimedia_file'), array('num_files' => 'COUNT(file_id)'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 'f.file_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'file_id')
					->where('f.is_active = ?', 1)
					->query()
					->fetch()
					->num_files;
	}	
}
