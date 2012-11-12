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
 * @version 	$Id: Google.php 3457 2010-07-07 16:53:19Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Get Google Pagerank
 */
class Tomato_Seo_Rank_Google
{
	/**
	 * The URL used to get Google Pagerank
	 */
	const RANK_URL = 'http://www.google.com/search?client=navclient-auto&ch=6%s&ie=UTF-8&oe=UTF-8&features=Rank&q=info:%s';
	
	/**
	 * Get Google Pagerank
	 *  
	 * @param string $url The URL
	 * @return int
	 */
	public static function getRank($url)
	{
		$hash = self::_generateHash(self::_strord('info:'.$url));
		$url  = sprintf(self::RANK_URL, $hash, urlencode($url));
		
		$pr = @file($url);
		if (null == $pr || $pr == '') {
			return null;
		}
		
		$prStr = implode('', $pr);
		return substr($prStr, strrpos($prStr, ':') + 1);	
	}
	
	private static function _convertToInt32($x) 
	{
		$z = hexdec(80000000);
		$y = (int)$x;
		/**
		 * On 64bit OSs if $x is double, negative ,will return -$z in $y
		 * which means 32th bit set (the sign bit)
		 */
		if ($y == -$z && $x < -$z) {
			/**
			 * This is the hack, make it positive before
			 * switch back the sign
			 */
			$y = (int)((-1) * $x);
			$y = (-1) * $y; 
		}
		return $y;
	}
	
	private static function _generateHash($url, $length = null, $init = 0xE6359A60) 
	{
		if (is_null($length)) {
			$length = sizeof($url);
		}
		$a = $b = 0x9E3779B9;
		$c = $init;
		$k = 0;
		$len = $length;
		while ($len >= 12) {
			$a += ($url[$k+0] + ($url[$k+1]<<8) + ($url[$k+2]<<16) + ($url[$k+3]<<24));
			$b += ($url[$k+4] + ($url[$k+5]<<8) + ($url[$k+6]<<16) + ($url[$k+7]<<24));
			$c += ($url[$k+8] + ($url[$k+9]<<8) + ($url[$k+10]<<16) + ($url[$k+11]<<24));
			$mix = self::_mix($a, $b, $c);
			$a = $mix[0];
			$b = $mix[1];
			$c = $mix[2];
			$k   += 12;
			$len -= 12;
		}
		
		$c += $length;
		/** All the case statements fall through */
		switch ($len) {
			case 11: $c += ($url[$k+10]<<24);
			case 10: $c += ($url[$k+9]<<16);
			case 9 : $c += ($url[$k+8]<<8);
			/** The first byte of c is reserved for the length */
			case 8 : $b += ($url[$k+7]<<24);
			case 7 : $b += ($url[$k+6]<<16);
			case 6 : $b += ($url[$k+5]<<8);
			case 5 : $b += ($url[$k+4]);
			case 4 : $a += ($url[$k+3]<<24);
			case 3 : $a += ($url[$k+2]<<16);
			case 2 : $a += ($url[$k+1]<<8);
			case 1 : $a += ($url[$k+0]);
			/** case 0: nothing left to add */
		}		
		$mix = self::_mix($a,$b,$c);
		return $mix[2];
	}	

	private static function _mix($a, $b, $c) 
	{
		$a -= $b; $a -= $c; $a = self::_convertToInt32($a); $a = (int)($a ^ (self::_zeroFill($c, 13)));
	    $b -= $c; $b -= $a; $b = self::_convertToInt32($b); $b = (int)($b ^ ($a<<8));
	    $c -= $a; $c -= $b; $c = self::_convertToInt32($c); $c = (int)($c ^ (self::_zeroFill($b, 13)));
	    $a -= $b; $a -= $c; $a = self::_convertToInt32($a); $a = (int)($a ^ (self::_zeroFill($c, 12)));
	    $b -= $c; $b -= $a; $b = self::_convertToInt32($b); $b = (int)($b ^ ($a<<16));
	    $c -= $a; $c -= $b; $c = self::_convertToInt32($c); $c = (int)($c ^ (self::_zeroFill($b, 5)));
	    $a -= $b; $a -= $c; $a = self::_convertToInt32($a); $a = (int)($a ^ (self::_zeroFill($c, 3)));
	    $b -= $c; $b -= $a; $b = self::_convertToInt32($b); $b = (int)($b ^ ($a<<10));
	    $c -= $a; $c -= $b; $c = self::_convertToInt32($c); $c = (int)($c ^ (self::_zeroFill($b, 15)));
	
		return array($a, $b, $c);
	}
	
	/**
	 * Converts a string into an array of integers containing 
	 * the numeric value of the	char
	 */
	private static function _strord($string) 
	{
		$result = array();
		for ($i = 0; $i < strlen($string); $i++) {
			$result[$i] = ord($string{$i});
		}
		return $result;
	}
	
	/** 
	 * Unsigned shift right
	 * 
	 * @param int $a
	 * @param int $b
	 */
	private static function _zeroFill($a, $b) 
	{
		$z = hexdec(80000000);
		if ($z & $a) {
			$a  = ($a>>1);
			$a &= (~$z);
			$a |= 0x40000000;
			$a  = ($a>>($b-1));
		} else {
			$a = ($a>>$b);
		}
		
		return $a;
	}
}
