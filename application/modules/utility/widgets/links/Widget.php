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
 * @version 	$Id: Widget.php 5373 2010-09-10 02:22:07Z huuphuoc $
 * @since		2.0.9
 */

class Utility_Widgets_Links_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$title = $this->_request->getParam('title');
		$links = $this->_request->getParam('links');
		
		$links = Zend_Json::decode($links);
		
		$this->_view->assign('title', $title);
		$this->_view->assign('links', $links);
	}
	
	protected function _prepareConfig() 
	{
		$params = $this->_request->getParam('params');
		if ($params) {
			$params = Zend_Json::decode($params);
			
			if (isset($params['links'])) {
				$links  = $params['links']['value'];
				$links  = Zend_Json::decode($links);
				$this->_view->assign('ultilyLinks', $links);
			}
		}
	}
}
