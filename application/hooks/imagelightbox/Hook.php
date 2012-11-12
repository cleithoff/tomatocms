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
 * @version 	$Id: Hook.php 5093 2010-08-29 17:29:58Z huuphuoc $
 * @since		2.0.0
 */

class Hooks_ImageLightbox_Hook extends Tomato_Hook 
{
	public static function filter($content) 
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view 	= $viewRenderer->view;
		$path 	= $view->APP_STATIC_SERVER . '/js/jquery.lightbox';
		$append = <<<END
		<script type="text/javascript">
		$(document).ready(function() {
			$('<link rel="stylesheet" type="text/css" href="$path/jquery.lightbox-0.5.css" />').appendTo('head');
			$('<script type="text/javascript" src="$path/jquery.lightbox-0.5.pack.js"><' + '/script>').prependTo('body');
			var settings = {
        		imageLoading: "$path/images/lightbox-ico-loading.gif",
				imageBtnPrev: "$path/images/lightbox-btn-prev.gif",
				imageBtnNext: "$path/images/lightbox-btn-next.gif",
				imageBtnClose: "$path/images/lightbox-btn-close.gif",
				imageBlank: "$path/images/lightbox-blank.gif",
				txtImage: "Image",
				txtOf: "of"
			};
        	$('.content').find('img').each(function() {
            	$(this).wrap('<a href="' + $(this).attr("src") + '" class="imagelightbox"></a>');
         	});
        	$('.content a.imagelightbox').lightBox(settings);
    	});
		</script>
END;
		return $content.$append;
	}
}
