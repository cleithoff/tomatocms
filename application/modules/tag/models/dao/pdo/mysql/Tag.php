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
 * @version 	$Id: Tag.php 5344 2010-09-07 09:21:25Z huuphuoc $
 * @since		2.0.5
 */

class Tag_Models_Dao_Pdo_Mysql_Tag extends Tomato_Model_Dao
	implements Tag_Models_Interface_Tag
{
	public function convert($entity) 
	{
		return new Tag_Models_Tag($entity);
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'tag'))
					->where('t.tag_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Tag_Models_Tag($row);
	}
	
	public function exist($text)
	{
		$row = $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'tag'), array('num_tags' => 'COUNT(*)'))
					->where('t.tag_text = ?', $text)
					->limit(1)
					->query()
					->fetch();
		return ($row->num_tags > 0);
	}
	
	public function add($tag) 
	{
		$this->_conn->insert($this->_prefix . 'tag', 
							array(
								'tag_text' => $tag->tag_text,
							));	
		return $this->_conn->lastInsertId($this->_prefix . 'tag');
	}
	
	public function delete($tagId) 
	{
		$where['tag_id = ?'] = $tagId;
		$this->_conn->delete($this->_prefix . 'tag_item_assoc', $where);
		return $this->_conn->delete($this->_prefix . 'tag', $where);
	}
	
	public function find($keyword, $offset, $count)
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix . 'tag'));
		if ($keyword != '') {
			$select->where("t.tag_text LIKE '%" . addslashes($keyword) . "%'");
		}
		$select->order('tag_id');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($keyword)
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix . 'tag'), array('num_tags' => 'COUNT(*)'));
		if ($keyword != '') {
			$select->where("t.tag_text LIKE '%" . addslashes($keyword) . "%'");
		}
		return $select->limit(1)->query()->fetch()->num_tags;
	}
	
	public function getByItem($item)
	{
		$rs = $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'tag'), array('tag_id', 'tag_text'))
					->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 't.tag_id = ti.tag_id', array('details_route_name'))
					->where('ti.item_id = ?', $item->item_id)
					->where('ti.item_name = ?', $item->item_name)
					->where('ti.route_name = ?', $item->route_name)
					->where('ti.details_route_name = ?', $item->details_route_name)
					->group('t.tag_id')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);		
	}
	
	public function getByRoute($item, $limit = null)
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix . 'tag'), array('tag_id', 'tag_text'))
						->joinInner(array('ti' => $this->_prefix . 'tag_item_assoc'), 't.tag_id = ti.tag_id', array('details_route_name'))
						->where('ti.route_name = ?', $item->route_name)
						->where("LOCATE(CONCAT('|', ti.params, '|'), '" . addslashes($item->params) . "') > 0");
		if (is_numeric($limit)) {
			$select->limit($limit);		
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);		
	}
}
