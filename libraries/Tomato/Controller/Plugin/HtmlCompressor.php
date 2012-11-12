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
 * @version 	$Id: HtmlCompressor.php 3349 2010-06-28 06:14:27Z huuphuoc $
 * @since		2.0.6
 */

/**
 * Compress HTML
 */
class Tomato_Controller_Plugin_HtmlCompressor extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopShutdown()
	{
		$response = $this->getResponse();
		$body     = $response->getBody();
		$body     = Tomato_Utility_HtmlCompressor::compress($body);
		$response->setBody($body);
	}
}
