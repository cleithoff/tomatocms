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

class Hooks_ContentSplitter_Hook extends Tomato_Hook 
{
	const SEPERATOR = '<!--more-->';
	
	public function __construct() 
	{
		parent::__construct();
	}
	
	public function filter($content) 
	{
		$array 	  = explode(self::SEPERATOR, $content);
		$str 	  = '';
		$prefixId = 'content_section_'.time();
		$pager 	  = '';
		
		if (null == $array || count($array) == 1) {
			return $content;
		}
		foreach ($array as $index => $item) {
			$display = ($index == 0) ? '' : ' style="display: none"';
			$str 	.= '<div id="'.$prefixId.'_'.($index + 1).'" class="t_contentsplitter"'.$display.'>'.$item.'</div>';
			$pager 	.= ' / <a href="javascript: void(0)">'.($index + 1).'</a>';
		}
		/**
		 * Create the pager
		 */
		$pager 		 = substr($pager, 2);
		$topPager 	 = '<div id="'.$prefixId.'_top_pager" class="t_contentsplitter_pager t_g_textleft">'.$pager.'</div>';
		$bottomPager = '<div id="'.$prefixId.'_bottom_pager" class="t_contentsplitter_pager t_g_textright">'.$pager.'</div>';
		
		$str = $topPager.$str.$bottomPager;
		$str .= <<<END
				<script type="text/javascript">
				$(document).ready(function() {
					//$("div.t_contentsplitter").hide();
					$("div.t_contentsplitter_pager").find("a").each(function() {
						$(this).bind("click", function() {
							$("div.t_contentsplitter").hide();
							$("#$prefixId" + "_" + $(this).html()).fadeIn("slow");
						});
					});
				});
				</script>
END;
		return $str;
	}
}
