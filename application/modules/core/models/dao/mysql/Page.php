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
 * @version 	$Id: Page.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Page extends Tomato_Model_Dao 
	implements Core_Models_Interface_Page
{
	public function convert($entity)
	{
		return new Core_Models_Page($entity); 
	}
	
	public function getOrdered()
	{
		$sql  = "SELECT * FROM " . $this->_prefix . "core_page ORDER BY ordering ASC, route ASC";
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function reupdateOrder()
	{
		$sql   = "UPDATE " . $this->_prefix . "core_page SET ordering = '%s' WHERE page_id = '%s'";
		$pages = $this->getOrdered();
		for ($i = 0; $i < count($pages); $i++) {
			mysql_query(sprintf($sql, $i, mysql_real_escape_string($pages[$i]->page_id)));
		}
		return $i;
	}
	
	public function updateOrder($pageId = null, $order)
	{
		if (null == $pageId) {
			$sql = sprintf("UPDATE " . $this->_prefix . "core_page SET ordering = '%s'",
							mysql_real_escape_string($order));
			mysql_query($sql);
		} else {
			$sql = 	sprintf("UPDATE " . $this->_prefix . "core_page SET ordering = '%s' WHERE page_id = '%s'",
							mysql_real_escape_string($order),
							mysql_real_escape_string($pageId));
			mysql_query($sql);			
		}
	}
	
	public function getByRoute($routeName)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "core_page
						WHERE route = '%s' LIMIT 1",
						mysql_real_escape_string($routeName));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Core_Models_Page(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($page)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_page (route, title, ordering)
						VALUES ('%s', '%s', '%s')",
						mysql_real_escape_string($page->route),
						mysql_real_escape_string($page->title),
						mysql_real_escape_string($page->ordering));
		mysql_query($sql);
		return mysql_insert_id();		
	}
	
	public function delete($id)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_page WHERE page_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($page)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_page
						SET title = '%s', route = '%s'
						WHERE page_id = '%s'",
						mysql_real_escape_string($page->title),
						mysql_real_escape_string($page->route),
						mysql_real_escape_string($page->page_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
