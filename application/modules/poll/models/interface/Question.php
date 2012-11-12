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
 * @version 	$Id: Question.php 4669 2010-08-16 07:33:32Z huuphuoc $
 * @since		2.0.5
 */

interface Poll_Models_Interface_Question
{
	/**
	 * Get question by given Id
	 * 
	 * @param int $id Id of question
	 * @return Poll_Models_Question
	 */
	public function getById($id);
	
	/**
	 * Add new question
	 * 
	 * @param Poll_Models_Question $question
	 * @return int
	 */
	public function add($question);
	
	/**
	 * Update question
	 * 
	 * @param Poll_Models_Question $question
	 * @return void
	 */
	public function update($question);
	
	/**
	 * Delete question by Id
	 * 
	 * @param int $questionId Id of question
	 * @return int
	 */
	public function delete($questionId);
	
	/**
	 * List questions
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param bool $isActive
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset = null, $count = null, $isActive = null);
	
	/**
	 * Count number of questions
	 * 
	 * @return int
	 */
	public function count();
	
	/**
	 * Toggle question status
	 * 
	 * @param int $id
	 * @return int
	 */
	public function toggleActive($id);
	
	/**
	 * Get translable items which haven't been translated of the default language
	 * 
	 * @since 2.0.8
	 * @param string $lang
	 * @return Tomato_Model_RecordSet
	 */
	public function getTranslatable($lang);
	
	/**
	 * Get translation item which was translated to given category
	 * 
	 * @since 2.0.8
	 * @param Poll_Models_Question $question
	 * @return Poll_Models_Question
	 */
	public function getSource($question);
}
