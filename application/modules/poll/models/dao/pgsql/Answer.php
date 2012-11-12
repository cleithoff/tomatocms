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
 * @version 	$Id: Answer.php 5444 2010-09-15 08:32:28Z leha $
 * @since		2.0.5
 */

class Poll_Models_Dao_Pgsql_Answer extends Tomato_Model_Dao
	implements Poll_Models_Interface_Answer
{
	public function convert($entity) 
	{
		return new Poll_Models_Answer($entity); 
	}
	
	public function add($answer) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "poll_answer (question_id, title, content, position, user_id, num_views)
						VALUES (%s, '%s', '%s', '%s', %s, '%s')
						RETURNING answer_id",
						pg_escape_string($answer->question_id),
						pg_escape_string($answer->title),
						pg_escape_string($answer->content),
						pg_escape_string($answer->position),
						pg_escape_string($answer->user_id),
						pg_escape_string($answer->num_views));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->answer_id;
	}
	
	public function deleteByQuestion($questionId) 
	{
		pg_delete($this->_conn, $this->_prefix . 'poll_answer', 
					array(
						'question_id' => $questionId, 
					));
	}
	
	public function getAnswers($questionId)
	{
		$sql  = sprintf("SELECT * FROM " . $this->_prefix . "poll_answer AS a
						WHERE a.question_id = %s",
						($questionId) ? pg_escape_string($questionId) : 'null');
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countViews($questionId)
	{
		$sql = sprintf("SELECT SUM(num_views) AS num_views FROM " . $this->_prefix . "poll_answer AS a
						WHERE a.question_id = %s
						LIMIT 1",
						pg_escape_string($questionId));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_views;
	}
	
	public function increaseViews($answerIds)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "poll_answer
						SET num_views = num_views + 1
						WHERE answer_id IN(%s)",
						pg_escape_string($answerIds));
		pg_query($sql);
	}
}
