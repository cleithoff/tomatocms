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
 * @version 	$Id: ConfigController.php 4216 2010-08-02 04:02:41Z leha $
 * @since		2.0.0
 */

class Core_ConfigController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	/**
	 * Add new setting
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module   = $request->getPost('moduleName');			
			$section1 = $request->getPost('section'); 
			$section2 = $request->getPost('new_section');
			$section  = ($section1 != '') ? $section1 : $section2;
			
			if ($section) {
				$key 	= $request->getPost('key');
				$value 	= $request->getPost('value');
				$file 	= TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'config.ini';
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				
				/**
				 * Could not create new section for config as follow:
				 * <code>
				 * 	$config->$section->$key = $value;
				 * 	$writer->write($file, $config);
				 * </code>
				 * so, below is trick
				 */
				$config = $config->toArray();
				if ($section1 == '') {
					$config[$section] = array();
				}
				$config[$section][$key] = $value;
				$writer = new Zend_Config_Writer_Ini();
				$writer->write($file, new Zend_Config($config));
			}
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_config_list'));
		}	
	}
	
	/**
	 * Configure application
	 * 
	 * @since 2.0.3
	 * @return void
	 */
	public function appAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$user 	 = Zend_Auth::getInstance()->getIdentity();
		$request = $this->getRequest();
		Core_Services_Acl::getInstance()
						->addResource(new Zend_Acl_Resource('core:install'));
						//->allow($user->role_name, 'core:install');
		
		if (!$request->isPost()) {
			$this->_forward('config', 'Install', 'core', array('mode' => 'config'));
		} else {
			/**
			 * User submited config form
			 */
			$params 		= $request->getPost();
			$params['mode'] = 'saveConfig';
			
			$this->_forward('config', 'Install', 'core', $params);
		}
	}
	
	/**
	 * Remove setting
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module	 = $request->getPost('moduleName');
			$section = $request->getPost('section');
			$key 	 = $request->getPost('key');
			
			$file 	= TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'config.ini';
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			unset($config->$section->$key);
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, $config);
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * List settings
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$sections = array();
		$subDirs  = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		$modules  = array();
		
		foreach ($subDirs as $module) {
			$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'config.ini';
			if (file_exists($file)) {
				/**
				 * Get sections from config file
				 */
				$config = new Zend_Config_Ini($file);
				$modules[] = $module;
				$sections[$module] = array();
				foreach ($config->toArray() as $section => $data) {
					$sections[$module][$section] = $data;
				}
			}	
		}
		
		$this->view->assign('modules', $modules);
		$this->view->assign('sections', $sections);
	}
	
	/**
	 * Update setting
	 * 
	 * @return void
	 */
	public function updateAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$keySection = $request->getPost('keySection');
			$newValue 	= $request->getPost('value');
			$arr 		= explode('____', $keySection);
			$module 	= $arr[0];
			$keySection = $arr[1]; 
			$keySection = substr($keySection, strlen('valueFor_'));
			
			if ($newValue != '') {
				$file 	= TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'config.ini';
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				
				list($section, $key) = explode('___', $keySection);
				$config->$section->$key = $newValue;
				$writer = new Zend_Config_Writer_Ini();
				
				/**
				 * If one of key values contain \ then it will be replace with \\,
				 * although we are not editing the key
				 * so, don't use:
				 * <code>
				 * 	$writer->write($file, $config);
				 * </code>
				 */
				
				$config = $config->toArray();
				$newConfig = $config;
				foreach ($newConfig as $section => $data) {
					foreach ($data as $key => $value) {
						$value = str_replace('\\\\', '\\', $value);
						$config[$section][$key] = stripslashes(addslashes($value));
					}
				}
				$writer->write($file, new Zend_Config($config));
			}
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
}
