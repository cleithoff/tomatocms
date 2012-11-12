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
 * @version 	$Id: Mail.php 5479 2010-09-20 09:59:10Z huuphuoc $
 * @since		2.0.6
 */

/**
 * Represents a mail
 */
class Mail_Models_Mail extends Tomato_Model_Entity 
{
	/**
	 * Mail variables that user can use in the mail template.
	 * Do NOT chance these constants.
	 */
	
	/**
	 * @const string
	 */
	const MAIL_VARIABLE_USERNAME = '%user_name%';
	
	/**
	 * @const string
	 */
	const MAIL_VARIABLE_EMAIL 	 = '%user_email%';
	
	protected $_properties = array(
		'mail_id' 	  	  => null,		/** Id of mail */
		'template_id' 	  => null,		/** Id of mail template */
		'subject' 		  => null,		/** Subject of mail */
		'content' 		  => null,		/** Content of mail */
		'created_user_id' => null,		/** Id of user who sent mail */
		'from_mail' 	  => null,		/** From email address */
		'from_name' 	  => null,		/** From name */
		'reply_to_mail'   => null,		/** Reply-to email address */
		'reply_to_name'   => null,		/** Reply-to name */
		'to_mail' 		  => null,		/** Send-to email adress */
		'to_name'         => null,		/** Send-to name */
		'status' 		  => null,		/** Status of mail */
		'created_date'    => null,		/** Mail creation date */
		'sent_date' 	  => null,		/** Mail sent date */
	);
}
