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
 * @version 	$Id: Comment.php 5473 2010-09-20 08:45:26Z huuphuoc $
 * @since		2.0.1
 */

/**
 * Represents a comment
 */
class Comment_Models_Comment extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'comment_id'   => null,		/** Id of comment */
		'title' 	   => null,		/** Title of comment */
		'content' 	   => null,		/** Content of comment */
	
		/**
		 * Information of user who submit comment
		 */
		'full_name'    => 0,		/** Full name of user */				
		'web_site' 	   => null,		/** Website of user */
		'email' 	   => null,		/** Email address of user. It will be used to get user's avatar, using Gavatar service */
		'user_id' 	   => null,		/** Id of user */
		'user_name'    => null,		/** Username of user */
	
		'ip' 		   => null,		/** IP address */
		'created_date' => null,		/** Comment's creation date */	
		'is_active'    => 0,		/** Comment's status. Can be 0 (not activated) or 1 (activated) */
		'reply_to' 	   => null,		/** Id of comment which user reply to */
		'depth'		   => 0,		/** Depth level of comment */
		'path' 		   => null,		/** The path from the root comment to current one */
		'ordering'	   => 0,		/** Ordering index of comment */
	
		/** 
		 * The URL of page that user submit comment
		 * TODO: Add the route of page. 
		 * Because, with this approach, we can not get the most commented objects 
		 * that have particular type, such as article, photo, etc. 
		 */
		'page_url' 	   => null,
	);
}
