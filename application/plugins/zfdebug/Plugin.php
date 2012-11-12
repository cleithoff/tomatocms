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
 * @version 	$Id: Plugin.php 4825 2010-08-24 07:18:31Z huuphuoc $
 * @since		2.0.0
 */

class Plugins_ZFDebug_Plugin extends Tomato_Controller_Plugin 
{
	/**
	 * ZFDebug instance
	 * 
	 * @var ZFDebug_Controller_Plugin_Debug
	 */
	private $_zf;
	
	public function __construct() 
	{
		$config = Tomato_Config::getConfig();
		$options = array(
			'jquery_path' => $config->web->url->static . '/js/jquery/jquery.min.js',
    		'plugins' 	  => array(
    			'Variables',
				'Html',
				'Database' => array('adapter' => Tomato_Db_Connection::factory()->getSlaveConnection()), 
				'File' 	   => array('base_path' => TOMATO_ROOT_DIR), 
				'Memory',
				'Time',
				'Registry',
				'Exception',
			)
		);
		
		/**
		 * Registry autoloader for ZFDebug
		 */
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('ZFDebug');
		
		$this->_zf = new ZFDebug_Controller_Plugin_Debug($options);
	}
	
	public function dispatchLoopShutdown() 
	{
		$this->_zf->setRequest(Zend_Controller_Front::getInstance()->getRequest());
		$this->_zf->setResponse(Zend_Controller_Front::getInstance()->getResponse());
		$this->_zf->dispatchLoopShutdown();
	}
}
