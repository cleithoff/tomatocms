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
 * @version 	$Id: ConfigController.php 3972 2010-07-25 10:30:28Z huuphuoc $
 * @since		2.0.6
 */

class Mail_ConfigController extends Zend_Controller_Action
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Configure mail server
	 * 
	 * @return void
	 */
	public function serverAction()
	{
		$file   = TOMATO_APP_DIR . DS . 'modules' . DS . 'mail' . DS . 'config' . DS . 'config.ini';
		$config = new Zend_Config_Ini($file);
		$config = $config->toArray();

		$this->view->assign('protocol', (isset($config['protocol']['protocol'])) ? $config['protocol']['protocol'] : 'mail');
		$this->view->assign('host', (isset($config['smtp']['host'])) ? $config['smtp']['host'] : null);
		$this->view->assign('port', (isset($config['smtp']['port'])) ? $config['smtp']['port'] : null);
		$this->view->assign('username', (isset($config['smtp']['username'])) ? $config['smtp']['username'] : null);
		$this->view->assign('password', (isset($config['smtp']['password'])) ? $config['smtp']['password'] : null);
		$this->view->assign('security', (isset($config['smtp']['security'])) ? $config['smtp']['security'] : null);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$config   = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config   = $config->toArray();
			$protocol = $request->getPost('protocol');
			$config['protocol']['protocol'] = $protocol;
			switch ($protocol) {
				case 'mail':
					if (isset($config['smtp'])) {
						unset($config['smtp']);
					}
					break;
				case 'smtp':
					$config['smtp']['host'] = $request->getPost('host');
					
					$port = $request->getPost('port');
					if ($port == null || $port == '') {
						unset($config['smtp']['port']);
					} else {
						$config['smtp']['port'] = $port;
					}
					
					if ($request->getPost('authentication') == 'true') {
						$config['smtp']['username'] = $request->getPost('username');
						$config['smtp']['password'] = $request->getPost('password');
					} else {
						unset($config['smtp']['username']);
						unset($config['smtp']['password']);
					}
					
					$security = $request->getPost('security');
					if ($security != 'none') {
						$config['smtp']['security'] = $security;
					}
					break;
			}

			/**
			 * Write file
			 */
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('config_server_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'mail_config_server'));
		}
	}
}
