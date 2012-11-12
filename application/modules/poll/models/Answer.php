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
 * @version 	$Id: Answer.php 5483 2010-09-20 09:59:26Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represent an answer
 */
class Poll_Models_Answer extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'answer_id'   => null,		/** Id of answer */
		'question_id' => null,		/** Id of question */
		'position' 	  => null,		/** Ordering index of answer in the list of questions's answers */
		'title' 	  => null,		/** Title of answer */
		'content' 	  => null,		/** Content of answer */
		'is_correct'  => 0,			/** Defines the answer is correct or not */
		'user_id'	  => null,		/** Id of user who create the answer */
		'num_views'   => 0,			/** Number of views */
	);
}
