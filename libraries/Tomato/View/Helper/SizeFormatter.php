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
 * @version 	$Id: SizeFormatter.php 3349 2010-06-28 06:14:27Z huuphuoc $
 * @since		2.0.6
 */

class Tomato_View_Helper_SizeFormatter 
{
	/**
	 * Format file size
	 * 
	 * @param int $bytes File size in bytes
	 * @param int $precision
	 * @see http://php.net/manual/en/function.filesize.php#91477
	 * @return string File size in KB, MB, GB
	 */
	public function sizeFormatter($bytes, $precision = 2)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    	$bytes = max($bytes, 0);
    	$pow   = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    	$pow   = min($pow, count($units) - 1);
    	$bytes /= pow(1024, $pow);
    	   
    	return round($bytes, $precision) . ' ' . $units[$pow];
	}
}
