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
 * @version 	$Id: Question.php 5483 2010-09-20 09:59:26Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a question
 */
class Poll_Models_Question extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'question_id' 	   => null,		/** Id of question */
		'title' 		   => null,		/** Title of question */
		'content' 		   => null,		/** Content of question */
		'created_date' 	   => null,		/** Question's creation date */
		'start_date' 	   => null,		/** Starting date */
		'end_date' 		   => null,		/** Ending date */
		'is_active' 	   => 0,		/** Question's status. Can be 0 (not activated) or 1 (activated) */
		'multiple_options' => 0,		/** Defines this question accepts multiple answers or not */
		'user_id' 		   => null,		/** Id of user who create question */
		'num_views' 	   => 0,		/** Number of views */
		'language'		   => null,		/** Language of question (@since 2.0.8) */
	);
}
