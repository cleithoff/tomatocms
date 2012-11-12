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
 * @version 	$Id: Config.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Hook_Config 
{
	/**
	 * Get hook information
	 * 
	 * @param string $hook Hook name
	 * @param string $module Module name
	 * @return array
	 */
	public static function getHookInfo($hook, $module = null) 
	{
		$hook = strtolower($hook);
		$file = (null == $module) 
				? TOMATO_APP_DIR . DS . 'hooks' . DS . $hook . DS . 'about.xml'
				: TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'hooks' . DS . $hook . DS . 'about.xml';
		return self::getHookInfoFromXml($file);
	}
	
	/**
	 * @param string $file
	 * @return array
	 */
	public static function getHookInfoFromXml($file) 
	{
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		return array(
			'name' 		  => strtolower($xml->name),
			'module' 	  => $xml->module,
			'description' => $xml->description,
			'thumbnail'   => $xml->thumbnail,
			'author' 	  => $xml->author,
			'email' 	  => $xml->email,
			'version' 	  => $xml->version,
			'license' 	  => $xml->license,
		);
	}

	/**
	 * Get hook params
	 * 
	 * @param string $hook
	 * @param string $module Name of module
	 * @return array
	 */
	public static function getParams($hook, $module = null) 
	{
		$hook = strtolower($hook);
		$file = (null == $module)  
			? TOMATO_APP_DIR . DS . 'hooks' . DS . $hook . DS . 'config.xml'
			: TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'hooks' . DS . $hook . DS . 'config.xml';
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		if ($xml->param == null || count($xml->param) == 0) {
			return null;
		}
		$params = array();
		foreach ($xml->param as $param) {
			$attr = $param->attributes();
			$params[] = array(
				'name' 		  => $attr['name'],
				'description' => (string)$param->description,
				'value' 	  => (string)$param->value,
			);
		}
		return $params;
	}
	
	/**
	 * Save configured params
	 * 
	 * @param array $params
	 * @param string $hook Name of hook
	 * @param string $module Name of module
	 */
	public static function saveParams($params, $hook, $module = null) 
	{
		$hook = strtolower($hook);
		$file = (null == $module)  
			? TOMATO_APP_DIR . DS . 'hooks' . DS . $hook . DS . 'config.xml'
			: TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'hooks' . DS . $hook . DS . 'config.xml';
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		foreach ($params as $key => $value) {
			$nodes = $xml->xpath('param[@name="'.addslashes($key).'"]');
			if ($nodes && is_array($nodes)) {
				$nodes[0]->value = $value;
			}
		}
		$xml->asXML($file);
	}
	
	/**
	 * Get hook targets of given module
	 * 
	 * @param string $module Name of module
	 * @return array
	 */
	public static function getTargetInfo($module) 
	{
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'hook.xml';
		if (!file_exists($file)) {
			return null;
		}
		$xml = simplexml_load_file($file);
		$items = $xml->targets;
		$items = $items[0];
		
		$targets = array();
		foreach ($items as $item) {
			$attr = $item->attributes();
			$targets[] = array(
				'target_module' => $module,
				'target_name' 	=> (string)$attr['name'],
				'description' 	=> (string)$item->description,
				'hook_type' 	=> (string)$attr['type'],
			);
		}
		return $targets;
	}
}
