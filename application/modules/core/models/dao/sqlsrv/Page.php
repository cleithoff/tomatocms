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
 * @version 	$Id: Page.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_Page extends Tomato_Model_Dao 
	implements Core_Models_Interface_Page
{
	public function convert($entity)
	{
		return new Core_Models_Page($entity); 
	}
	
	public function getOrdered()
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_page ORDER BY ordering ASC, route ASC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function reupdateOrder()
	{
		$sql   = 'UPDATE ' . $this->_prefix . 'core_page SET ordering = ? WHERE page_id = ?';
		$stmt  = $this->_conn->prepare($sql);
		$pages = $this->getOrdered();
		for ($i = 0; $i < count($pages); $i++) {
			$stmt->execute(array($id, $pages[$i]->page_id));
		}
		return $i;
	}
	
	public function updateOrder($pageId = null, $order)
	{
		if (null == $pageId) {
			$sql  = 'UPDATE ' . $this->_prefix . 'core_page SET ordering = ?';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($order));
		} else {
			$sql = 	'UPDATE ' . $this->_prefix . 'core_page SET ordering = ? WHERE page_id = ?';
			$stmt = $this->_conn->prepare($sql);
			$stmt->execute(array($order, $pageId));
		}
	}
	
	public function getByRoute($routeName)
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'core_page WHERE route = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($routeName));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Core_Models_Page($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($page)
	{
		$this->_conn->insert($this->_prefix . 'core_page', array(
			'route'	   => $page->route,
			'title'	   => $page->title,
			'ordering' => $page->ordering,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_page');		
	}
	
	public function delete($id)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_page WHERE page_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $row;
	}
	
	public function update($page)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'core_page
				SET title = ?, route = ?
				WHERE page_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$page->title,
			$page->route,
			$page->page_id,
		));
		$row = $stmt->rowCount();
		$stmt->closeCursor();
		return $row;
	}
}
