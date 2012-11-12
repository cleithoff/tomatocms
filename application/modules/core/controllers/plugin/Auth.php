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
 * @version 	$Id: Auth.php 4786 2010-08-23 12:04:41Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Base on the request URL and role/permisson of current user, forward the user
 * to the login page if the user have not logged in 
 */
class Core_Controllers_Plugin_Auth extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request) 
	{
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);

		$uri = rtrim($uri, '/') . '/';
		if (strpos($uri, '/admin/') === false) {
			return;
		}
		
		/**
		 * Switch to admin template
		 * @since 2.0.4
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		$view->assign('APP_TEMPLATE', 'admin');
		Zend_Layout::startMvc(array('layoutPath' => TOMATO_APP_DIR . DS . 'templates' . DS . 'admin' . DS . 'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('admin');
		
		$isAllowed = false;
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$user 		= Zend_Auth::getInstance()->getIdentity();
			$module 	= $request->getModuleName();
			$controller = $request->getControllerName();
			$action 	= $request->getActionName();
			
			/**
			 * Add 'core:message' resource that allows show the friendly error message
			 */
			$acl = Core_Services_Acl::getInstance();
			if (!$acl->has('core:message')) {
				$acl->addResource('core:message');
			}
			
			/**
			 * Alway allows logged in user to access the dashboard
			 * We should NOT use:
			 * <code>
			 * $currentRouteName = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
			 * if ('core_dashboard_index' == $currentRouteName) {
			 * 		$isAllowed = true;
			 * }
			 * </code>
			 * because this approach still throws exception when there are no routes matching with current URL
			 * @since 2.0.8
			 */
			if ('core_dashboard_index' == strtolower($module . '_' . $controller . '_' . $action)) {
				$isAllowed = true;
			} else {
				$isAllowed = $acl->isUserOrRoleAllowed($user->role_id, $user->user_id, $module, $controller, $action);
			}
		}
		if (!$isAllowed) {
			$forwardAction = Zend_Auth::getInstance()->hasIdentity() ? 'deny' : 'login';
			
			/**
			 * DON'T use redirect! as folow:
			 * $this->getResponse()->setRedirect('/Login/');
			 */
			$request->setModuleName('core')
					->setControllerName('Auth')
					->setActionName($forwardAction)
					->setDispatched(true);
		}
	}
}
