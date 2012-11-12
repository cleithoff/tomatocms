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
 * @version 	$Id: Revision.php 5485 2010-09-20 10:00:00Z huuphuoc $
 * @since		2.0.4
 */

/**
 * Represents a revision of article
 */
class News_Models_Revision extends Tomato_Model_Entity
{
	protected $_properties = array(
		'revision_id' 		=> null,	/** Id of revision */
		'article_id' 		=> null,	/** Id of article */
		'title' 			=> null,	/** Main title of article */
		'sub_title' 		=> null,	/** Sub-title of article */
		'slug' 				=> null,	/** Slug of article */
		'description' 		=> null,	/** Description of article */		
		'content' 			=> null,	/** Content of article */
		'icons' 			=> null,	/** Article icons */
		'created_date' 		=> null,	/** Article's creation date */
		'created_user_id' 	=> null,	/** Id of user who create article */
		'created_user_name' => null,	/** Username of user who create article */
		'author' 			=> null,	/** Author of article */
	);
}
