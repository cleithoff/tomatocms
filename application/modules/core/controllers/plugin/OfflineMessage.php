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
 * @version 	$Id: OfflineMessage.php 4818 2010-08-24 06:45:44Z huuphuoc $
 * @since		2.0.3
 */

class Core_Controllers_Plugin_OfflineMessage extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @var array
	 */
	private static $_EXCEPT_ACTIONS = array(
		'core_auth_login',
		'core_auth_logout',
		'core_auth_deny',
	);
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$config = Tomato_Config::getConfig();
		$config = $config->toArray();
		
		/**
		 * Check whether the site is currently in offline mode or not
		 */
		if (!isset($config['web']['offline']['enable']) || 'false' == $config['web']['offline']['enable']) {
			return;
		}
		
		$act = implode('_', array(
			$request->getModuleName(),
			$request->getControllerName(),
			$request->getActionName()
		));
		$act = strtolower($act);
		if (in_array($act, self::$_EXCEPT_ACTIONS)) {
			return;
		}
		

		$uri = $request->getRequestUri();
		$uri = strtolower($uri);
		$uri = rtrim($uri, '/') . '/';
		if (strpos($uri, '/admin/') === false) {
			/**
			 * Forward user to the action that show offline message
			 */
			$request->setModuleName('core')
					->setControllerName('Message')
					->setActionName('offline')
					->setDispatched(true);	
		}
	}
}
