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
 * @version 	$Id: Registry.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Hook_Registry 
{
	const HOOKS = 'Tomato_Hook_Registry';
	
	/**
	 * @var Tomato_Hook_Registry
	 */
	private static $_instance;
	
	/**
	 * @var array
	 */
	private static $_hooks;
	
	/**
	 * @return Tomato_Hook_Registry
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @return void
	 */
	private function __construct() 
	{
		self::$_hooks = array();
	}
	
	/**
	 * Register an action or a filter
	 * 
	 * @param $name string Name of action or filter
	 * @param $args array Arguments
	 * @return bool
	 */
	public function register($name, $args = array(), $priority = 10) 
	{
		$key = $this->_buildKeyId($args);
		self::$_hooks[$name][$priority][$key] = $args;
		//$hooks[$name][] = $args;
		
		return true;
	}
	
	/**
	 * Unregister an action or filter
	 * 
	 * @param $name string
	 * @param $args array
	 * @return bool
	 */
	public function unregister($name, $args = array(), $priority = 10) 
	{
		$key = $this->_buildKeyId($args);
		$isset = isset(self::$_hooks[$name][$priority][$key]);
		if (true === $isset) {
			unset(self::$_hooks[$name][$priority][$key]);
			if (empty(self::$_hooks[$name][$priority])) {
				unset(self::$_hooks[$name][$priority]);
			}
		}
		return $isset;
	}
	
	/**
	 * @param $name string
	 * @param $priority int
	 * @return bool
	 */
	public function isRegistered($name, $priority = 10) 
	{
		$isset = isset(self::$_hooks[$name][$priority]);
		return $isset;
	}
	
	/**
	 * Unregister all actions or filters
	 * @param $name string
	 * @return bool
	 */
	public function unregisterAll($name, $priority = false) 
	{
		if (isset(self::$_hooks[$name])) {
			if (false !== $priority && isset(self::$_hooks[$name][$priority])) {
				unset(self::$_hooks[$name][$priority]);
			} else {
				unset(self::$_hooks[$name]);
			}
		}	
		return true;
	}
	
	/**
	 * Run filter
	 * 
	 * @param $name
	 * @param $value
	 * @return mixed
	 */
	public function executeFilter($name, $value, $args = array()) 
	{
		$hooks = self::$_hooks;
		
		if (!array_key_exists($name, $hooks)) {
			return $value;
		}
		if (!is_array($hooks[$name])) {
			throw new Exception('There is not hook for ' . $name ."\n");
			return $value;
		}
		
//		$args = array();
//		if (empty($args)) {
//			$args = func_get_args();
//		}
		ksort($hooks[$name]);
		
		foreach ($hooks[$name] as $index => $hookArray) {
			foreach ($hookArray as $key => $hook) {
				/**
				 * $hook can be one of following formats:
				 * 1) 'functionName'
				 * 2) $obj
				 * 3) array('functionName')
				 *    array('functionName', $data)
				 * If we want to call static method, then the function name have to be
				 * formated as: objectClass::methodName
				 * 4) array($obj)
				 * 5) array($obj, 'methodName')
				 *    array($obj, 'methodName', $data) 
				 */
				$object   = null;
				$method   = null;
				$func 	  = null;
				$data 	  = null;
				$haveData = false;
				
				/**
				 * Case 3, 4, 5
				 */
				if (is_array($hook)) {
					if (count($hook) < 1) {
						throw new Exception('Empty array in hooks for ' . $name . "\n");
					}
					/**
					 * Case 4, 5
					 */
					else if (is_object($hook[0])) {
						//$object = $hooks[$name][$index][0];
						list($key, $object) = each($hooks[$name][$index]);
						$object = $object[0];
						/**
						 * Case 4
						 */
						if (count($hook) < 2) {
							$method = 'on' . $name;
						}
						/**
						 * Case 5
						 */ 
						else {
							$method = $hook[1];
							if (count($hook) > 2) {
								$data 	  = $hook[2];
								$haveData = true;
							}
						}
					}
					/**
					 * Case 3
					 */
					else if (is_string($hook[0])) {
						$func = $hook[0];
						if (count($hook) > 1) {
							$data 	  = $hook[1];
							$haveData = true;
						}
					} else {
						throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
					}
				}
				/**
				 * Case 1
				 */
				else if (is_string($hook)) {
					$func = $hook;
				}
				/**
				 * Case 2
				 */
				else if (is_object($hook)) {
					$object = $hooks[$name][$index];
					$method = "on" . $name;
				} else {
					throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
				}
				
				/**
				 * Call method and pass variables
				 */
				if (!is_array($args)) {
					$args = array($args);
				}
				
				$hookArgs = $haveData ? array_merge(array($data), $args) : $args;
				
				if (isset($object)) {
					$func 	  = get_class($object) . '::' . $method;
					$callback = array($object, $method);
				} elseif (false !== ($pos = strpos( $func, '::' ))) {
					$callback = array(substr($func, 0, $pos), substr($func, $pos + 2));
				} else {
					$callback = $func;
				}
				
				if (is_callable($callback)) {
					array_unshift($hookArgs, $value);
					$value = call_user_func_array($callback, $hookArgs);
				}
			}
		}
		
		return $value;
	}
	
	/**
	 * Run action
	 * 
	 * @param $actionName
	 * @param $args
	 * @return void
	 */
	public function executeAction($name, $args = array()) 
	{
		$hooks = self::$_hooks;
		if (!array_key_exists($name, $hooks)) {
			return;
		}
		if (!is_array($hooks[$name])) {
			throw new Exception('There is not hook for ' . $name ."\n");
			return;
		}
		
		ksort($hooks[$name]);
		
		foreach ($hooks[$name] as $index => $hookArray) {
			foreach ($hookArray as $key => $hook) {
				/**
				 * $hook can be one of following formats:
				 * 1) 'functionName'
				 * 2) $obj
				 * 3) array('functionName')
				 *    array('functionName', $data)
				 * If we want to call static method, then the function name have to be
				 * formated as: objectClass::methodName
				 * 4) array($obj)
				 * 5) array($obj, 'methodName')
				 *    array($obj, 'methodName', $data) 
				 */
				$object   = null;
				$method   = null;
				$func 	  = null;
				$data 	  = null;
				$haveData = false;
				
				/**
				 * Case 3, 4, 5
				 */
				if (is_array($hook)) {
					if (count($hook) < 1) {
						throw new Exception('Empty array in hooks for ' . $name . "\n");
					}
					/**
					 * Case 4, 5
					 */
					else if (is_object($hook[0])) {
						//$object = $hooks[$name][$index][0];
						list($key, $object) = each($hooks[$name][$index]);
						$object = $object[0];
						/**
						 * Case 4
						 */
						if (count($hook) < 2) {
							$method = 'on' . $name;
						}
						/**
						 * Case 5
						 */ 
						else {
							$method = $hook[1];
							if (count($hook) > 2) {
								$data 	  = $hook[2];
								$haveData = true;
							}
						}
					}
					/**
					 * Case 3
					 */
					else if (is_string($hook[0])) {
						$func = $hook[0];
						if (count($hook) > 1) {
							$data 	  = $hook[1];
							$haveData = true;
						}
					} else {
						throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
					}
				}
				/**
				 * Case 1
				 */ 
				else if (is_string($hook)) {
					$func = $hook;
				}
				/**
				 * Case 2
				 */ 
				else if (is_object($hook)) {
					$object = $hooks[$name][$index];
					$method = "on" . $name;
				} else {
					throw new Exception('Unknown datatype in hooks for ' . $name . "\n");
				}
				
				/**
				 * Call method and pass variables
				 */
				if (!is_array($args)) {
					$args = array($args);
				}
				//$hookArgs = $haveData ? array_merge(array($data), $args) : $args;
				$hookArgs = $haveData ? array_merge($data, $args) : $args;
				
				if (isset($object)) {
					$func 	  = get_class($object) . '::' . $method;
					$callback = array($object, $method);
				} elseif (false !== ($pos = strpos( $func, '::' ))) {
					$callback = array(substr($func, 0, $pos), substr($func, $pos + 2));
				} else {
					$callback = $func;
				}
				
				if (is_callable($callback)) {
					call_user_func_array($callback, $hookArgs);
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getHooks() 
	{
		if (!Zend_Registry::isRegistered(self::HOOKS) 
			|| null == Zend_Registry::get(self::HOOKS)
		) {
			Zend_Registry::set(self::HOOKS, self::$_hooks);	
		}
		return Zend_Registry::get(self::HOOKS);
	}
	
	/**
	 * @param $args array
	 * @return string
	 */
	private function _buildKeyId($args = array()) 
	{
		/**
		 * String
		 */
		if (is_string($args)) {
			return $args;
		}
		/**
		 * Object
		 */
		if (is_object($args[0])) {
			//return get_class($args[0]).$args[1];
			return get_class($args[0]).'__'.$args[1].uniqid();
		}
		/**
		 * Static method
		 */
		else if (is_string($args[0])) {
			return $arg[0].$args[1];
		}
	}
}
