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
 * @version 	$Id: AuthController.php 4539 2010-08-12 09:56:40Z huuphuoc $
 * @since		2.0.0
 */

class Core_AuthController extends Zend_Controller_Action 
{
	/**
	 * Init controller
	 * 
	 * @return void
	 */
	public function init() 
	{
		Zend_Layout::getMvcInstance()
				->setLayoutPath(TOMATO_APP_DIR . DS . 'templates' . DS . 'admin' . DS . 'layouts')
				->setLayout('auth');
	}
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Deny access
	 * 
	 * @return void
	 */
	public function denyAction() 
	{
		Zend_Layout::getMvcInstance()->setLayout('message');
		if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->getHelper('viewRenderer')->setNoRender(); 
            $this->_helper->getHelper('layout')->disableLayout();
             
            $return = array('message' => $this->view->translator('auth_deny_guide')); 
            $this->getResponse()->setBody(Zend_Json::encode($return));
        }
	}
	
	/**
	 * Forgot password
	 * 
	 * @since 2.0.6
	 * @return void
	 */
	public function forgotAction()
	{
		$request = $this->getRequest();
		$auth 	 = Zend_Auth::getInstance();
		
		/**
		 * Redirect to dashboard if user has logged in already
		 */
		if ($auth->hasIdentity()) {
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index'));
		}
		if ($request->isPost()) {
			$username = $request->getPost('username');
			$email 	  = $request->getPost('email');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
			$userDao->setDbConnection($conn);
			
			$users = $userDao->find(null, null, array('username' => $username, 'email' => $email));
			if (count($users) == 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('auth_forgot_user_not_found'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_auth_forgot'));
			} else {
				/**
				 * Send the confirmation link to reset password via email 
				 */
				$user = $users[0];
				
				$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
				$templateDao->setDbConnection($conn);
				$template = $templateDao->getByName(Mail_Models_Template::TEMPLATE_FORGOT_PASSWORD);
				
				if ($template == null) {
					$message = sprintf($this->view->translator('auth_mail_template_not_found'), Mail_Models_Template::TEMPLATE_FORGOT_PASSWORD);
					throw new Exception($message);
				}
				
				$search  = array(Mail_Models_Mail::MAIL_VARIABLE_EMAIL, Mail_Models_Mail::MAIL_VARIABLE_USERNAME);
				$replace = array($user->email, $user->user_name);
				$subject = str_replace($search, $replace, $template->subject);
				$content = str_replace($search, $replace, $template->body);
				
				/**
				 * Replace the reset password link
				 * @TODO: Add security key?
				 */
				$encodedLink = array(
					'user_name' => $username,
					'email'     => $email,
				);
				$encodedLink = base64_encode(urlencode(Zend_Json::encode($encodedLink)));
				$link    	 = $this->view->serverUrl() . $this->view->url(array('encoded_link' => $encodedLink), 'core_auth_reset');
				$content 	 = str_replace('%reset_link%', $link, $content);
				
				/**
			 	 * Get mail transport instance
			 	 */
				$transport = Mail_Services_Mailer::getMailTransport();
				
				$mail = new Zend_Mail();
				$mail->setFrom($template->from_mail, $template->from_name)					
					->addTo($user->email, $user->user_name)
					->setSubject($subject)
					->setBodyHtml($content)
					->send($transport);

				/**
				 * Redirect
				 */
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('auth_forgot_sent_mail'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_auth_forgot'));
			}
		}
	}
	
	/**
	 * Reset password
	 * 
	 * @since 2.0.6
	 * @return void
	 */
	public function resetAction()
	{
		$request 	 = $this->getRequest();
		$encodedLink = $request->getParam('encoded_link');
		$encodedLink = Zend_Json::decode(urldecode(base64_decode($encodedLink)));
		
		$invalidLink = true;
		if (is_array($encodedLink) && isset($encodedLink['user_name']) && isset($encodedLink['email'])) {
			$username = $encodedLink['user_name'];
			$email 	  = $encodedLink['email'];
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
			$userDao->setDbConnection($conn);
			
			$users = $userDao->find(null, null, array('username' => $username, 'email' => $email));
			if (count($users) > 0) {
				$invalidLink = false;
				$user = $users[0];
				
				/**
				 * Reset the password
				 */
				$password = substr(md5(rand(100000, 999999)), 0, 8);
				$userDao->updatePasswordFor($username, $password);
				
				/**
				 * Send new password via email
				 */
				$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
				$templateDao->setDbConnection($conn);
				$template = $templateDao->getByName(Mail_Models_Template::TEMPLATE_NEW_PASSWORD);
				
				if ($template == null) {
					$message = sprintf($this->view->translator('auth_mail_template_not_found'), Mail_Models_Template::TEMPLATE_NEW_PASSWORD);
					throw new Exception($message);
				}
				
				$search  = array(Mail_Models_Mail::MAIL_VARIABLE_EMAIL, Mail_Models_Mail::MAIL_VARIABLE_USERNAME);
				$replace = array($user->email, $user->user_name);
				$subject = str_replace($search, $replace, $template->subject);
				$content = str_replace($search, $replace, $template->body);
				$content = str_replace('%new_password%', $password, $content);
				$content = str_replace('%link%', $this->view->serverUrl() . $this->view->url(array(), 'core_auth_login'), $content);
				
				/**
			 	 * Get mail transport instance
			 	 */
				$transport = Mail_Services_Mailer::getMailTransport();
				
				$mail = new Zend_Mail();
				$mail->setFrom($template->from_mail, $template->from_name)					
					->addTo($user->email, $user->user_name)
					->setSubject($subject)
					->setBodyHtml($content)
					->send($transport);
			}
		}
		
		$this->view->assign('invalidLink', $invalidLink);
	}
	
	/**
	 * Login
	 * 
	 * @return void
	 */
	public function loginAction() 
	{
		$request = $this->getRequest();
		$auth 	 = Zend_Auth::getInstance();
		
		/**
		 * Redirect to dashboard if user has logged in already
		 */ 
		if ($auth->hasIdentity()) {
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_dashboard_index'));
		}
		if ($request->isPost()) {
			$username = $request->getPost('username');
			$password = $request->getPost('password');
			$adapter  = new Core_Services_Auth($username, $password);
			$result   = $auth->authenticate($adapter);
			switch ($result->getCode()) {
				/**
				 * Found user, but the account has not been activated
				 */
				case Core_Services_Auth::NOT_ACTIVE:
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('auth_login_user_not_activated'));
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_auth_login'));
					break;
					
				/**
				 * Logged in successfully
				 */
				case Core_Services_Auth::SUCCESS:
					$user = $auth->getIdentity();
					
					Tomato_Hook_Registry::getInstance()->executeAction('Core_Auth_Login_LoginSuccess', $user);
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_dashboard_index'));
					break;
					
				/** 
				 * Not found
				 */
				case Core_Services_Auth::FAILURE:
				default:
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('auth_login_failure'));
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_auth_login'));
					break;
			}
		}
	}
	
	/**
	 * Logout
	 * 
	 * @return void
	 */
	public function logoutAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$user = $auth->getIdentity();
			
			Tomato_Hook_Registry::getInstance()->executeAction('Core_Auth_Login_LogoutSuccess', $user);
			
			/**
			 * Clear session
			 */
			Zend_Session::destroy(false, false);
			
			$auth->clearIdentity();
		}
		$this->_redirect($this->view->baseUrl());
	}
}
