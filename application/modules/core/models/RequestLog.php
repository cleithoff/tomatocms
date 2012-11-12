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
 * @version 	$Id: RequestLog.php 5476 2010-09-20 09:03:39Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a request information
 */
class Core_Models_RequestLog extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'log_id' 	  => null,		/** Id of log */
		'ip' 		  => null,		/** IP address that user make request */
		'agent' 	  => null,		/** Agent of browser that user use to make request */
		'browser' 	  => null,		/** Name of browser */
		'version' 	  => null,		/** Version of browser */
		'platform' 	  => null,		/** Platform of browser. It is opertaing system name */
		'bot' 		  => null,		/** Defines the request that is sent by bot or not */
		'uri' 		  => null,		/** Request URI */
		'refer_url'   => null,		/** Refer URL where the request comes from */
		'full_url' 	  => null,		/** Full URL of request */
		'access_time' => null,		/** Access time of request */
	);
}
