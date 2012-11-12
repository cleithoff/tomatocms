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
 * @version 	$Id: Plugin.php 3985 2010-07-25 16:14:44Z huuphuoc $
 * @since		2.0.0
 */

class Plugins_AdminAccess_Plugin extends Tomato_Controller_Plugin 
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$currentUri = $request->getRequestUri();
		$currentUri = Tomato_Utility_String::normalizeUri($currentUri);
		$currentUri = '/' . $currentUri;
		$ips = $this->getParam('ips');
		
		/**
		 * 7 is length of /admin/
		 */
		if (substr($currentUri, 0, 7) != '/admin/' || null == $ips || '' == $ips) {
			return;
		}
		$ips = explode(',', $ips);
		$ip = $request->getClientIp();
		if (in_array($ip, $ips)) {
			$request->setModuleName('core')
					->setControllerName('Auth')
					->setActionName('deny')
					->setDispatched(true);
		}
	}
}
