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
 * @version 	$Id: PluginController.php 4535 2010-08-12 09:51:49Z huuphuoc $
 * @since		2.0.0
 */

class Core_PluginController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */

	/**
	 * Configure plugin
	 * 
	 * @return void
	 */
	public function configAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$plugin = $request->getPost('plugin');
			$params = $request->getPost('params');
			$params = Zend_Json::decode($params);
			Tomato_Plugin_Config::saveParams($plugin, $params);
			
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Install plugin
	 * 
	 * @return void
	 */
	public function installAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name = $request->getPost('name');
			$info = Tomato_Plugin_Config::getPluginInfo($name);
			if ($info) {
				$conn = Tomato_Db_Connection::factory()->getMasterConnection();
				$pluginDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPluginDao();
				$pluginDao->setDbConnection($conn);
				$id = $pluginDao->add(new Core_Models_Plugin($info));
				
				/**
				 * Perform the action when plugin is activated
				 */
				$pluginClass = 'Plugins_' . $name . '_Plugin';
				if (class_exists($pluginClass)) {
					$plugin = new $pluginClass();
					$plugin->activate();
				}
				
				$this->getResponse()->setBody($name . ':' . $id);
			} else {
				$this->getResponse()->setBody('RESULT_ERROR');
			}
		}
	}
	
	/**
	 * List plugins
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$plugins = array();
		$subDirs = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'plugins');
		foreach ($subDirs as $pluginName) {
			$info = Tomato_Plugin_Config::getPluginInfo($pluginName);
			if (null == $info) {
				continue;
			}
			$plugin = new Core_Models_Plugin($info);
			$plugin->params = Tomato_Plugin_Config::getParams($pluginName); 
			$plugins[] = $plugin;
		}
		
		/**
		 * Get the list of plugins from database
		 */
		$dbPlugins = array();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pluginDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPluginDao();
		$pluginDao->setDbConnection($conn);
		$rs = $pluginDao->getOrdered();
		if ($rs) {
			foreach ($rs as $row) {
				$key = strtolower($row->name); 
				$dbPlugins[$key] = $key . ':' . $row->plugin_id;
			}
		}
		
		$this->view->assign('plugins', $plugins);
		$this->view->assign('dbPlugins', $dbPlugins);
	}
	
	/**
	 * Uninstall plugin
	 * 
	 * @return void
	 */
	public function uninstallAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name = $request->getPost('name');
			$id   = $request->getPost('id');
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$pluginDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPluginDao();
			$pluginDao->setDbConnection($conn);
			$pluginDao->delete($id);
			
			/**
			 * Perform the action when plugin is deactivated
			 */
			$pluginClass = 'Plugins_' . $name . '_Plugin';
			if (class_exists($pluginClass)) {
				$plugin = new $pluginClass();
				$plugin->deactivate();
			}
			
			$this->getResponse()->setBody($name);
		}
	}
	
	/**
	 * Upload new plugin
	 * 
	 * @return void
	 */
	public function uploadAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$file 	 = $_FILES['file'];
			$prefix  = 'plugin_' . time();
			$zipFile = TOMATO_TEMP_DIR . DS . $prefix . $file['name'];
			move_uploaded_file($file['tmp_name'], $zipFile);
			
			/**
			 * Process uploaded file
			 */
			$zip = Tomato_Zip::factory($zipFile);
			$res = $zip->open();
			if ($res === true) {
				$tempDir = TOMATO_TEMP_DIR . DS . $prefix;
				if (!file_exists($tempDir)) {
					mkdir($tempDir);
				}
				$zip->extract($tempDir);
				
				/**
				 * Get the first (and only) sub-forder 
				 */
				$subDirs = Tomato_Utility_File::getSubDir($tempDir);
				$xml 	 = $tempDir . DS . $subDirs[0] . DS . 'about.xml';
				$info 	 = Tomato_Plugin_Config::getPluginInfoFromXml($xml);
				if ($info) {
					$plugin = new Core_Models_Plugin($info);
					
					/**
					 * TODO: Check whether the plugin was already installed
					 */					
					$conn = Tomato_Db_Connection::factory()->getMasterConnection();
					$pluginDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPluginDao();
					$pluginDao->setDbConnection($conn);
					$id = $pluginDao->add($plugin);
					
					/**
					 * Copy to the plugins directory
					 */
					$pluginDir = TOMATO_APP_DIR . DS . 'plugins' . DS . $plugin->name;
					Tomato_Utility_File::copyRescursiveDir($tempDir . DS . $subDirs[0], $pluginDir);
				} else {
					/**
					 * TODO: Still add the plugin information to database without its about file
					 */
				}
				
				/**
				 * Remove all the temp files
				 */
				$zip->close();
				
				Tomato_Utility_File::deleteRescursiveDir($tempDir);
				unlink($zipFile);
			}
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_plugin_list'));
		}
	}
}
