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
 * @version 	$Id: Hook.php 3981 2010-07-25 16:05:49Z huuphuoc $
 * @since		2.0.0
 */

class Hooks_EmotionIcon_Hook extends Tomato_Hook 
{
	private static $_ICONS = array(
		'smile.png' 	 => array(':-)', ':)', ':]', '=)'),
		'frown.png' 	 => array(':-(', ':(', ':[', '=('),
		'tongue.png'	 => array(':-P', ':P', ':-p', ':p', '=P'),
		'grin.png'		 => array(':-D', ':D', '=D'),
		'gasp.png'		 => array(':-O', ':O', ':-o', ':o'),
		'wink.png'		 => array(';-)', ';)'),
		'glasses.png'	 => array('8-)', '8)', 'B-)', 'B)'),
		'sunglasses.png' => array('8-|', '8|', 'B-|', 'B|'),
		'grumpy.png'	 => array('>:', '( >', ':-('),
		'unsure.png'	 => array(':-/', ':\\', ':-\\'),
		'cry.png' 		 => array(":'("),
		'devil.png'		 => array('3:)', '3:-)'),
		'angel.png'		 => array('O:)', 'O:-)'),
		'kiss.png' 		 => array(':-*', ':*'),
		'heart'			 => array('<3'),
		'kiki.png'		 => array('^_^'),
		'squint.png'	 => array('-_-'),
		'confused.png' 	 => array('o.O', 'O.o'),
		'upset.png' 	 => array('>:O', '>:-O', '>:o', '>:-o'),
		'curly_lips.png' => array(':3'),
	);
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function filter($content) 
	{
		$path = $this->getParam('path');
		$path = rtrim($path, '/');
		
		foreach (self::$_ICONS as $icon => $text) {
			foreach ($text as $str) {
				$content = str_replace($str, '<img src="' . $path . '/' . $icon . '" />', $content);
			}
		}
		return $content;
	}
}
