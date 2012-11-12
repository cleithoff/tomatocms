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
 * @version 	$Id: Mail.php 5033 2010-08-28 17:34:27Z huuphuoc $
 * @since		2.0.5
 */

class Mail_Models_Dao_Mysql_Mail extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Mail
{
	public function convert($entity) 
	{
		return new Mail_Models_Mail($entity); 
	}
	
	public function getMails($userId, $offset = null, $count = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "mail
				ORDER BY mail_id DESC";
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
		$sql = sprintf("SELECT COUNT(*) AS num_mails FROM " . $this->_prefix . "mail
						WHERE created_user_id = %s 
						LIMIT 1", 
						mysql_real_escape_string($userId));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_mails;
	}
	
	public function add($mail)
	{
		$sql = sprintf("INSERT INTO ".$this->_prefix."mail (template_id, subject, content, created_user_id, from_mail, 
							from_name, reply_to_mail, reply_to_name, to_mail, to_name, status, created_date, sent_date)
						VALUES ('%s', '%s', '%s', %s, '%s',
							'%s', '%s', '%s', '%s', '%s', %s, '%s', '%s')",
						mysql_real_escape_string($mail->template_id),
						mysql_real_escape_string($mail->subject),
						mysql_real_escape_string($mail->content),
						mysql_real_escape_string($mail->created_user_id),
						mysql_real_escape_string($mail->from_mail),
						mysql_real_escape_string($mail->from_name),
						mysql_real_escape_string($mail->reply_to_mail),
						mysql_real_escape_string($mail->reply_to_name),
						mysql_real_escape_string($mail->to_mail),
						mysql_real_escape_string($mail->to_name),
						mysql_real_escape_string($mail->status),
						mysql_real_escape_string($mail->created_date),
						mysql_real_escape_string($mail->sent_date));
		mysql_query($sql);
		return mysql_insert_id();
	}
}
