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
 * @version 	$Id: TemplateController.php 4539 2010-08-12 09:56:40Z huuphuoc $
 * @since		2.0.6
 */

class Mail_TemplateController extends Zend_Controller_Action
{
	/* ========== Backend actions =========================================== */

	/**
	 * Add new mail template
	 * 
	 * @return void
	 */
	public function addAction()
	{
		$user = Zend_Auth::getInstance()->getIdentity();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$title 		 = $request->getPost('title');
			$subject 	 = $request->getPost('subject');
			$content 	 = $request->getPost('content');
			$fromName 	 = $request->getPost('fromName');
			$fromMail 	 = $request->getPost('fromMail');
			$replyToName = $request->getPost('replyToName');
			$replyToMail = $request->getPost('replyToMail');
			
			$template = new Mail_Models_Template(array(
				'name' 			  => $name,
				'title' 		  => $title,
				'subject' 		  => $subject,
				'body' 			  => $content,
				'from_mail' 	  => $fromMail,
				'from_name' 	  => $fromName,
				'reply_to_mail'   => $replyToMail,
				'reply_to_name'   => $replyToName,
				'created_user_id' => $user->user_id,
				'locked'		  => 0,
			));
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
			$templateDao->setDbConnection($conn);
			
			$templateDao->add($template);
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('template_add_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'mail_template_add'));
		}
		
		$this->view->assign('email', $user->email);
		$this->view->assign('name', $user->full_name);
	}
	
	/**
	 * Check if the template name exist or not
	 * 
	 * @return void
	 */
	public function checkAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request  = $this->getRequest();
		$original = $request->getParam('original');
		$name 	  = $request->getParam('name');
		$template = null;
		if ($original == null || ($original != null && $name != $original)) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
			$templateDao->setDbConnection($conn);
			$template = $templateDao->getByName($name);
		}
		($template == null) ? $this->getResponse()->setBody('true') 
						    : $this->getResponse()->setBody('false');
	}
	
	/**
	 * Delete template
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
			$templateDao->setDbConnection($conn);
			
			$template = $templateDao->getById($id);
			$user     = Zend_Auth::getInstance()->getIdentity();
			
			if ($template == null || $template->locked == 1 
				|| $template->created_user_id != $user->user_id) 
			{
				$this->getResponse()->setBody('RESULT_ERROR');	
			} else {
				$templateDao->delete($id);
				$this->getResponse()->setBody('RESULT_OK');
			}
		}
	}
	
	/**
	 * Update mail template
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$request = $this->getRequest();
		$id      = $request->getParam('template_id');

		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
		$templateDao->setDbConnection($conn);
		
		$template = $templateDao->getById($id);
		if (null == $template) {
			throw new Exception('Not found mail template with id of ' . $id);
		}
		
		$user     = Zend_Auth::getInstance()->getIdentity();
		$editable = ($template != null && $template->created_user_id == $user->user_id);
		
		$this->view->assign('editable', $editable);
		$this->view->assign('template', $template);
		
		if ($editable) {					
			if ($request->isPost()) {
				$template->name 	     = $request->getPost('name');
				$template->title 	     = $request->getPost('title');
				$template->subject 	     = $request->getPost('subject');
				$template->body 	     = $request->getPost('content');
				$template->from_name     = $request->getPost('fromName');
				$template->from_mail     = $request->getPost('fromMail');
				$template->reply_to_name = $request->getPost('replyToName');
				$template->reply_to_mail = $request->getPost('replyToMail');
								
				$templateDao->update($template);
							
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('template_edit_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array('template_id' => $template->template_id), 'mail_template_edit'));
			}
		}
	}
	
	/**
	 * List mail templates
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
		$templateDao = Tomato_Model_Dao_Factory::getInstance()->setModule('mail')->getTemplateDao();
		$templateDao->setDbConnection($conn);
		
		$templates    = $templateDao->getTemplates($user->user_id, $offset, $perPage);
		$numTemplates = $templateDao->count($user->user_id);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($templates, $numTemplates));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('templates', $templates);
		$this->view->assign('numTemplates', $numTemplates);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path'	   => $this->view->url(array(), 'mail_template_list'),
			'itemLink' => 'page-%d',
		));
	}
}
