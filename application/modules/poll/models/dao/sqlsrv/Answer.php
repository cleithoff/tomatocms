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
 * @version 	$Id: Answer.php 5057 2010-08-28 18:39:33Z huuphuoc $
 * @since		2.0.5
 */

class Poll_Models_Dao_Sqlsrv_Answer extends Tomato_Model_Dao
	implements Poll_Models_Interface_Answer
{
	public function convert($entity) 
	{
		return new Poll_Models_Answer($entity); 
	}
	
	public function add($answer) 
	{
		$this->_conn->insert($this->_prefix . 'poll_answer', array(
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
		$sql  = 'DELETE FROM ' . $this->_prefix . 'poll_answer WHERE question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($questionId));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getAnswers($questionId)
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'poll_answer AS a
				WHERE a.question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($questionId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countViews($questionId)
	{
		$sql  = 'SELECT TOP 1 SUM(num_views) AS num_views FROM ' . $this->_prefix . 'poll_answer AS a
				WHERE a.question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($questionId));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_views;
	}
	
	public function increaseViews($answerIds)
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'poll_answer
				SET num_views = num_views + 1
				WHERE answer_id IN(?)';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($answerIds));
		$stmt->closeCursor();
	}
}
