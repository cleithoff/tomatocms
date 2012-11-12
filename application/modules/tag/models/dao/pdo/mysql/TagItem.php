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
 * @version 	$Id: TagItem.php 5344 2010-09-07 09:21:25Z huuphuoc $
 * @since		2.0.5
 */

class Tag_Models_Dao_Pdo_Mysql_TagItem extends Tomato_Model_Dao
	implements Tag_Models_Interface_TagItem
{
	public function convert($entity) 
	{
		return new Tag_Models_TagItem($entity);
	}
	
	public function delete($item)
	{
		return $this->_conn->delete($this->_prefix . 'tag_item_assoc', 
									array(
										'item_id = ?' 			 => $item->item_id,
										'item_name = ?' 		 => $item->item_name,
										'route_name = ?' 		 => $item->route_name,
										'details_route_name = ?' => $item->details_route_name,
									));
	}
	
	public function add($item)
	{
		$this->_conn->insert($this->_prefix . 'tag_item_assoc', 
							array(
								'tag_id' 			 => $item->tag_id,
								'item_id' 			 => $item->item_id,
								'item_name' 		 => $item->item_name,
								'route_name' 		 => $item->route_name,
								'details_route_name' => $item->details_route_name,
								'params' 			 => $item->item_name . ':' . $item->item_id,
							));
	}
	
	public function getTagCloud($routeName, $limit = null)
	{
		$select = $this->_conn
						->select()
						->from(array('ti' => $this->_prefix . 'tag_item_assoc'), array('details_route_name'))
						->joinInner(array('t' => $this->_prefix . 'tag'), 
									'ti.tag_id = t.tag_id', 
									array('tag_id', 'tag_text', 'num_items' => 'COUNT(*)'))
						->where('ti.route_name = ?', $routeName)
						->group('tag_text');
		if (is_numeric($limit)) {
			$select->limit($limit);	
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
}
