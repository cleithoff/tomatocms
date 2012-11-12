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
 * @version 	$Id: Widget.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_Widgets_Html_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
//		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$content = $this->_request->getParam('content', null);
		$file 	 = $this->_request->getParam('file', null);
		if ($content && $content != '') {
			$this->_response->setBody($content);
		} elseif ($file != null) {
			$this->_response->setBody($this->_view->render($file));	 
		}
	}
	
	protected function _prepareConfig()
	{
		$this->_view->assign('uuid', uniqid());
		$params = $this->_request->getParam('params');
		if ($params) {
			$params  = Zend_Json::decode($params);
			$content = $params['content']['value'];
			$this->_view->assign('content', $content);
		}
	}
}
