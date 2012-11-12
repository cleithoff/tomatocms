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
 * @version 	$Id: LogController.php 4109 2010-07-30 04:42:49Z huuphuoc $
 * @since		2.0.7
 */

class Core_LogController extends Zend_Controller_Action
{
	/* ========== Backend actions =========================================== */

	/**
	 * Delete log
	 * 
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$ids = array();
			$id  = $request->getPost('id');
			if (is_numeric($id)) {
				$ids[] = $id;
			} else {
				$ids = Zend_Json::decode($id);
			}

			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$logDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getLogDao();
			$logDao->setDbConnection($conn);
			foreach ($ids as $logId) {
				$logDao->delete($logId);
			}

			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * List logs
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$logDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getLogDao();
		$logDao->setDbConnection($conn);
		
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('page_index', 1);
		$perPage   = 20;
		$offset	   = ($pageIndex - 1) * $perPage;
		
		$logs 	 = $logDao->find($offset, $perPage);
		$numLogs = $logDao->count();
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($logs, $numLogs));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('logs', $logs);
		$this->view->assign('numLogs', $numLogs);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'core_log_list'),
			'itemLink' => 'page-%d',
		));
	}
}
