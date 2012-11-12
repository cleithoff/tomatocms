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
 * @version 	$Id: FileCache.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.6
 */

class Core_Services_FileCache
{
	/**
	 * Get information of caching directory
	 * 
	 * @param string $dir
	 * @param string $excludeFile
	 * @return array Array includes the following keys:
	 * - numberFiles: Number of files
	 * - size: Total size
	 */
	public static function getInfo($dir, $excludeFile = null)
	{
		$return = array(
			'numberFiles' => 0,
			'size'		  => 0,
		);
		
		if (!file_exists($dir) || !is_dir($dir)) {
			return $return;
		}
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $name = $file->getFilename();
            if ($excludeFile == null || ($excludeFile != null && $name != $excludeFile)) { 
            	$return['numberFiles']++;
            	$return['size'] += filesize($dir . DS . $name);
            }
        }
		
		return $return;
	}

	/**
	 * Clear all caching file in given directory
	 * 
	 * @param string $dir
	 */
	public static function clear($dir)
	{
		return Tomato_Utility_File::deleteRescursiveDir($dir);
	}
}
