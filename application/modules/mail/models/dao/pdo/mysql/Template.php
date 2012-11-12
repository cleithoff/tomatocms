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
 * @version 	$Id: Template.php 5337 2010-09-07 08:18:38Z huuphuoc $
 * @since		2.0.6
 */

class Mail_Models_Dao_Pdo_Mysql_Template extends Tomato_Model_Dao 
	implements Mail_Models_Interface_Template
{
	public function convert($entity) 
	{
		return new Mail_Models_Template($entity); 
	}

	public function getByName($name)
	{
		$row = $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'mail_template'))
					->where('t.name = ?', $name)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Mail_Models_Template($row);
	}
	
	public function getById($id)
	{
		$row = $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'mail_template'))
					->where('t.template_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Mail_Models_Template($row);
	}
	
	public function getTemplates($userId, $offset = null, $count = null)
	{
		$select = $this->_conn
						->select()
						->from(array('t' => $this->_prefix . 'mail_template'))
						->where('t.created_user_id = ?', $userId);
		if (is_int($offset) && is_int($count)) {
			$select->order('t.template_id DESC')
					->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count($userId)
	{
		return $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'mail_template'), array('num_templates' => 'COUNT(*)'))
					->where('t.created_user_id = ?', $userId)
					->limit(1)
					->query()
					->fetch()
					->num_templates;
	}
	
	public function add($template)
	{
		$this->_conn->insert($this->_prefix . 'mail_template', 
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
							));
		return $this->_conn->lastInsertId($this->_prefix . 'mail_template');
	}
	
	public function delete($id)
	{
		return $this->_conn->delete($this->_prefix . 'mail_template', 
									array(
										'template_id = ?' => $id,
									));
	}
	
	public function update($template)
	{
		return $this->_conn->update($this->_prefix . 'mail_template', 
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
										'template_id = ?' => $template->template_id, 
									));
	}
}
