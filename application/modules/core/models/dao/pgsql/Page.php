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
 * @version 	$Id: Page.php 5424 2010-09-14 08:40:14Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Page extends Tomato_Model_Dao 
	implements Core_Models_Interface_Page
{
	public function convert($entity)
	{
		return new Core_Models_Page($entity); 
	}
	
	public function getOrdered()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_page ORDER BY ordering ASC, route ASC";
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function reupdateOrder()
	{
		$sql   = "UPDATE " . $this->_prefix . "core_page SET ordering = %s WHERE page_id = %s";
		$pages = $this->getOrdered();
		for ($i = 0; $i < count($pages); $i++) {
			pg_query(sprintf($sql, $i, pg_escape_string($pages[$i]->page_id)));
		}
		return $i;
	}
	
	public function updateOrder($pageId = null, $order)
	{
		if (null == $pageId) {
			pg_update($this->_conn, $this->_prefix . 'core_page', array('ordering' => $order));
		} else {
			pg_update($this->_conn, $this->_prefix . 'core_page', 
						array('ordering' => $order), 
						array(
							'page_id' => $pageId,
						));	
		}
	}
	
	public function getByRoute($routeName)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_page
						WHERE route = '%s' LIMIT 1",
						pg_escape_string($routeName));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Core_Models_Page(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($page)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_page (route, title, ordering)
						VALUES ('%s', '%s', %s)
						RETURNING page_id",
						pg_escape_string($page->route),
						pg_escape_string($page->title),
						pg_escape_string($page->ordering));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->page_id;		
	}
	
	public function delete($id)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_page', 
						array(
							'page_id' => $id,
						));	
	}
	
	public function update($page)
	{
		return pg_update($this->_conn, $this->_prefix . 'core_page', 
						array(
							'title' => $page->title,
							'route' => $page->route,
						),
						array(
							'page_id' => $page->page_id,
						));	
	}
}
