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
 * @version 	$Id: LanguageController.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_LanguageController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new language key
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module   = $request->getPost('module_name');
			$language = $request->getPost('language');
			$widget   = $request->getPost('widget');
			
			$section1 = $request->getPost('section'); 
			$section2 = $request->getPost('new_section');
			$section  = ($section1 != '') ? $section1 : $section2;			
			if ($section != '') {
				$key   = $request->getPost('key');
				$value = $request->getPost('value');
				
				$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module;
				$file = ($widget)
						? $file . DS . 'widgets' . DS . $widget . DS . 'lang.' . $language . '.ini'
						: $file . DS . 'languages' . DS . 'lang.' . $language . '.ini';
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				$writer = new Zend_Config_Writer_Ini();
				
				/**
				 * Could not create new section for config as follow:
				 * <code>
				 * 	$config->$section->$key = $value;
				 * 	$writer->write($file, $config);
				 * </code>
				 * so, below is the trick
				 */
				$config = $config->toArray();
				if ($section1 == '') {
					$config[$section] = array();
				}
				$config[$section][$key] = $value;
				
				$writer->write($file, new Zend_Config($config));
				
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('language_add_successful'));
			}					
			$url = ($widget != '')
					? $this->view->url(array('module_name' => $module, 'widget' => $widget, 'language' => $language), 'core_language_edit_widget')
					: $this->view->url(array('module_name' => $module, 'language' => $language), 'core_language_edit_module');
			$this->_redirect($this->view->serverUrl() . $url);
		}	
	}
	
	/**
	 * Delete language key
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module   = $request->getPost('module_name');
			$language = $request->getPost('language');
			$widget   = $request->getPost('widget');
			$section  = $request->getPost('section');
			$key 	  = $request->getPost('key');
			
			$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module;
			$file = ($widget != '')
					? $file . DS . 'widgets' . DS . $widget . DS . 'lang.' . $language . '.ini'
					: $file . DS . 'languages' . DS . 'lang.' . $language . '.ini';
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$writer = new Zend_Config_Writer_Ini();
			
			unset($config->$section->$key);
			$writer->write($file, $config);
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Edit language key
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request  = $this->getRequest();
		$module   = $request->getParam('module_name');
		$language = $request->getParam('language');
		$widget   = $request->getParam('widget', '');
		
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module;
		$file = ($widget != '')
				? $file . DS . 'widgets' . DS . $widget . DS . 'lang.' . $language . '.ini'
				: $file . DS . 'languages' . DS . 'lang.' . $language . '.ini';
		if (!file_exists($file)) {
			return;
		}
		$config	= new Zend_Config_Ini($file, null, array('allowModifications' => true));
		$writer	= new Zend_Config_Writer_Ini();
		
		/**
		 * Update language file
		 */
		$writer->write($file, $config);
		$config = $config->toArray();
		
		$this->view->assign('moduleName', $module);
		$this->view->assign('language', $language);
		if ($widget != '') {
			$this->view->assign('widget', $widget);	
		}
		
		$this->view->assign('data', $config);
		$this->view->assign('sections', array_keys($config));
	}
	
	/**
	 * List languages
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$modules = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		$widgets = array();
		foreach ($modules as $module) {
			$widgets[$module] = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets');
		}
		$this->view->assign('modules', $modules);
		$this->view->assign('widgets', Zend_Json::encode($widgets));
	}
	
	/**
	 * Add new language
	 * 
	 * @return void
	 */
	public function newAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module   = $request->getPost('module_name');
			$widget   = $request->getPost('widget');
			$language = $request->getPost('language');
			
			$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module;
			$file = ($widget) 
					? $file . DS . 'widgets' . DS . $widget . DS . 'lang.' . $language . '.ini'
					: $file . DS . 'languages' . DS . 'lang.' . $language . '.ini';
			if (!file_exists($file)) {
				/**
				 * Create new file
				 */
				$f = fopen($file, 'w');
				fclose($f);
			}
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_language_list'));
		}
	}

	/**
	 * Update language file
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
			$keySection = substr($keySection, strlen('_valueFor_'));
			
			/**
			 * Update language file
			 */
			if ($newValue != '') {
				$module   = $request->getPost('module_name');
				$widget   = $request->getPost('widget');
				$language = $request->getPost('language');
				
				$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module;
				$file = ($widget)
						? $file . DS . 'widgets' . DS . $widget . DS . 'lang.' . $language . '.ini'
						: $file . DS . 'languages' . DS . 'lang.' . $language . '.ini';
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				$writer = new Zend_Config_Writer_Ini();
				
				list($section, $key) = explode('___', $keySection);
				$config->$section->$key = $newValue;
				$writer->write($file, $config);
			}
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Allows user to upload language packages in *.zip format

	 * @since 2.0.4
	 * @return void
	 */
	public function uploadAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			Tomato_Language::upload($_FILES['file']);
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_language_list'));
		}
	}
}
