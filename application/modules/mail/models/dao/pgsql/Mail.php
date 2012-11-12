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
 * @version 	$Id: Mail.php 5035 2010-08-28 17:38:02Z huuphuoc $
 * @since		2.0.6
 */

class Mail_Models_Dao_Pgsql_Mail extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Mail
{
	public function convert($entity) 
	{
		return new Mail_Models_Mail($entity); 
	}
	
	public function getMails($userId, $offset = null, $count = null)
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "mail 
						WHERE created_user_id = %s
						ORDER BY mail_id DESC", 
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
		$sql = sprintf("SELECT COUNT(*) AS num_mails FROM " . $this->_prefix . "mail
						WHERE created_user_id = %s 
						LIMIT 1", 
						pg_escape_string($userId));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_mails;
	}
	
	public function add($mail)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "mail (template_id, subject, content, created_user_id, from_mail, 
							from_name, reply_to_mail, reply_to_name, to_mail, to_name, status, created_date, sent_date)
						VALUES (%s, '%s', '%s', %s, '%s',
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
						RETURNING mail_id",
						pg_escape_string($mail->template_id),
						pg_escape_string($mail->subject),
						pg_escape_string($mail->content),
						pg_escape_string($mail->created_user_id),
						pg_escape_string($mail->from_mail),
						pg_escape_string($mail->from_name),
						pg_escape_string($mail->reply_to_mail),
						pg_escape_string($mail->reply_to_name),
						pg_escape_string($mail->to_mail),
						pg_escape_string($mail->to_name),
						pg_escape_string($mail->status),
						pg_escape_string($mail->created_date),
						pg_escape_string($mail->sent_date));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);	
		return $row->mail_id;		
	}
}
