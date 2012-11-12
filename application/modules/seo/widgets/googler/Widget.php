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
 * @version 	$Id: Widget.php 3756 2010-07-17 12:10:44Z huuphuoc $
 * @since		2.0.3
 */

class Seo_Widgets_Googler_Widget extends Tomato_Widget
{
	protected function _prepareShow()
	{
		$refer = $this->_request->getServer('HTTP_REFERER');
		if (null == $refer) {
			return;
		}
		$refer 	 = strtolower($refer);
		$googler = false;
		if ('http://google.' == substr($refer, 0, 14) || 'http://www.google.' == substr($refer, 0, 18)) {
//		if ('http://localhost' == substr($refer, 0, 16)) {
			/**
			 * Visitor go to our site from Google
			 */
			$googler = true;
			$this->_view->assign('message', $this->_request->getParam('message'));		
		}
		$this->_view->assign('googler', $googler);
	}
}
