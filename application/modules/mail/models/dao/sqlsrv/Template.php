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
 * @version 	$Id: Template.php 5036 2010-08-28 17:40:13Z huuphuoc $
 * @since		2.0.6
 */


class Mail_Models_Dao_Sqlsrv_Template extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Template
{
	public function convert($entity) 
	{
		return new Mail_Models_Template($entity); 
	}

	public function getByName($name)
	{
		$sql  = 'SELECT TOP 1 t.* FROM ' . $this->_prefix . 'mail_template AS t 
				WHERE t.name = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($name));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Mail_Models_Template($row);
	}
	
	public function getById($id)
	{
		$sql  = 'SELECT TOP 1 t.* FROM ' . $this->_prefix . 'mail_template AS t 
				WHERE t.template_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Mail_Models_Template($row);
	}
	
	public function getTemplates($userId, $offset = null, $count = null)
	{
		$sql = 'SELECT t.* FROM ' . $this->_prefix . 'mail_template AS t 
				WHERE t.created_user_id = ?
				ORDER BY template_id DESC';
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
		$sql  = 'SELECT TOP 1 COUNT(*) AS num_templates FROM ' .  $this->_prefix . 'mail_template AS t 
				WHERE t.created_user_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($userId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_templates;
	}
	
	public function add($template)
	{
		$this->_conn->insert($this->_prefix . 'mail_template', array(
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
		));
		return $this->_conn->lastInsertId($this->_prefix . 'mail_template');
	}
	
	public function delete($id)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'mail_template WHERE template_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor(); 
		return $numRows;
	}
	
	public function update($template)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'mail_template
				SET name = ?, title = ?, subject = ?, body = ?, from_mail = ?, from_name = ?, 
					reply_to_mail = ?, reply_to_name = ?, created_user_id = ?, locked = ?
				WHERE template_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$template->name,
			$template->title,
			$template->subject,
			$template->body,
			$template->from_mail,
			$template->from_name,
			$template->reply_to_mail,
			$template->reply_to_name,
			$template->created_user_id,
			$template->locked,
			$template->template_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
}
