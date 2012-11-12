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
 * @version 	$Id: HtmlCompressor.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Inspired from the set of functions created by Oliver Lillie
 * @see http://php100.wordpress.com/2006/10/30/html-compact/#comment-12700
 */
class Tomato_Utility_HtmlCompressor
{
	public static function compress($html) 
	{
		$html = self::_removeHtmlComments($html);
		$ret  = self::_compressHorizontally($html);
		$ret  = self::_compressVertically($ret[0], $ret[1]);
		$html = self::_removeSpacesInScriptAndStyleTags($ret[0]);
		
		return $html;
	}
	
	private static function _removeHtmlComments($html) 
	{
		/**
		 * Check that the opening browser is Internet Explorer
		 */
		$msie 	  = "/msie\s(.*).*(win)/i";
		$keepCond = (isset($_SERVER['HTTP_USER_AGENT']) && preg_match($msie, $_SERVER['HTTP_USER_AGENT']));
		if($keepCond) {
			$html = str_replace(array('<!–[if', ''), array('–**@@IECOND-OPEN@@**–', '–**@@IECOND-CLOSE@@**–'), $html);
		}
		
		/**
		 * Remove comments
		 */
		$html = preg_replace('//', '', $html);
		
		/**
		 * Re sub-in the conditionals if required.
		 */
		if ($keepCond) {
			$html = str_replace(array('–**@@IECOND-OPEN@@**–', '–**@@IECOND-CLOSE@@**–'), array('<!–[if', ''), $html);
		}
		return $html;
	}
	
	/**
	 * Compresses white space horizontally (ie spaces, tabs etc) whilst preserving
	 * textarea and pre content.
	 * Idea and partial code borrowed from smarty.
	 * http://smarty.net/contribs/plugins/view.php/outputfilter.trimwhitespace.php
	 *
	 * @return array
	 */
	private static function _compressHorizontally($html, $preservedBlocks = false) 
	{
		$flag = true;
		if (!$preservedBlocks) {
			$flag = false;
			/**
			 * Get the textarea matches
			 */
			preg_match_all('!]*>.*?!is', $html, $preservedAreaMatch);
			$preservedBlocks = $preservedAreaMatch[0];
			
			/**
			 * Replace the textareas inerds with markers
			 */
			$html = preg_replace('!]*>.*?!is', '@@@HTMLCOMPRESSION@@@', $html);
		}
		
		/**
		 * Remove the white space
		 */
		$html = preg_replace('/((?)\n)[\s]+/m', '\1', $html);
		
		/**
		 * Reinsert the textareas inners
		 */
		if ($flag) {
			foreach ($preservedBlocks as $currBlock) {
				$html = preg_replace('!@@@HTMLCOMPRESSION@@@!', $currBlock, $html, 1);
			}
		}
		return array($html, $preservedBlocks);
	}
	
	/**
	 * Compresses white space vertically (ie line breaks) whilst preserving
	 * textarea and pre content.
	 *
	 * @param mixed $preservedBlocks false if no textarea blocks have already been taken out, otherwise an array.
	 * @return array
	 */
	private static function _compressVertically($html, $preservedBlocks = false) 
	{
		$flag = true;
		if (!$preservedBlocks) {
			$flag = false;
			/**
			 * Get the textarea matches
			 */
			preg_match_all('!]*>.*?!is', $html, $preservedAreaMatch);
			$preservedBlocks = $preservedAreaMatch[0];
			
			/**
			 * Replace the textareas inerds with markers
			 */
			$html = preg_replace('!]*>.*?!is', '@@@HTMLCOMPRESSION@@@', $html);
		}
		$html = str_replace("\n", '', $html);
		
		/**
		 * Reinsert the textareas inerds
		 */
		if ($flag) {
			foreach($preservedBlocks as $currBlock) {
				$html = preg_replace('!@@@HTMLCOMPRESSION@@@!', $currBlock, $html, 1);
			}
		}
		return array($html, $preservedBlocks);
	}
	
	private static function _removeSpacesInScriptAndStyleTags($html) 
	{
		preg_match_all('!(]*>(?:\\s*\\s*)?)!is', $html, $scripts);
		/**
		 * Collect and compress the parts
		 */
		$compressed = array();
		$parts 		= array();
		for ($i = 0; $i < count($scripts[0]); $i++) {
			array_push($parts, $scripts[0][$i]);
			array_push($compressed, self::_removeSpacesInJsAndCss($scripts[0][$i]));
		}
		
		/**
		 * Do the replacements and return
		 */
		$html = str_replace($parts, $compressed, $html);
		return $html;	
	}
	
	private static function _removeSpacesInJsAndCss($code) 
	{
		/**
		 * Remove multiline comment
		 */
		$mulLineComment = '/\/\*(?!-)[\x00-\xff]*?\*\//';
		$code 			= preg_replace($mulLineComment, '', $code);
		
		/**
		 * Remove single line comment
		 */
		$singLineComment = '/[^:]\/\/.*/';
		$code 			 = preg_replace($singLineComment, '', $code);
		
		/**
		 * Remove extra spaces
		 */
		$extraSpace = '/\s+/';
		$code 		= preg_replace($extraSpace, ' ', $code);
		
		/**
		 * Remove spaces that can be removed
		 */
		$removableSpace = '/\s?([\{\};\=\(\)\\\/\+\*-])\s?/';
		$code 			= preg_replace('/\s?([\{\};\=\(\)\/\+\*-])\s?/', '\\1', $code);
		
		return $code;
	}
}
