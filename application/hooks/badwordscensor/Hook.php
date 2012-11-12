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

class Hooks_BadWordsCensor_Hook extends Tomato_Hook 
{
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function filter($content) 
	{
		$badWords  = $this->getParam('badWords');
		$separator = $this->getParam('seperator');
		
		if ($badWords == null || $badWords == '') {
			return $content;
		}
		$badWords = explode($separator, $badWords);
		foreach ($badWords as $word) {
			$newWord = '***';
			$content = str_replace($word, $newWord, $content);
		}
		
		return $content;
	}
}
