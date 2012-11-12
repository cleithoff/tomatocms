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
 * @version 	$Id: Answer.php 3351 2010-06-28 06:15:32Z huuphuoc $
 * @since		2.0.5
 */

interface Poll_Models_Interface_Answer
{
	/**
	 * Add new answer
	 * 
	 * @param Poll_Models_Answer $answer
	 * @return int
	 */
	public function add($answer);
	
	/**
	 * Delete all answers by question Id
	 * 
	 * @param int $questionId Id of question
	 * @return int
	 */
	public function deleteByQuestion($questionId);
	
	/**
	 * Get all answers by question
	 * 
	 * @param int $questionId Id of question
	 * @return Tomato_Model_RecordSet
	 */
	public function getAnswers($questionId);
	
	/**
	 * Count number of answer views
	 * 
	 * @param int $questionId Id of question
	 * @return int
	 */
	public function countViews($questionId);
	
	/**
	 * Increase number of answer views
	 * 
	 * @param string $answerIds Array of answer Ids
	 */
	public function increaseViews($answerIds);
}
