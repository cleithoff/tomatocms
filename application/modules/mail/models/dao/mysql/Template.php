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
 * @version 	$Id: Template.php 5033 2010-08-28 17:34:27Z huuphuoc $
 * @since		2.0.5
 */


class Mail_Models_Dao_Mysql_Template extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Template
{
	public function convert($entity) 
	{
		return new Mail_Models_Template($entity); 
	}

	public function getByName($name)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail_template 
						WHERE name = '%s'
						LIMIT 1", 
						mysql_real_escape_string($name));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Mail_Models_Template(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function getById($id)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail_template 
						WHERE template_id = '%s'
						LIMIT 1",
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Mail_Models_Template(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function getTemplates($userId, $offset = null, $count = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail_template 
						WHERE created_user_id = '%s'
						ORDER BY template_id DESC", 
						mysql_real_escape_string($userId));
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
	
	public function count($userId)
	{
		$sql = sprintf("SELECT COUNT(*) AS num_templates 
						FROM " . $this->_prefix . "mail_template
						WHERE created_user_id = '%s' LIMIT 1", 
						mysql_real_escape_string($userId));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_templates;
	}
	
	public function add($template)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_client (name, email, telephone, address, created_date)
						VALUES ('%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($client->name),
						mysql_real_escape_string($client->email),
						mysql_real_escape_string($client->telephone),
						mysql_real_escape_string($client->address),
						mysql_real_escape_string($client->created_date));
		mysql_query($sql);
		return mysql_insert_id();
		
		$sql = sprintf("INSERT INTO " . $this->_prefix . "name, title, subject, body, from_mail, from_name, 
							reply_to_mail, reply_to_name, created_user_id, locked)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s',
							'%s', '%s', '%s', '%s')",
						mysql_real_escape_string($template->name),
						mysql_real_escape_string($template->title),
						mysql_real_escape_string($template->subject),
						mysql_real_escape_string($template->body),
						mysql_real_escape_string($template->from_mail),
						mysql_real_escape_string($template->from_name),
						mysql_real_escape_string($template->reply_to_mail),
						mysql_real_escape_string($template->reply_to_name),
						mysql_real_escape_string($template->created_user_id),
						mysql_real_escape_string($template->locked));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($id)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "mail_template WHERE template_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function update($template)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "mail_template
						SET name = '%s', title = '%s', subject = '%s', body = '%s',
							from_mail = '%s', from_name = '%s', reply_to_mail = '%s', 
							reply_to_name = '%s', created_user_id = %s, locked = %s
						WHERE template_id = %s",
						mysql_real_escape_string($template->name),
						mysql_real_escape_string($template->title),
						mysql_real_escape_string($template->subject),
						mysql_real_escape_string($template->body),
						mysql_real_escape_string($template->from_mail),
						mysql_real_escape_string($template->from_name),
						mysql_real_escape_string($template->reply_to_mail),
						mysql_real_escape_string($template->reply_to_name),
						mysql_real_escape_string($template->created_user_id),
						mysql_real_escape_string($template->locked),
						mysql_real_escape_string($template->template_id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
