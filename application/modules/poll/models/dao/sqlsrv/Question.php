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
 * @version 	$Id: Question.php 5057 2010-08-28 18:39:33Z huuphuoc $
 * @since		2.0.5
 */

class Poll_Models_Dao_Sqlsrv_Question extends Tomato_Model_Dao
	implements Poll_Models_Interface_Question
{
	public function convert($entity) 
	{
		return new Poll_Models_Question($entity); 
	}
	
	public function getById($id) 
	{
		$sql  = 'SELECT TOP 1 * FROM ' . $this->_prefix . 'poll_question 
				WHERE question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$rs = $stmt->fetch();
		$return = (null == $rs) ? null : new Poll_Models_Question($rs);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($question) 
	{
		$this->_conn->insert($this->_prefix . 'poll_question', array(
			'title'			   => $question->title,
			'content'		   => $question->content,
			'created_date'	   => $question->created_date,
			'start_date'	   => $question->start_date,
			'end_date'		   => $question->end_date,
			'is_active'		   => $question->is_active,
			'multiple_options' => $question->multiple_options,
			'user_id'		   => $question->user_id,
			/**
			 * @since 2.0.8
			 */
			'language'     	   => $question->language,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'poll_question');
	}
	
	public function update($question) 
	{
		$sql = 'UPDATE ' . $this->_prefix . 'poll_question
				SET title = ?, content = ?, created_date = ?, start_date = ?, end_date = ?, 
					is_active = ?, multiple_options = ?, user_id = ?, language = ?
				WHERE question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$question->title,
			$question->content,
			$question->created_date,
			$question->start_date,
			$question->end_date,
			$question->is_active,
			$question->multiple_options,
			$question->user_id,
			/**
			 * @since 2.0.8
			 */
			$question->language,
			$question->question_id,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function delete($questionId) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'poll_question WHERE question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($questionId));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function find($offset = null, $count = null, $isActive = null) 
	{
		$sql    = "SELECT * FROM " . $this->_prefix . "poll_question WHERE language = ?";
		/**
		 * @since 2.0.8
		 */
		$params = array($this->_lang);
		if (is_bool($isActive)) {
			$sql 	 .= ' AND is_active = ?';
			$params[] = (int)$isActive;
		}
		$sql .= ' ORDER BY question_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count() 
	{
		$sql 	= 'SELECT COUNT(*) AS num_questions FROM ' . $this->_prefix . 'poll_question WHERE language = ?';
		/**
		 * @since 2.0.8
		 */
		$params = array($this->_lang);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_questions;
	}
	
	public function toggleActive($id) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'poll_question
				SET is_active = 1 - is_active 
				WHERE question_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql  = 'SELECT q.* 
				FROM ' . $this->_prefix . 'poll_question AS q
				INNER JOIN 
				(
					SELECT tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language 
					FROM ' . $this->_prefix . 'core_translation AS tr1
					INNER JOIN ' . $this->_prefix . 'core_translation AS tr2 
						ON (tr1.item_id = ? AND tr1.source_item_id = tr2.item_id) 
						OR (tr2.item_id = ? AND tr1.item_id = tr2.source_item_id)
						OR (tr1.source_item_id = ? AND tr1.source_item_id = tr2.source_item_id)
					WHERE tr1.item_class = ? AND tr2.item_class = ?
					GROUP by tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language
				) AS tr
					ON tr.item_id = q.question_id';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($item->question_id, $item->question_id, $item->question_id, 
						'Poll_Models_Question', 'Poll_Models_Question'
					   ));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return (null == $rows) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql  = "SELECT * FROM ".$this->_prefix . "poll_question AS q
				LEFT JOIN ".$this->_prefix . "core_translation AS tr
					ON tr.source_item_id = q.question_id 
					AND tr.item_class = ?
					AND tr.language = ?
				WHERE q.language = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array('Poll_Models_Question', $lang, $this->_lang));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($question)
	{
		$sql  = "SELECT TOP 1 * FROM ".$this->_prefix . "poll_question AS q 
				INNER JOIN ".$this->_prefix . "core_translation AS tr
					ON q.question_id = tr.source_item_id
				WHERE tr.item_id = ? AND tr.item_class = ?";
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($question->question_id, 'Poll_Models_Question'));
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return (null == $row) ? null : new Poll_Models_Question($row);
	}	
}
