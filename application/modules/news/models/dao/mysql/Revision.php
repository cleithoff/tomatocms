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
 * @version 	$Id: Revision.php 5340 2010-09-07 08:50:11Z huuphuoc $
 * @since		2.0.5
 */

class News_Models_Dao_Mysql_Revision extends Tomato_Model_Dao
	implements News_Models_Interface_Revision
{
	public function convert($entity) 
	{
		return new News_Models_Revision($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "news_article_revision 
						WHERE revision_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new News_Models_Revision(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($revision) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "news_article_revision (article_id, category_id, title, sub_title, slug,
						description, content, created_date, created_user_id, created_user_name, author, icons)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($revision->article_id),
						mysql_real_escape_string($revision->category_id),
						mysql_real_escape_string($revision->title),
						mysql_real_escape_string($revision->sub_title),
						mysql_real_escape_string($revision->slug),
						mysql_real_escape_string($revision->description),
						mysql_real_escape_string($revision->content),
						mysql_real_escape_string($revision->created_date),
						mysql_real_escape_string($revision->created_user_id),
						mysql_real_escape_string($revision->created_user_name),
						mysql_real_escape_string($revision->author),
						mysql_real_escape_string($revision->icons));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = "SELECT * FROM " . $this->_prefix . "news_article_revision";
		if ($exp) {
			if (isset($exp['article_id'])) {
				$sql .= sprintf(" WHERE article_id = '%s'", mysql_real_escape_string($exp['article_id']));
			}
		}
		$sql .= " ORDER BY created_date DESC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql = "SELECT COUNT(*) AS num_revisions FROM " . $this->_prefix . "news_article_revision";
		if ($exp) {
			if (isset($exp['article_id'])) {
				$sql .= sprintf(" WHERE article_id = '%s'", mysql_real_escape_string($exp['article_id']));
			}
		}
		$sql .= " LIMIT 1";
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_revisions;
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "news_article_revision WHERE revision_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}	
}
