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
 * @version 	$Id: Template.php 5479 2010-09-20 09:59:10Z huuphuoc $
 * @since		2.0.6
 */

/**
 * Represents a mail template
 */
class Mail_Models_Template extends Tomato_Model_Entity 
{
	/**
	 * Available templates that are not editable
	 * Do NOT change these constants
	 */
	
	/**
	 * The mail template that send link to reset password
	 * 
	 * @const string
	 */
	const TEMPLATE_FORGOT_PASSWORD = 'forgot_password';

	/**
	 * The mail template that send new password
	 * 
	 * @const string
	 */
	const TEMPLATE_NEW_PASSWORD    = 'new_password';
	
	protected $_properties = array(
		'template_id' 	  => null,		/** Id of template */
		'name' 			  => null,		/** Name of template */
		'title' 		  => null,		/** Title of template */
		'subject' 		  => null,		/** Subject of template */
		'body' 			  => null,		/** Body of template */
		'from_mail' 	  => null,		/** From mail address */
		'from_name' 	  => null,		/** From name */
		'reply_to_mail'   => null,		/** Reply-to mail address */
		'reply_to_name'   => null,		/** Reply-to name */
		'created_user_id' => null,		/** Id of user who create the template */
		'locked'		  => 0,			/** Lock status. Can be 0 or 1 */
	);
}
