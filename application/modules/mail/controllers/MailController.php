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
 * @version 	$Id: MailController.php 4522 2010-08-12 09:38:16Z huuphuoc $
 * @since		2.0.6
 */

class Mail_MailController extends Zend_Controller_Action
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * List sent mails
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage   = 20;
		$offset    = ($pageIndex - 1) * $perPage;
		
		$user = Zend_Auth::getInstance()->getIdentity();	
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$mailDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getMailDao();
		$mailDao->setDbConnection($conn);
		
		$mails    = $mailDao->getMails($user->user_id, $offset, $perPage);
		$numMails = $mailDao->count($user->user_id);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($mails, $numMails));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('mails', $mails);
		$this->view->assign('numMails', $numMails);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path'	   => $this->view->url(array(), 'mail_mail_list'),
			'itemLink' => 'page-%d',
		));
	}

	/**
	 * Send an email
	 * 
	 * @return void
	 */
	public function sendAction()
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
		$roleDao     = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRoleDao();
		$templateDao->setDbConnection($conn);
		$roleDao->setDbConnection($conn);
				
		$templates = $templateDao->getTemplates($user->user_id);
		$roles 	   = $roleDao->getRoles();
		
		$request 	= $this->getRequest();
		$templateId = $request->getParam('template_id');
		$currentTemplate = ($templateId == null) ? null : $templateDao->getById($templateId);
		
		if ($request->isPost()) {
			/**
			 * Get the list of users
			 */
			$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
			$userDao->setDbConnection($conn);
			$roleId = $request->getPost('role');
			$exp    = ($roleId == '') ? array('status' => 1) : array('role' => $roleId, 'status' => 1);
			$users  = $userDao->find(null, null, $exp);
			
			$subject     = $request->getPost('subject');
			$content     = $request->getPost('content');
			$fromName    = $request->getPost('fromName');
			$fromMail    = $request->getPost('fromMail');
			$replyToName = $request->getPost('replyToName');
			$replyToMail = $request->getPost('replyToMail');
			
			/**
			 * Get mail transport instance
			 */
			$transport = Mail_Services_Mailer::getMailTransport();
			
			/**
			 * Send email to each user
			 * @see http://framework.zend.com/manual/en/zend.mail.multiple-emails.html
			 */
			$search = array(Mail_Models_Mail::MAIL_VARIABLE_EMAIL, Mail_Models_Mail::MAIL_VARIABLE_USERNAME);
			$mailDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getMailDao();
			$mailDao->setDbConnection($conn);
			
			foreach ($users as $u) {
				$replace = array($u->email, $u->user_name);
				$subject = str_replace($search, $replace, $subject);
				$content = str_replace($search, $replace, $content);
				
				$mailDao->add(new Mail_Models_Mail(array(
					'template_id' 	  => $templateId,
					'subject' 		  => $subject,
					'content' 		  => $content,
					'created_user_id' => $user->user_id,
					'from_mail' 	  => $fromMail,
					'from_name' 	  => $fromName,
					'reply_to_mail'   => $replyToMail,
					'reply_to_name'   => $replyToName,
					'to_mail' 		  => $u->email,
					'to_name'         => $u->user_name,
					'status' 		  => 'sent',
					'created_date'    => date('Y-m-d H:i:s'),
					'sent_date' 	  => date('Y-m-d H:i:s'),
				)));
				
				$mail = new Zend_Mail();
				$mail->clearFrom()->setFrom($fromMail, $fromName)
					->clearReplyTo()->setReplyTo($replyToMail, $replyToName)
					->addTo($u->email, $u->user_name)
					->setSubject($subject)
					->setBodyHtml($content)
					->send($transport);
			}
			
			/**
			 * Redirect
			 */
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('mail_send_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'mail_mail_list'));
		}
		
		$this->view->assign('templates', $templates);
		$this->view->assign('currentTemplate', $currentTemplate);
		$this->view->assign('roles', $roles);
		$this->view->assign('email', $user->email);
		$this->view->assign('name', $user->full_name);
	}
}
