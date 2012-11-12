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
 * @version 	$Id: Answer.php 5054 2010-08-28 18:31:12Z huuphuoc $
 * @since		2.0.5
 */

class Poll_Models_Dao_Mysql_Answer extends Tomato_Model_Dao
	implements Poll_Models_Interface_Answer
{
	public function convert($entity) 
	{
		return new Poll_Models_Answer($entity); 
	}
	
	public function add($answer) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "poll_answer (question_id, title, content, position, user_id, num_views)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($answer->question_id),
						mysql_real_escape_string($answer->title),
						mysql_real_escape_string($answer->content),
						mysql_real_escape_string($answer->position),
						mysql_real_escape_string($answer->user_id),
						mysql_real_escape_string($answer->num_views));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function deleteByQuestion($questionId) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "poll_answer WHERE question_id = '%s'", 
						mysql_real_escape_string($questionId));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getAnswers($questionId)
	{
		$sql  = sprintf("SELECT * FROM " . $this->_prefix . "poll_answer AS a
						WHERE a.question_id = '%s'",
						mysql_real_escape_string($questionId));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function countViews($questionId)
	{
		$sql = sprintf("SELECT SUM(num_views) AS num_views FROM " . $this->_prefix . "poll_answer AS a
						WHERE a.question_id = '%s'
						LIMIT 1",
						mysql_real_escape_string($questionId));
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_views;
	}
	
	public function increaseViews($answerIds)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "poll_answer
						SET num_views = num_views + 1
						WHERE answer_id IN(%s)",
						mysql_real_escape_string($answerIds));
		mysql_query($sql);
	}
}
