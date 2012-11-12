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
 * @version 	$Id: QuestionController.php 4930 2010-08-25 03:38:40Z huuphuoc $
 * @since		2.0.0
 */

class Poll_QuestionController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	/**
	 * Activate poll
	 * 
	 * @return void
	 */
	public function activateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		$body = '';
		if ($request->isPost()) {
			$id	= $request->getPost('id');
						
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
			$questionDao->setDbConnection($conn);
			$question = $questionDao->getById($id);
			if (null == $question) {
				$this->getResponse()->setBody('RESULT_NOT_FOUND');
				return;
			}
			$questionDao->toggleActive($id);
			$body = 1 - $question->is_active;
		}
		$this->getResponse()->setBody($body);
	}
	
	/**
	 * Add new poll
	 * 
	 * @return void
	 */	
	public function addAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$questionDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$sourceId       = $request->getParam('source_id');
		$sourceQuestion = (null == $sourceId) ? null : $questionDao->getById($sourceId);
		$this->view->assign('translatableData', (null == $sourceQuestion) ? array() : $sourceQuestion->getProperties());
		$this->view->assign('sourceQuestion', $sourceQuestion);
		$this->view->assign('lang', $request->getParam('lang'));
		
		if ($request->isPost()) {
			$user     = Zend_Auth::getInstance()->getIdentity();
			$answers  = $request->getPost('answers');
			$purifier = new HTMLPurifier();
			
			$question = new Poll_Models_Question(array(
				'title'			   => $purifier->purify($request->getPost('title')),
				'content'		   => $purifier->purify($request->getPost('content')),
				'created_date'	   => date('Y-m-d H:i:s'),
				'start_date'	   => $request->getPost('startDate'),
				'end_date'		   => $request->getPost('endDate'),
				'is_active'		   => 0,
				'multiple_options' => (int)$request->getPost('multipleOptions'),
				'user_id'		   => $user->user_id,
			
				/**
				 * @since 2.0.8
				 */
				'language'         => $request->getPost('languageSelector'),
			));
			$questionId = $questionDao->add($question);
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->add(new Core_Models_Translation(array(
				'item_id' 	      => $questionId,
				'item_class'      => get_class($question),
				'source_item_id'  => ('' == $source['id']) ? $questionId : $source['id'],
				'language'        => $question->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			if ($answers != null && $questionId != null) {
				$answerDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getAnswerDao();
				$answerDao->setDbConnection($conn);
				
				for ($i = 0; $i < count($answers); $i++) {
					$answerDao->add(new Poll_Models_Answer(array(
						'question_id' => $questionId,
						'title'		  => $purifier->purify($answers[$i]),
						'position'	  => $i + 1,
						'user_id'	  => $user->user_id,
						'num_views'	  => 0,
					)));
				}
			}
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('poll_add_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'poll_question_add'));
		}
	}
	
	/**
	 * Delete poll
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		$result = 'RESULT_ERROR';
		
		if ($request->isPost()) {
			$id = $request->getPost('id');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
			$answerDao 	 = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getAnswerDao();
			$questionDao->setDbConnection($conn);
			$answerDao->setDbConnection($conn);
			
			$question = $questionDao->getById($id);
			if (null == $question) {
				$this->getResponse()->setBody('RESULT_NOT_FOUND');
				return;
			} 
			
			$answerDao->deleteByQuestion($id);
			$questionDao->delete($id);
			$result = 'RESULT_OK';
		}
		$this->getResponse()->setBody($result);
	}	
	
	/**
	 * Edit poll
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request    = $this->getRequest();
		$questionId = $request->getParam('question_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$answerDao 	 = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getAnswerDao();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$questionDao->setDbConnection($conn);
		$answerDao->setDbConnection($conn);
		
		$question = $questionDao->getById($questionId);
		$answers  = $answerDao->getAnswers($questionId);
		
		if (null == $question) {
			throw new Exception('Not found question with Id of ' . $questionId);
		}
		
		/**
		 * @since 2.0.8
		 */
		$sourceQuestion = $questionDao->getSource($question);
		$this->view->assign('sourceQuestion', $sourceQuestion);
		
		$this->view->assign('question', $question);
		$this->view->assign('answers', $answers);
		
		if ($request->isPost()) {
			$user     = Zend_Auth::getInstance()->getIdentity();
			$answers  = $request->getPost('answers');
			$purifier = new HTMLPurifier();
			
			$question->title 			= $purifier->purify($request->getPost('title'));
			$question->content 			= $purifier->purify($request->getPost('content'));
			$question->multiple_options = (int)$request->getPost('multipleOptions');
			$question->start_date 		= $request->getPost('startDate');
			$question->end_date 		= $request->getPost('endDate');
			/**
			 * @since 2.0.8
			 */
			$question->language         = $request->getPost('languageSelector', $question->language);
			
			$questionDao->update($question);
			
			/**
			 * @since 2.0.8
			 */
			$source = Zend_Json::decode($request->getPost('sourceItem', '{"id": "", "language": ""}'));
			$translationDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTranslationDao();
			$translationDao->setDbConnection($conn);
			$translationDao->update(new Core_Models_Translation(array(
				'item_id' 	      => $questionId,
				'item_class'      => get_class($question),
				'source_item_id'  => ('' == $source['id']) ? $questionId : $source['id'],
				'language'        => $question->language,
				'source_language' => ('' == $source['language']) ? null : $source['language'],
			)));
			
			if ($answers != null && $questionId != null) {
				$answerDao->deleteByQuestion($questionId);
				
				for ($i = 0; $i < count($answers); $i++) {
					$answerDao->add(new Poll_Models_Answer(array(
						'question_id' => $questionId,
						'title'		  => $purifier->purify($answers[$i]),
						'position'	  => $i + 1,
						'user_id'	  => $user->user_id,
						'num_views'	  => 0,
					)));
				}
			}
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('poll_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('question_id' => $questionId), 'poll_question_edit'));
		}
	}
	
	/**
	 * List polls
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage = 20;
		$offset  = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$questionDao->setDbConnection($conn);
		
		/**
		 * @since 2.0.8
		 */
		$lang = $request->getParam('lang');
		$questionDao->setLang($lang);
		
		$questions 	  = $questionDao->find($offset, $perPage);
		$numQuestions = $questionDao->count();
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($questions, $numQuestions));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('questions', $questions);
		$this->view->assign('numQuestions', $numQuestions);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'poll_question_list'),
			'itemLink' => 'page-%d',
		));
	}	
}
