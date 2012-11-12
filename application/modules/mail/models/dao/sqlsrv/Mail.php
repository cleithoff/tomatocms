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
 * @version 	$Id: Mail.php 5036 2010-08-28 17:40:13Z huuphuoc $
 * @since		2.0.6
 */

class Mail_Models_Dao_Sqlsrv_Mail extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Mail
{
	public function convert($entity) 
	{
		return new Mail_Models_Mail($entity); 
	}
	
	public function getMails($userId, $offset = null, $count = null)
	{
		$sql = 'SELECT m.* FROM ' . $this->_prefix . 'mail AS m 
				WHERE m.created_user_id = ? 
				ORDER BY mail_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($userId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($userId)
	{
		$sql = 'SELECT TOP 1 COUNT(*) AS num_mails FROM ' . $this->_prefix . 'mail AS m 
				WHERE m.created_user_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($userId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_mails;
	}
	
	public function add($mail)
	{	
		$this->_conn->insert($this->_prefix . 'mail', array(
			'template_id' 	  => $mail->template_id,
			'subject' 		  => $mail->subject,
			'content' 		  => $mail->content,
			'created_user_id' => $mail->created_user_id,
			'from_mail' 	  => $mail->from_mail,
			'from_name' 	  => $mail->from_name,
			'reply_to_mail'   => $mail->reply_to_mail,
			'reply_to_name'   => $mail->reply_to_name,
			'to_mail' 		  => $mail->to_mail,
			'to_name'         => $mail->to_name,
			'status' 		  => $mail->status,
			'created_date'    => $mail->created_date,
			'sent_date' 	  => $mail->sent_date,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'mail');
	}
}
