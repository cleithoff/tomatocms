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
 * @version 	$Id: Question.php 5445 2010-09-15 08:32:47Z leha $
 * @since		2.0.5
 */

class Poll_Models_Dao_Pgsql_Question extends Tomato_Model_Dao
	implements Poll_Models_Interface_Question
{
	public function convert($entity) 
	{
		return new Poll_Models_Question($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "poll_question 
						WHERE question_id = %s
						LIMIT 1", 
						($id) ? pg_escape_string($id) : 'null');
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Poll_Models_Question(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($question) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "poll_question (title, content, created_date, start_date, end_date, is_active, multiple_options, user_id, language)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s')
						RETURNING question_id",
						pg_escape_string($question->title),
						pg_escape_string($question->content),
						pg_escape_string($question->created_date),
						pg_escape_string(date('Y-m-d h:s:i', strtotime($question->start_date))),
						pg_escape_string(date('Y-m-d h:s:i', strtotime($question->end_date))),
						pg_escape_string($question->is_active),
						pg_escape_string($question->multiple_options),
						pg_escape_string($question->user_id),
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($question->language));
						
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->question_id;
	}
	
	public function update($question) 
	{
		return pg_update($this->_conn, $this->_prefix . 'poll_question', 
						array(
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
						),
						array(
							'question_id' 	   => $question->question_id,
						));
	}
	
	public function delete($questionId) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'poll_question',
						array(
							'question_id' => $questionId,
						));
	}
	
	public function find($offset = null, $count = null, $isActive = null) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "poll_question 
						WHERE language = '%s'",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
		if (is_bool($isActive)) {
			$sql .= sprintf(" AND is_active = %s", (int)$isActive);
		}
		$sql .= " ORDER BY question_id DESC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s OFFSET %s", $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count() 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_questions FROM " . $this->_prefix . "poll_question 
						WHERE language = '%s'",
						/**
						 * @since 2.0.8
						 */
						pg_escape_string($this->_lang));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_questions;
	}
	
	public function toggleActive($id) 
	{
		return pg_update($this->_conn, $this->_prefix . 'poll_question', 
						array(
							'is_active' => new Zend_Db_Expr('1 - is_active'),
						),
						array(
							'question_id' => $id,
						));
	}
	
		/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = sprintf("SELECT q.* FROM " . $this->_prefix . "poll_question AS q
						INNER JOIN 
						(
							SELECT tr1.translation_id, MAX(tr1.item_id) AS item_id, MAX(tr1.item_class) AS item_class, MAX(tr1.source_item_id) AS source_item_id, MAX(tr1.language) AS language, MAX(tr1.source_language) AS source_language 
							FROM " . $this->_prefix . "core_translation AS tr1
							INNER JOIN " . $this->_prefix . "core_translation AS tr2 
								ON (tr1.item_id = %s AND tr1.source_item_id = tr2.item_id) 
								OR (tr2.item_id = %s AND tr1.item_id = tr2.source_item_id)
								OR (tr1.source_item_id = %s AND tr1.source_item_id = tr2.source_item_id)
							WHERE tr1.item_class = '%s' AND tr2.item_class = '%s'
							GROUP by tr1.translation_id
						) AS tr
							ON tr.item_id = q.question_id",
						pg_escape_string($item->question_id),
						pg_escape_string($item->question_id),
						pg_escape_string($item->question_id),
						'Poll_Models_Question', 
						'Poll_Models_Question');
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "poll_question AS q
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON q.question_id = tr.source_item_id
							AND tr.item_class = '%s'
							AND tr.language = '%s'
						WHERE q.language = '%s'",
						'Poll_Models_Question',
						pg_escape_string($lang),
						pg_escape_string($this->_lang));
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($question)
	{
		$sql = sprintf("SELECT *
						FROM " . $this->_prefix . "poll_question AS q
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON q.question_id = tr.source_item_id
						WHERE tr.item_id = %s 
							AND tr.item_class = '%s'",
						pg_escape_string($question->question_id),
						'Poll_Models_Question');
						
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Category_Models_Category(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}	
}
