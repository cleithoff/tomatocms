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
 * @version 	$Id: Entity.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Model_Entity 
{
	protected $_properties;
	
	public function __construct($data) 
	{
		if (is_object($data)) {
			$data = (array)$data;
		}
		if (!is_array($data)) {
			//throw new Exception('The data must be an array or object');
		}
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
		return $this;
	}
	
	/**
	 * @since 2.0.2
	 * @return array
	 */
	public function getProperties() 
	{
		return $this->_properties;
	}
	
	public function __set($name, $value)
	{
		$this->_properties[$name] = $value;
	}
	
	public function __get($name) 
	{
		if (array_key_exists($name, $this->_properties)) {
			return $this->_properties[$name];
		}
		return null;
	}
	
	public function __isset($name) 
	{
		return isset($this->_properties[$name]);
	}
	
	public function __unset($name) 
	{
		if (isset($this->$name)) {
			$this->_properties[$name] = null;
		}
	}
}
