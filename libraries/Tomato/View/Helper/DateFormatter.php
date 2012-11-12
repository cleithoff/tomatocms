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
 * @version 	$Id: DateFormatter.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_View_Helper_DateFormatter extends Zend_View_Helper_Abstract 
{
	private static $_DIFF_FORMAT = array(
		'DAY' 			=> '%s days ago',
		'DAY_HOUR'		=> '%s days %s hours ago',
		'HOUR' 			=> '%s hours ago',
		'HOUR_MINUTE' 	=> '%s hours %s minute ago',
		'MINUTE' 		=> '%s minutes ago',
		'MINUTE_SECOND'	=> '%s minutes %s seconds ago',
		'SECOND'		=> '%s seconds ago',
	);
	
	public function dateFormatter() 
	{
		return $this;
	}
	
	/**
	 * Get the diff between given timestamp and now
	 * 
	 * @param int $timestamp
	 * @param array $formats
	 * @return string
	 */
	public function diff($timestamp, $formats = null) 
	{
		if ($formats == null) {
			$formats = self::$_DIFF_FORMAT;
		}
		$seconds = time() - $timestamp;
		$minutes = floor($seconds / 60);
		$hours 	 = floor($minutes / 60);
		$days 	 = floor($hours / 24);
		
		if ($days > 0) {
			$diffFormat = 'DAY';
		} else {
			$diffFormat = ($hours > 0) ? 'HOUR' : 'MINUTE';
			if ($diffFormat == 'HOUR') {
				$diffFormat .= ($minutes > 0 && ($minutes - $hours * 60) > 0) ? '_MINUTE' : '';
			} else {
				$diffFormat = (($seconds - $minutes * 60) > 0 && $minutes > 0) 
								? $diffFormat.'_SECOND' : 'SECOND';
			}
		}
		
		$dateDiff = null;
		switch ($diffFormat) {
			case 'DAY':
				$dateDiff = sprintf($formats[$diffFormat], $days);
				break;
			case 'DAY_HOUR':
				$dateDiff = sprintf($formats[$diffFormat], $days, $hours - $days * 60);
				break;
			case 'HOUR':
				$dateDiff = sprintf($formats[$diffFormat], $hours);
				break;
			case 'HOUR_MINUTE':
				$dateDiff = sprintf($formats[$diffFormat], $hours, $minutes - $hours * 60);
				break;
			case 'MINUTE':
				$dateDiff = sprintf($formats[$diffFormat], $minutes);
				break;
			case 'MINUTE_SECOND':
				$dateDiff = sprintf($formats[$diffFormat], $minutes, $seconds - $minutes * 60);
				break;
			case 'SECOND':
				$dateDiff = sprintf($formats[$diffFormat], $seconds);
				break;
		}
		return $dateDiff;
	}
}
