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
 * @version 	$Id: Set.php 5339 2010-09-07 08:38:12Z huuphuoc $
 * @since		2.0.5
 */

class Multimedia_Models_Dao_Pdo_Mysql_Set extends Tomato_Model_Dao
	implements Multimedia_Models_Interface_Set
{
	public function convert($entity) 
	{
		return new Multimedia_Models_Set($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('s' => $this->_prefix . 'multimedia_set'))
					->where('s.set_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Multimedia_Models_Set($row);	
	}
	
	public function add($set)
	{
		$this->_conn->insert($this->_prefix . 'multimedia_set', 
							array(
								'title' 			=> $set->title,
								'slug' 				=> $set->slug,
								'description' 		=> $set->description,
								'created_date' 		=> $set->created_date,
								'created_user_id' 	=> $set->created_user_id,
								'created_user_name' => $set->created_user_name,
								'image_square' 		=> $set->image_square,
								'image_thumbnail' 	=> $set->image_thumbnail,
								'image_small' 		=> $set->image_small,
								'image_crop' 		=> $set->image_crop,
								'image_medium' 		=> $set->image_medium,
								'image_large' 		=> $set->image_large,
								'is_active' 		=> $set->is_active,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'multimedia_set');
	}
	
	public function update($set) 
	{
		return $this->_conn->update($this->_prefix . 'multimedia_set', 
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
										'set_id = ?' => $set->set_id,
									));			
	}
	
	public function find($offset = null, $count = null, $exp = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('s' => $this->_prefix . 'multimedia_set'));
		if ($exp) {
			if (isset($exp['created_user_id'])) {
				$select->where('s.created_user_id = ?', $exp['created_user_id']);
			}
			if (isset($exp['keyword'])) {
				$select->where("s.title LIKE '%" . addslashes($exp['keyword']) . "%'");
			}
			if (isset($exp['is_active'])) {
				$select->where('s.is_active = ?', (int)$exp['is_active']);
			}
		}
		$select->order('s.set_id DESC');
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
						->from(array('s' => $this->_prefix . 'multimedia_set'), array('num_sets' => 'COUNT(*)'));
		if ($exp) {
			if (isset($exp['created_user'])) {
				$select->where('s.created_user_id = ?', $exp['created_user']);
			}
			if (isset($exp['keyword'])) {
				$select->where("s.title LIKE '%" . addslashes($exp['keyword']) . "%'");
			}
		}
		return $select->query()->fetch()->num_sets;
	}
	
	public function delete($id)
	{
		$where['set_id = ?'] = $id;
		$this->_conn->delete($this->_prefix . 'multimedia_file_set_assoc', $where);
		return $this->_conn->delete($this->_prefix . 'multimedia_set', $where);
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
		return $this->_conn->update($this->_prefix . 'multimedia_set', 
									$data,
									array(
										'set_id = ?' => $setId,
									));
	}
	
	public function toggleStatus($id) 
	{
		return $this->_conn->update($this->_prefix . 'multimedia_set', 
									array(
										'is_active' => new Zend_Db_Expr('1 - is_active'),
									),
									array(
										'set_id = ?' => $id,
									));
	}
	
	public function getByTag($tagId, $offset, $count)
	{
		$rs = $this->_conn
					->select()
					->from(array('s' => $this->_prefix . 'multimedia_set'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 's.set_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'set_id')
					->where('s.is_active = ?', 1)
					//->group('s.set_id')
					->order('s.set_id DESC')
					->limit($count, $offset)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function countByTag($tagId)
	{
		return $this->_conn
					->select()
					->from(array('s' => $this->_prefix . 'multimedia_set'), array('num_sets' => 'COUNT(set_id)'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 's.set_id = ti.item_id', array())
					->where('ti.tag_id = ?', $tagId)
					->where('ti.item_name = ?', 'set_id')
					->where('s.is_active = ?', 1)
					->query()
					->fetch()
					->num_sets;
	}
}
