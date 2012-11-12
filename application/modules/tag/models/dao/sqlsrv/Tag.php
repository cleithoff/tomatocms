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
 * @version 	$Id: Tag.php 5061 2010-08-28 18:48:02Z huuphuoc $
 * @since		2.0.5
 */

class Tag_Models_Dao_Sqlsrv_Tag extends Tomato_Model_Dao
	implements Tag_Models_Interface_Tag
{
	public function convert($entity) 
	{
		return new Tag_Models_Tag($entity);
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'tag 
				WHERE tag_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Tag_Models_Tag($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function exist($text)
	{
		$sql  = 'SELECT TOP 1 COUNT(*) AS num_tags
				FROM ' . $this->_prefix . 'tag AS t
				WHERE t.tag_text = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($text));
		$row = $stmt->fetch();
		$stmt->closeCursor();						
		return ($row->num_tags > 0);
	}
	
	public function add($tag) 
	{
		$this->_conn->insert($this->_prefix . 'tag', array(
			'tag_text' => $tag->tag_text,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'tag');
	}
	
	public function delete($tagId) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'tag_item_assoc WHERE tag_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId));
		
		$sql  = 'DELETE FROM ' . $this->_prefix . 'tag WHERE tag_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($tagId));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function find($keyword, $offset, $count)
	{
		$sql = 'SELECT * FROM ' . $this->_prefix . 'tag AS t';
		if ($keyword != '') {
			$sql .= " WHERE t.tag_text LIKE '%" . addslashes($keyword) . "%'";
		}
		$sql .= ' ORDER BY tag_id';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($keyword)
	{
		$sql = 'SELECT COUNT(*) AS num_tags FROM ' . $this->_prefix . 'tag AS t';
		if ($keyword != '') {
			$sql .= " WHERE t.tag_text LIKE '%" . addslashes($keyword) . "%'";
		}
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_tags;
	}
	
	public function getByItem($item)
	{
		$sql  = 'SELECT t.tag_id, t.tag_text, ti.details_route_name
				FROM ' . $this->_prefix . 'tag AS t
				INNER JOIN ' . $this->_prefix . 'tag_item_assoc AS ti
					ON t.tag_id = ti.tag_id
				WHERE ti.item_id = ?
					AND ti.item_name = ?
					AND ti.route_name = ?
					AND ti.details_route_name = ?
				GROUP BY t.tag_id, t.tag_text, ti.details_route_name';
		$stmt = $this->_conn->prepare($sql);
		
		$stmt->execute(array(
			$item->item_id,
			$item->item_name,
			$item->route_name,
			$item->details_route_name,
		));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);		
	}
	
	public function getByRoute($item, $limit = null)
	{
		$sql = 'SELECT t.tag_id, t.tag_text, ti.details_route_name
				FROM ' . $this->_prefix . 'tag AS t
				INNER JOIN '.$this->_prefix."tag_item_assoc AS ti
					ON t.tag_id = ti.tag_id
				WHERE ti.route_name = ?
					AND CHARINDEX('|' + ti.params + '|', ?) > 0";
		if (is_numeric($limit)) {
			$sql = $this->_conn->limit($sql, $limit);			
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($item->route_name, $item->params)); 
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
