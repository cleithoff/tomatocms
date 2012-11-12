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
 * @version 	$Id: Plugin.php 4722 2010-08-18 03:29:06Z huuphuoc $
 * @since		2.0.0
 */

abstract class Tomato_Controller_Plugin extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * @var SimpleXMLElement
	 */
	protected $_params = null;
	
	public function __construct() 
	{
		/**
		 * Get the class of plugin
		 */
		$class = get_class($this);

		/**
		 * Try to read config file from plugin directory
		 */
		$pos  = strrpos($class, '_');
		
		/**
		 * 7 is length of _Plugin
		 */
		$dir  = strtolower(substr($class, 0, -7));
		$file = TOMATO_APP_DIR . DS . str_replace('_', DS, $dir) . DS . 'config.xml';
		if (!file_exists($file)) {
			return;
		}
		$this->_params = simplexml_load_file($file);
	}

	/**
	 * Call when user activate the plugin
	 */
	public function activate() 
	{}
	
	/**
	 * Call when user deactivate the plugin
	 */
	public function deactivate() 
	{}
	
	public function getParam($paramName) 
	{
		if (null == $this->_params) {
			return null;
		}
		$xml = $this->_params->xpath("//param[@name='" . addslashes($paramName) . "']");
		return ($xml == null) ? null : $xml[0]->value;
	}
}
