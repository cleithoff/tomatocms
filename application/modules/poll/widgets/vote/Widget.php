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
 * @version 	$Id: Widget.php 5002 2010-08-26 07:01:48Z hoangninh $
 * @since		2.0.0
 */

class Poll_Widgets_Vote_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$questionId	= $this->_request->getParam('poll_id');
		$container 	= $this->_request->getParam('container');
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$answerDao 	 = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getAnswerDao();
		$questionDao->setDbConnection($conn);
		$answerDao->setDbConnection($conn);
		
		$question = $questionDao->getById($questionId);
		$question = (null == $question || $question->is_active != 1) ? null : $question;
		$answers  = $answerDao->getAnswers($questionId);
		
		$data = Zend_Json::encode(array('poll_id' => $questionId, 'container' => $container));
		
		$this->_view->assign('question', $question);
		$this->_view->assign('answers', $answers);
		$this->_view->assign('container', $container);
		$this->_view->assign('data', $data);
	}
	
	protected function _prepareResult() 
	{
		$questionId = $this->_request->getParam('poll_id');
		$answerIds 	= $this->_request->getParam('answers');
		$container 	= $this->_request->getParam('container');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$answerDao 	 = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getAnswerDao();
		$questionDao->setDbConnection($conn);
		$answerDao->setDbConnection($conn);
		
		$question = $questionDao->getById($questionId);
		if ($answerIds) {
			$answerIds = explode(',', $answerIds);
			foreach ($answerIds as $answerId) {
				$answerDao->increaseViews($answerId);
			}
		}
		
		/**
		 * Get result
		 */
		$answers = $answerDao->getAnswers($questionId);
		
		/**
		 * Count the number of answers
		 */
		$count = $answerDao->countViews($questionId);
		
		$data = Zend_Json::encode(array('poll_id' => $questionId, 'container' => $container));
		
		$this->_view->assign('question', $question);
		$this->_view->assign('answers', $answers);
		$this->_view->assign('count', $count);
		$this->_view->assign('container', $container);
		$this->_view->assign('data', $data);
	}
	
	protected function _prepareConfig() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$questionDao->setDbConnection($conn);
		
		$questions = $questionDao->find(null, null, true);
		$this->_view->assign('questions', $questions);
	}
}
