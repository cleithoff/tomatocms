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
 * @version 	$Id: RuleChecker.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_Services_RuleChecker 
{
	public static function isAllowed($action, $controller = null, $module = null, $callback = null, $params = null) 
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			return false;
		}
		$user = Zend_Auth::getInstance()->getIdentity();
		
		$action = strtolower($action);
		
		/**
		 * Get module and controller name
		 */
		if (null == $controller) {
			$controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		}
		if (null == $module) {
			$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		}
		
		$isAllowed = Core_Services_Acl::getInstance()
					->isUserOrRoleAllowed($user->role_id, $user->user_id, $module, $controller, $action);
		if (!$isAllowed) {
			return false;
		}
		if (null != $callback) {
			if (false !== ($pos = strpos($callback, '::'))) {
				$callback = array(substr($callback, 0, $pos), substr($callback, $pos + 2));
			}
			return call_user_func_array($callback, $params);
		}
		return true;
	}
}
