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
 * @version 	$Id: Factory.php 4353 2010-08-04 16:53:10Z huuphuoc $
 * @since		2.0.5
 */

class Tomato_Model_Dao_Factory
{
	/**
	 * @var Tomato_Model_Dao_Factory
	 */
	private static $_instance;
	
	/**
	 * Database adapter
	 * @var string
	 */
	protected $_dbAdapter = 'Pdo_Mysql';
	
	/**
	 * @var string
	 */
	private $_module;
	
	/**
	 * @var Tomato_Widget
	 */
	private $_widget;
	
	private function __construct()
	{
		$config = Tomato_Config::getConfig();
		$this->_dbAdapter = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($config->db->adapter))));
	}
	
	/**
	 * @return Tomato_Model_Dao_Factory
	 */
	public static function getInstance()
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		/**
		 * Reset
		 */
		self::$_instance->reset();
		return self::$_instance;
	}
	
	/**
	 * @param string $module Name of module
	 * @return Tomato_Model_Dao_Factory
	 */
	public function setModule($module)
	{
		$this->_module = $module;
		return $this;
	}
	
	/**
	 * @param Tomato_Widget $widget Widget instance
	 * @return Tomato_Model_Dao_Factory
	 */
	public function setWidget($widget)
	{
		$this->_widget = $widget;
		$this->_module = $widget->getModule();
		return $this;
	}
	
	/**
	 * Reset the module and widget
	 */
	public function reset()
	{
		$this->_module = null;
		$this->_widget = null;
	}
	
	public function __call($name, $arguments)
	{
		if (strlen($name) <= 6 || substr($name, 0, 3) != 'get' || substr($name, -3) != 'Dao') {
			return;
		}
		$name = substr($name, 3);
		$name = substr($name, 0, -3);
		$name = ucfirst($name);
		if (null == $this->_module) {
			throw new Exception('Module is not set');
		}
		if (null == $this->_widget) {
			$class = ucfirst($this->_module) . '_Models_Dao_' . $this->_dbAdapter . '_' . $name;
		} else {
			$class = ucfirst($this->_module) . '_Widgets_' . ucfirst(strtolower($this->_widget->getName())) . '_Models_Dao_' . $this->_dbAdapter . '_' . $name;
		}
		$dao = new $class();
		return $dao;
	}
}
