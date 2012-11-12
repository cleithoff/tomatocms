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
 * @version 	$Id: Loader.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Module_Loader 
{
	/**
	 * @var Tomato_Module_Loader
	 */
	private static $_instance;
	
	/**
	 * @var array
	 */
	private $_moduleNames;
	
	/**
	 * @return Tomato_Module_Loader
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;		
	}
	
	private function __construct() 
	{
		$this->_moduleNames = $this->_getModules();
	}
	
	public function getModuleNames() 
	{
		return $this->_moduleNames;
	}
	
	/**
	 * @return Zend_Controller_Router_Interface
	 */
	public function getRoutes() 
	{
		if (null == $this->_moduleNames) {
			return;
		}
		$router = new Zend_Controller_Router_Rewrite();
		
		foreach ($this->_moduleNames as $name) {
			$configFiles = $this->_loadRouteConfigs($name);
			
			foreach ($configFiles as $file) {
				$config = new Zend_Config_Ini($file, 'routes');
				$router->addConfig($config, 'routes');
			}
		}
		
		return $router;
	}
	
	/**
	 * @return array
	 */
	private function _getModules() 
	{
		return Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
	}
	
	/**
	 * @return array
	 */
	private function _loadRouteConfigs($moduleName) 
	{
		$dir = TOMATO_APP_DIR . DS . 'modules' . DS . $moduleName . DS . 'config' . DS . 'routes';
		if (!is_dir($dir)) {
			return array();
		}
		
		$configFiles = array();
		
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $name = $file->getFilename();
            if (preg_match('/^[^a-z]/i', $name) || ('CVS' == $name) 
            		|| ('.svn' == strtolower($name))) {
                continue;
            }
            $configFiles[] = $dir . DS . $name;
        }
		
		return $configFiles;
	}	
}
