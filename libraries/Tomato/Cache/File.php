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
 * @version 	$Id: File.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Cache_File 
{
	/**
	 * Support cache by URL or action
	 */
	const CACHE_URL    		= 'url';
	const CACHE_WIDGET 		= 'widget';
	const CACHE_ACTION 		= 'action';
	const CACHE_FILE_PREFIX = 'cache_';
	
	public static function isCached($type, $key, $timeout) 
	{
		$file = self::_generateFileName($type, $key);
		return self::isFileNewerThan($file, time() - $timeout);			
	}
	
	public static function cache($type, $key, $content) 
	{
		$file = self::_generateFileName($type, $key);
		$f = fopen($file, 'w');
		fwrite($f, $content);
		fclose($f);
	}
	
	public static function fromCache($type, $key) 
	{
		$file = self::_generateFileName($type, $key);
		return file_get_contents($file);
	}

	/**
	 * Generate file name
	 *  
	 * @param string $type
	 * @param string $key
	 * @return string
	 */
	private static function _generateFileName($type, $key) 
	{
		/**
		 * TODO: Create file name by encoding the key
		 */
		$dir = TOMATO_TEMP_DIR . DS . 'cache' . DS . $type;
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		return $dir . DS . self::CACHE_FILE_PREFIX . $type . '_' . $key . '.html';
	}
	
	/**
	 * Compare modified time of file to given time stamp
	 * 
	 * @param string $file File name
	 * @param string $compareTo Timestamp to compare
	 * @return bool
	 */
	public static function isFileNewerThan($file, $compareTo) 
	{
		if (!file_exists($file)) {
			return false;
		}
		$modifiedDate = filemtime($file);
		if ($modifiedDate === false) {
			return false;
		}
		if ($modifiedDate < $compareTo) {
			return false;
		}
		return true;
	}
}