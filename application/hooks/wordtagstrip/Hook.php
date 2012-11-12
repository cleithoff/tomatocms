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

class Hooks_WordTagStrip_Hook extends Tomato_Hook
{
	private static $_REPLACE = array(
		'/<o:p>\s*<\/o:p>/' 					=> '',
		'/<o:p>.*?<\/o:p>/' 					=> '',
		'/\s*mso-[^:]+:[^;"]+;?/' 				=> '&nbsp;',
		'/\s*mso-[^:]+:[^;"]+;?/' 				=> '',
		'/\s*mso-[^:]+:[^;"]+;?/' 				=> '',
		'/\s*MARGIN: 0cm 0cm 0pt\s*;/' 			=> '',
		'/\s*MARGIN: 0cm 0cm 0pt\s*"/' 			=> '',
		'/\s*TEXT-INDENT: 0cm\s*;/' 			=> '',
		'/\s*TEXT-INDENT: 0cm\s*"/' 			=> '',
		'/\s*TEXT-ALIGN: [^\s;]+;?"/' 			=> '',
		'/\s*PAGE-BREAK-BEFORE: [^\s;]+;?"/' 	=> '',
		'/\s*FONT-VARIANT: [^\s;]+;?"/' 		=> '',
		'/\s*tab-stops:[^;"]*;?/' 				=> '',
		'/\s*tab-stops:[^"]*/' 					=> '',
		'/\s*face="[^"]*"/' 					=> '',
		'/\s*face=[^ >]*/' 						=> '',
		'/\s*FONT-FAMILY:[^;"]*;?/' 			=> '',
//		'/<(\w[^>]*) class=([^ |>]*)([^>]*)/i' 	=> '<$1$3',
//		'/<(\w[^>]*) style="([^\"]*)"([^>]*)/i' => '<$1$3',
		'/<SPAN\s*[^>]*>\s*&nbsp;\s*<\/SPAN>/' 	=> '',
		'/<SPAN\s*[^>]*><\/SPAN>/' 				=> '',
		'/<(\w[^>]*) lang=([^ |>]*)([^>]*)/' 	=> '<$1$3',
		'/<SPAN\s*>(.*?)<\/SPAN>/' 				=> '$1',
		'/<FONT\s*>(.*?)<\/FONT>/' 				=> '$1',
		'/<\\?\?xml[^>]*>/' 					=> '',
		'/<\/?\w+:[^>]*>/' 						=> '',
		'/<H\d>\s*<\/H\d>/' 					=> '',
		'/<H1([^>]*)>/' 						=> '',
		'/<H2([^>]*)>/' 						=> '',
		'/<H3([^>]*)>/' 						=> '',
		'/<H4([^>]*)>/' 						=> '',
		'/<H5([^>]*)>/' 						=> '',
		'/<H6([^>]*)>/' 						=> '',
		'/<\/H\d>/' 							=> '<br />',
		'/<(U|I|STRIKE)>&nbsp;<\/\1>/' 			=> '&nbsp;',
		'/<(B|b)>&nbsp;<\/\b|B>/' 				=> '',
		//'/<([^\s>]+)[^>]*>\s*<\/\1>/' 		=> '',
		//'/(<P)([^>]*>.*?)(<\/P>)/i' 			=> '<div>$2</div>',
		//'/(<font|<FONT)([^*>]*>.*?)(<\/FONT>|<\/font>)/i' => '<div>$2</div>',
		'/size|SIZE = ([\d]{1})/i' 				=> '',
		'/<meta([^>]+)content=([^>]*)>/i' 		=> '',
		'/<\/meta>/i' 							=> '',
		'/<link([^>]+)href="file:([^\>]*)"([^>]*)>/i' => '',
		'/<!--\[if(\s+)gte(\s+)mso(\s+)(9|10)\]>(\s*)([<style>]*)([^>]*)([<\/style>]*)(\s*)<!\[endif\]-->/i' => '',
		'/<style(\s+)type="text\/css">(\s*)<!--([^>]*)MsoNormal([^>]*)-->(\s*)<\/style>/i' 					 => '',
		'/<!--(.|\s)*?-->/i' 					=> '', 
	); 
	
	/**
	 * Used for formating content
	 * 
	 * @param string $content
	 * @return string
	 */
	public static function filter($content) 
	{
		foreach (self::$_REPLACE as $search => $replace) {
			$content = preg_replace($search, $replace, $content);
		}
		$content = trim($content);
		return $content;
	}	
}
