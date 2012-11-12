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
 * @version 	$Id: Request.php 3456 2010-07-07 16:53:01Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Seo_Request
{
	/**
	 * Get response from search engine
	 * 
	 * @param string $url The URL
	 * @param int $timeout The connection timeout (in seconds)
	 * @return string
	 */
	public static function getResponse($url, $timeout = 10)
	{
		if (function_exists('curl_init')) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if ((ini_get('open_basedir') == '') && (ini_get('safe_mode') == 'Off')) {
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			}
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			return @curl_exec($ch);
		} else {
			return @file_get_contents($url);
		}
	}
}
