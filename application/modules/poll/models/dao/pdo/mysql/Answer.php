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
 * @version 	$Id: Answer.php 5343 2010-09-07 09:17:16Z huuphuoc $
 * @since		2.0.5
 */

class Poll_Models_Dao_Pdo_Mysql_Answer extends Tomato_Model_Dao
	implements Poll_Models_Interface_Answer
{
	public function convert($entity) 
	{
		return new Poll_Models_Answer($entity); 
	}
	
	public function add($answer) 
	{
		$this->_conn->insert($this->_prefix . 'poll_answer', 
							array(
								'question_id' => $answer->question_id,
								'title'		  => $answer->title,
								'content'	  => $answer->content,
								'position'	  => $answer->position,
								'user_id'	  => $answer->user_id,
								'num_views'	  => $answer->num_views,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'poll_answer');
	}
	
	public function deleteByQuestion($questionId) 
	{
		$this->_conn->delete($this->_prefix . 'poll_answer', 
							array(
								'question_id = ?' => $questionId, 
							));
	}
	
	public function getAnswers($questionId)
	{
		$rs = $this->_conn
					->select()
					->from(array('a' => $this->_prefix . 'poll_answer'))
					->where('a.question_id = ?', $questionId)
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);	
	}
	
	public function countViews($questionId)
	{
		return $this->_conn
					->select()
					->from(array('p' => $this->_prefix . 'poll_answer'), array('num_views' => 'SUM(num_views)'))
					->where('question_id = ?', $questionId)
					->limit(1)
					->query()
					->fetch()
					->num_views;
	}
	
	public function increaseViews($answerIds)
	{
		$this->_conn->update($this->_prefix . 'poll_answer', 
							array(
								'num_views' => new Zend_Db_Expr('num_views + 1'),
							),
							array(
								'answer_id IN(?)' => $answerIds,
							));
	}
}
