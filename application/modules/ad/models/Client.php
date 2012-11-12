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
 * @version 	$Id: Client.php 5470 2010-09-20 08:30:02Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a client
 */
class Ad_Models_Client extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'client_id'    => null,		/** Id of client */
		'name' 		   => null,		/** Name of client */
		'email' 	   => null,		/** Email address of client */
		'telephone'    => null,		/** Telephone number of client */
		'address' 	   => null,		/** Address of client */
		'created_date' => null,		/** The date we add client */
	);
}
