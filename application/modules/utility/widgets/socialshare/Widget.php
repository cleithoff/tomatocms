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
 * @version 	$Id: Widget.php 5223 2010-08-31 01:58:18Z huuphuoc $
 * @since		2.0.0
 */

class Utility_Widgets_SocialShare_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		/**
		 * @since 2.0.8
		 */
		$size = $this->_request->getParam('size', 'default');
		$this->_view->assign('size', $size);
	}
}
