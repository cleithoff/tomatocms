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
 * @version 	$Id: Question.php 5408 2010-09-13 07:32:40Z leha $
 * @since		2.0.5
 */

class Poll_Models_Dao_Pdo_Mysql_Question extends Tomato_Model_Dao 
	implements Poll_Models_Interface_Question
{
	public function convert($entity) 
	{
		return new Poll_Models_Question($entity); 
	}
	
	public function getById($id) 
	{
		$row = $this->_conn
					->select()
					->from(array('q' => $this->_prefix . 'poll_question'))
					->where('q.question_id = ?', $id)
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Poll_Models_Question($row);	
	}
	
	public function add($question) 
	{
		$this->_conn->insert($this->_prefix . 'poll_question', 
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
							));
		return $this->_conn->lastInsertId($this->_prefix . 'poll_question');
	}
	
	public function update($question) 
	{
		return $this->_conn->update($this->_prefix . 'poll_question', 
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
										'question_id = ?' => $question->question_id,
									));
	}
	
	public function delete($questionId) 
	{
		return $this->_conn->delete($this->_prefix . 'poll_question',
									array(
										'question_id = ?' => $questionId,
									));
	}
	
	public function find($offset = null, $count = null, $isActive = null) 
	{
		$select = $this->_conn
						->select()
						->from(array('q' => $this->_prefix . 'poll_question'))
						/**
						 * @since 2.0.8
						 */
						->where('q.language = ?', $this->_lang);
		if (is_bool($isActive)) {
			$select->where('q.is_active = ?', (int)$isActive);
		}
		$select->order('q.question_id DESC');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function count() 
	{
		return $this->_conn
					->select()
					->from(array('q' => $this->_prefix . 'poll_question'), array('num_questions' => 'COUNT(*)'))
					/**
					 * @since 2.0.8
					 */
					->where('q.language = ?', $this->_lang)
					->query()
					->fetch()
					->num_questions;
	}
	
	public function toggleActive($id) 
	{
		return $this->_conn->update($this->_prefix . 'poll_question', 
									array(
										'is_active' => new Zend_Db_Expr('1 - is_active'),
									),
									array(
										'question_id = ?' => $id,
									));
	}

	/* ========== For translation =========================================== */
	
	public function getTranslations($item)
	{
		$rs = $this->_conn
					->select()
					->from(array('q' => $this->_prefix . 'poll_question'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'),
						'tr.item_class = ?
						AND (tr.item_id = ? OR tr.source_item_id = ?)
						AND (tr.item_id = q.question_id OR tr.source_item_id = q.question_id)',
						array('tr.source_item_id'))
					->group('q.question_id')
					->bind(array(
						'Poll_Models_Question',
						$item->question_id,
						$item->question_id,
					))
					->query()
					->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getTranslatable($lang)
	{
		$rs = $this->_conn
					->select()
					->from(array('q' => $this->_prefix . 'poll_question'))
					->joinLeft(array('tr' => $this->_prefix . 'core_translation'), 
							'tr.source_item_id = q.question_id 
							AND tr.item_class = ? 
							AND tr.language = ?',
							array('translatable' => '(tr.item_id IS NULL)'))
					->where('q.language = ?', $this->_lang)
					->order('q.question_id DESC')
					->bind(array(
						'Poll_Models_Question', 
						$lang,
					))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
	
	public function getSource($question)
	{
		$row = $this->_conn
					->select()
					->from(array('q' => $this->_prefix . 'poll_question'))
					->joinInner(array('tr' => $this->_prefix . 'core_translation'), 'q.question_id = tr.source_item_id', array())
					->where('tr.item_id = ?', $question->question_id)
					->where('tr.item_class = ?', 'Poll_Models_Question')
					->limit(1)
					->query()
					->fetch();
		return (null == $row) ? null : new Poll_Models_Question($row);
	}	
}
