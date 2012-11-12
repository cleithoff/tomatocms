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
 * @version 	$Id: Note.php 5486 2010-09-20 13:47:08Z huuphuoc $
 * @since		2.0.4
 */

/**
 * Represents a photo note
 */
class Multimedia_Models_Note extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'note_id' 	   => null,		/** Id of note */
		'file_id' 	   => null,		/** Id of photo */
	
		/**
		 * Position of note
		 */
		'top' 		   => null,
		'left' 		   => null,
	
		'width' 	   => null,		/** Width of note */
		'height' 	   => null,		/** Height of note */
		'content' 	   => null,		/** Content of note */
		'is_active'    => 0,		/** Note's status. Can be 0 (not activated) or 1 (activated) */
		'user_id' 	   => null,		/** Id of user who create note */
		'user_name'    => null,		/** Username of user who create note */
		'created_date' => null,		/** Note's creation date */
	);
}
