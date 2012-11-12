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
 * @version 	$Id: Hook.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Hook 
{
	protected $_params = null;
	
	public function __construct() 
	{
		$class = get_class($this);
		$pos   = strrpos($class, '_');
		/**
		 * 7 is length of Tomato_
		 */
		$dir   = strtolower(substr($class, 7, $pos - 7));
		$file  = TOMATO_APP_DIR . DS . str_replace('_', DS, $dir) . DS . 'config.xml';
		if (!file_exists($file)) {
			return;
		}
		$this->_params = simplexml_load_file($file);
	}

	/**
	 * Call when user activate the hook
	 */
	public function activate() 
	{
	}
	
	/**
	 * Call when user deactivate the hook
	 */
	public function deactivate() 
	{
	}
	
	public function getParam($paramName) 
	{
		if (null == $this->_params) {
			return null;
		}
		$xml = $this->_params->xpath("//param[@name='".addslashes($paramName)."']");
		return $xml ? $xml[0]->value : null;
	}
}
