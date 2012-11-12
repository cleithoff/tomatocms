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
 * @version 	$Id: NoteController.php 3752 2010-07-17 12:01:06Z huuphuoc $
 * @since		2.0.4
 */

class Multimedia_NoteController extends Zend_Controller_Action
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Add new note
	 * 
	 * @return void
	 */
	public function addAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$noteDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getNoteDao();
			$noteDao->setDbConnection($conn);		

			$userId   = null;
			$userName = null;
			if (Zend_Auth::getInstance()->hasIdentity()) {			
				$user 	  = Zend_Auth::getInstance()->getIdentity();
				$userId   = $user->user_id;
				$userName = $user->user_name;
			}
			
			$note = new Multimedia_Models_Note(array(
				'file_id' 	=> $request->getPost('fileId'),
				'top' 		=> $request->getPost('top'),
				'left' 		=> $request->getPost('left'),
				'width' 	=> $request->getPost('width'),
				'height' 	=> $request->getPost('height'),
				'content' 	=> $request->getPost('content'),
				'user_id' 	=> $userId,
				'user_name' => $userName,
			));
			$noteId = $noteDao->add($note);
			$this->getResponse()->setBody($noteId);
		}
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Activate note
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id 	= $request->getPost('id');
			$status = $request->getPost('status');
			$status = ($status == 1) ? 0 : 1;
				
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$noteDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getNoteDao();
			$noteDao->setDbConnection($conn);
			$noteDao->updateStatus($id, $status);
			
			$this->getResponse()->setBody($status);
		}
	}
	
	/**
	 * Delete note
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$noteId = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$noteDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getNoteDao();
			$noteDao->setDbConnection($conn);
			$noteDao->delete($noteId);

			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Edit note
	 * 
	 * @return void
	 */
	public function editAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$noteDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getNoteDao();
			$noteDao->setDbConnection($conn);
			$user = Zend_Auth::getInstance()->getIdentity();
			
			$note = new Multimedia_Models_Note(array(
				'note_id' 	=> $request->getPost('id'),
				'top' 		=> $request->getPost('top'),
				'left' 		=> $request->getPost('left'),
				'width' 	=> $request->getPost('width'),
				'height' 	=> $request->getPost('height'),
				'content' 	=> $request->getPost('content'),
				'user_id' 	=> $user->user_id,
				'user_name' => $user->user_name,
			));
			$noteDao->update($note);
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * List notes
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$noteDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getNoteDao();
		$noteDao->setDbConnection($conn);
		
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('page_index', 1);
		$perPage   = 20;
		$offset	   = ($pageIndex - 1) * $perPage;
		
		$notes 	  = $noteDao->find($offset, $perPage);
		$numNotes = $noteDao->count();
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($notes, $numNotes));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('notes', $notes);
		$this->view->assign('numNotes', $numNotes);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'multimedia_note_list'),
			'itemLink' => 'page-%d',
		));
	}
}
