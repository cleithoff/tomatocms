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
 * @version 	$Id: Template.php 5440 2010-09-15 06:45:36Z leha $
 * @since		2.0.6
 */

class Mail_Models_Dao_Pgsql_Template extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Template
{
	public function convert($entity) 
	{
		return new Mail_Models_Template($entity); 
	}

	public function getByName($name)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail_template 
						WHERE name = '%s' LIMIT 1", 
						pg_escape_string($name));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Mail_Models_Template(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function getById($id)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail_template 
						WHERE template_id = %s 
						LIMIT 1", 
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Mail_Models_Template(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function getTemplates($userId, $offset = null, $count = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail_template 
						WHERE created_user_id = %s
						ORDER BY template_id DESC",
						pg_escape_string($userId));	
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(' LIMIT %s OFFSET %s', $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($userId)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_templates FROM " . $this->_prefix . "mail_template AS m
						WHERE created_user_id = %s LIMIT 1",
						pg_escape_string($userId));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_templates;
	}
	
	public function add($template)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "mail_template (name, title, subject, body, from_mail, from_name, 
							reply_to_mail, reply_to_name, created_user_id, locked)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s',
							'%s', '%s', %s, %s)
						RETURNING template_id",
						pg_escape_string($template->name),
						pg_escape_string($template->title),
						pg_escape_string($template->subject),
						pg_escape_string($template->body),
						pg_escape_string($template->from_mail),
						pg_escape_string($template->from_name),
						pg_escape_string($template->reply_to_mail),
						pg_escape_string($template->reply_to_name),
						pg_escape_string($template->created_user_id),
						pg_escape_string($template->locked));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->template_id;
	}
	
	public function delete($id)
	{
		return pg_delete($this->_conn, $this->_prefix . 'mail_template', 
						array(
							'template_id' => $id,
						));		
}
	
	public function update($template)
	{
		return pg_update($this->_conn, $this->_prefix . 'mail_template', 
							array(
								'name' 			  => $template->name,
								'title' 		  => $template->title,
								'subject' 		  => $template->subject,
								'body' 			  => $template->body,
								'from_mail' 	  => $template->from_mail,
								'from_name' 	  => $template->from_name,
								'reply_to_mail'   => $template->reply_to_mail,
								'reply_to_name'   => $template->reply_to_name,
								'created_user_id' => $template->created_user_id,
								'locked'		  => $template->locked,
							), 
							array(
								'template_id' => $template->template_id, 
							));
	}
}
