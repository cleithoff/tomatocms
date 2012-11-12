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
 * @version 	$Id: Cache.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Cache 
{
	/**
	 * Get global cache instance
	 * 
	 * @return Zend_Cache_Core
	 */
	public static function getInstance() 
	{
		$config = Tomato_Config::getConfig();
		if (!isset($config->cache->frontend) || !isset($config->cache->backend)) {
			return null;
		}
		$frontendOptions = $config->cache->frontend->options->toArray();
		$backendOptions  = $config->cache->backend->options->toArray();
		$frontendOptions = self::_replaceConst($frontendOptions);
		$backendOptions  = self::_replaceConst($backendOptions);
		
		return Zend_Cache::factory($config->cache->frontend->name, $config->cache->backend->name,
			$frontendOptions, $backendOptions);
	}
	
	private static function _replaceConst($options) 
	{
		$search 	= array('{DS}', '{TOMATO_TEMP_DIR}');
		$replace 	= array(DS, TOMATO_TEMP_DIR);
		$newOptions = array();
		foreach ($options as $key => $value) {
			$newOptions[$key] = str_replace($search, $replace, $value);
		}
		return $newOptions;
	}
}
