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
 * @version 	$Id: Page.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Page extends Tomato_Model_Dao 
	implements Core_Models_Interface_Page
{
	public function convert($entity)
	{
		return new Core_Models_Page($entity); 
	}
	
	public function getOrdered()
	{
		$rs = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_page'))
					->order('p.ordering ASC')
					->order('p.route ASC')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function reupdateOrder()
	{
		$pages = $this->getOrdered();
		foreach ($pages as $index => $page) {
			$this->_conn->update($this->_prefix . 'core_page',
					array('ordering' => $page->ordering + 1),
					array('page_id = ?'  => $page->page_id)
			);
		}
		return $index;
	}
	
	public function updateOrder($pageId = null, $order)
	{
		if (null == $pageId) {
			$this->_conn->update($this->_prefix . 'core_page', array('ordering' => $order));			
		} else {
			$this->_conn->update($this->_prefix . 'core_page', 
								array('ordering' => $order), 
								array(
									'page_id = ?' => $pageId,
								));
		}
	}
	
	public function getByRoute($routeName)
	{
		$row = $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'core_page'))
					->where('p.route = ?', $routeName)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Core_Models_Page($row);		
	}
	
	public function add($page)
	{
		$this->_conn->insert($this->_prefix . 'core_page', 
							array(
								'route'    => $page->route,
								'title'    => $page->title,
								'ordering' => $page->ordering,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_page');		
	}
	
	public function delete($id)
	{
		return $this->_conn->delete($this->_prefix . 'core_page', 
									array(
										'page_id = ?' => $id,
									));	
	}
	
	public function update($page)
	{
		return $this->_conn->update($this->_prefix . 'core_page', 
									array(
										'title' => $page->title,
										'route' => $page->route,
									),
									array(
										'page_id = ?' => $page->page_id,
									));	
	}
}
