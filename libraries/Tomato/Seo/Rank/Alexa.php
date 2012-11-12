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
 * @version 	$Id: Alexa.php 3457 2010-07-07 16:53:19Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Seo_Rank_Alexa
{
	/**
	 * The URL used to get Alexa rank
	 */
	const RANK_URL = 'http://data.alexa.com/data?cli=10&dat=snbamz&url=%s';
	
	/**
	 * Get Alexa rank
	 *  
	 * @param string $url The URL
	 * @return int
	 */
	public static function getRank($url)
	{
		$url = sprintf(self::RANK_URL, urlencode($url));
		$xml = simplexml_load_file($url);
		$path = $xml->xpath('/ALEXA/SD/POPULARITY');
		if (null == $path || !is_array($path)) {
			return null;
		}
		$attrs = $path[0]->attributes();
		$rank = (string) $attrs['TEXT'];
		
		return $rank;
	}
}
