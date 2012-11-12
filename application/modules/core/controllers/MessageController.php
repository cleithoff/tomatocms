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
 * @version 	$Id: MessageController.php 4107 2010-07-29 11:11:47Z huuphuoc $
 * @since		2.0.3
 */

class Core_MessageController extends Zend_Controller_Action 
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
			->setLayout('message');
	}
	
	/* ========== Frontend actions ========================================== */

	/**
	 * Show error.
	 * If you want to throw data not found exception, add the following code to your controller action:
	 * <code>
	 * 	if (null == $data) {
	 * 		throw new Tomato_Exception_NotFound();
	 * 	}
	 * </code>
	 * 
	 * @return void
	 */
	public function errorAction()
	{
		$request    = $this->getRequest();
		$error 	    = $request->getParam('error_handler');
		
		/**
		 * Log the exception
		 * @since 2.0.7
		 */
        $class 	    = get_class($error->exception);
        $message    = $error->exception->getMessage();
        $config 	= Tomato_Config::getConfig();
        
        if (isset($config->install->version)) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$logDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getLogDao();
			$logDao->setDbConnection($conn);
			$logDao->add(new Core_Models_Log(array(
	    		'created_date' 	=> date('Y-m-d H:i:s'),
				'uri'			=> $request->getRequestUri(),
	    		'module'        => ($request->module == null) ? $request->getModuleName() : $request->module,
	    		'controller'    => ($request->controller == null) ? $request->getControllerName() : $request->controller,
	    		'action' 	    => ($request->action == null) ? $request->getActionName() : $request->action,
	    		'class' 	    => $class,
				'file'			=> $error->exception->getFile(),
				'line'			=> $error->exception->getLine(),
	    		'message' 	    => $message,
				'trace'			=> $error->exception->getTraceAsString(),
	        )));
        }
		
        /**
         * Show the message
         */
		$debug 	= (isset($config->web->debug) && 'true' == $config->web->debug) ? true : false;
		switch ($class) {
			case 'Tomato_Exception_NotFound':
				$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
				break;
			default:
				break;
		}
		
		$this->view->assign('error', $error);
		$this->view->assign('message', $message);
		$this->view->assign('debug', $debug);
	}
	
	/**
	 * Show offline message
	 * 
	 * @return void
	 */
	public function offlineAction() 
	{
		$config  = Tomato_Config::getConfig();
		$config  = $config->toArray();
		$message = isset($config['web']['offline_message']) 
						? $config['web']['offline_message'] 
						: null;
		$this->view->assign('message', $message);
	}
}
