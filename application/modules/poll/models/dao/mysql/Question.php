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
 * @version 	$Id: Question.php 4900 2010-08-24 20:35:44Z huuphuoc $
 * @since		2.0.5
 */

class Poll_Models_Dao_Mysql_Question extends Tomato_Model_Dao
	implements Poll_Models_Interface_Question
{
	public function convert($entity) 
	{
		return new Poll_Models_Question($entity); 
	}
	
	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "poll_question 
						WHERE question_id = '%s'
						LIMIT 1", 
						mysql_real_escape_string($id));
						
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Poll_Models_Question(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($question) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "poll_question (title, content, created_date, start_date, end_date, is_active, multiple_options, user_id, language)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($question->title),
						mysql_real_escape_string($question->content),
						mysql_real_escape_string($question->created_date),
						mysql_real_escape_string($question->start_date),
						mysql_real_escape_string($question->end_date),
						mysql_real_escape_string($question->is_active),
						mysql_real_escape_string($question->multiple_options),
						mysql_real_escape_string($question->user_id),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($question->language));
						
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($question) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "poll_question
						SET title = '%s', content = '%s', created_date = '%s', start_date = '%s', end_date = '%s', 
							is_active = '%s', multiple_options = '%s', user_id = '%s', language = '%s'
						WHERE question_id = '%s'",
						mysql_real_escape_string($question->title),
						mysql_real_escape_string($question->content),
						mysql_real_escape_string($question->created_date),
						mysql_real_escape_string($question->start_date),
						mysql_real_escape_string($question->end_date),
						mysql_real_escape_string($question->is_active),
						mysql_real_escape_string($question->multiple_options),
						mysql_real_escape_string($question->user_id),
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($question->language),
						mysql_real_escape_string($question->question_id));
						
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function delete($questionId) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "poll_question WHERE question_id = '%s'", 
						mysql_real_escape_string($questionId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function find($offset = null, $count = null, $isActive = null) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "poll_question
						WHERE language = '%s'",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
		if (is_bool($isActive)) {
			$sql .= sprintf(" AND is_active = '%s'", (int)$isActive);
		}
		$sql .= " ORDER BY question_id DESC";
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
	
	public function count() 
	{
		$sql = sprintf("SELECT COUNT(*) AS num_questions 
						FROM " . $this->_prefix . "poll_question
						WHERE language = '%s'",
						/**
						 * @since 2.0.8
						 */
						mysql_real_escape_string($this->_lang));
						
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_questions;
	}
	
	public function toggleActive($id) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "poll_question
						SET is_active = 1 - is_active 
						WHERE question_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$sql = sprintf("SELECT q.* FROM " . $this->_prefix . "poll_question AS q
						INNER JOIN 
						(
							SELECT tr1.* FROM " . $this->_prefix . "core_translation AS tr1
							INNER JOIN " . $this->_prefix . "core_translation AS tr2 
								ON (tr1.item_id = '%s' AND tr1.source_item_id = tr2.item_id) 
								OR (tr2.item_id = '%s' AND tr1.item_id = tr2.source_item_id)
								OR (tr1.source_item_id = '%s' AND tr1.source_item_id = tr2.source_item_id)
							WHERE tr1.item_class = '%s' AND tr2.item_class = '%s'
							GROUP by tr1.translation_id
						) AS tr
							ON tr.item_id = q.question_id",
						mysql_real_escape_string($item->question_id),
						mysql_real_escape_string($item->question_id),
						mysql_real_escape_string($item->question_id),
						'Poll_Models_Question',
						'Poll_Models_Question');
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getTranslatable($lang)
	{
		$sql = sprintf("SELECT q.*, (tr.item_id IS NULL) AS translatable 
						FROM " . $this->_prefix . "poll_question AS q
						LEFT JOIN " . $this->_prefix . "core_translation AS tr
							ON tr.source_item_id = q.question_id 
							AND tr.item_class = '%s' 
							AND tr.language = '%s'
						WHERE q.language = '%s'",
						'Poll_Models_Question',
						mysql_real_escape_string($lang),
						mysql_real_escape_string($this->_lang));
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function getSource($question)
	{
		$sql = sprintf("SELECT q.* 
						FROM " . $this->_prefix . "poll_question AS q
						INNER JOIN " . $this->_prefix . "core_translation AS tr
							ON q.question_id = tr.source_item_id
						WHERE tr.item_id = '%s' AND tr.item_class = '%s'
						LIMIT 1", 
						mysql_real_escape_string($question->question_id),
						'Poll_Models_Question');
		
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Poll_Models_Question(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
}
