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
 * @version 	$Id: DashboardController.php 4561 2010-08-12 11:49:30Z huuphuoc $
 * @since		2.0.0
 */

class Core_DashboardController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Show dashboard of administration
	 * 
	 * @return void
	 */
	public function indexAction() 
	{
		$request = $this->getRequest();
		$act  = $request->getParam('act', '');
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$user = Zend_Auth::getInstance()->getIdentity();
		
		/**
		 * Allow user to personalize the dashboard
		 * @since 2.0.7
		 */
		switch ($act) {
			/**
			 * Load the layout editor
			 */
			case 'edit':
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				
				$moduleDirs = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		
				/**
				 * Get modules
				 */
				$moduleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getModuleDao();
				$moduleDao->setDbConnection($conn);
				$modules = $moduleDao->getModules();
				$this->view->assign('widgetModules', $modules);
				
				$response = $this->view->render('dashboard/_editor.phtml');
				$this->getResponse()->setBody($response);
				break;
				
			/**
			 * Save the dashboard layout
			 */
			case 'save':
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();

				$dashboradDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getDashboardDao();
				$dashboradDao->setDbConnection($conn);
				$dashboard = $dashboradDao->getByUser($user->user_id);
				if (null == $dashboard) {
					$dashboradDao->create(new Core_Models_Dashboard(array(
						'user_id' 	 => $user->user_id,
						'user_name'  => $user->user_name,
						'layout' 	 => $request->getParam('layout'),
						'is_default' => 0,
					)));
				} else {
					$dashboard->layout = $request->getParam('layout');
					$dashboradDao->update($dashboard);
				}
				
				$this->getResponse()->setBody('RESULT_OK');
				break;
				
			/**
			 * Load the dashboard layout
			 */
			default:
				$dashboradDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getDashboardDao();
				$dashboradDao->setDbConnection($conn);
				$dashboard = $dashboradDao->getByUser($user->user_id);
				if (null == $dashboard) {
					/**
					 * Try to load the default dashboard
					 */
					$dashboard = $dashboradDao->getDefault();
				}
				if ($dashboard != null) {
					$this->view->assign('dashboardLayout', $dashboard->layout);
				}
				break;
		}
	}
}
