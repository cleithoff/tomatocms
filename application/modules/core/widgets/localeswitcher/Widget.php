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
 * @version 	$Id: Widget.php 5266 2010-08-31 11:25:20Z huuphuoc $
 * @since		2.0.8
 */

class Core_Widgets_LocaleSwitcher_Widget extends Tomato_Widget 
{
	protected function _prepareShow()
	{
		$type = $this->_request->getParam('type', 'default');
		
		$config = Tomato_Config::getConfig()->toArray();
		if ('false' == $config['localization']['enable']) {
			return;
		}
		
		/**
		 * Get base URL (http://localhost/tomatocms/index.php)
		 */
		$baseUrl = rtrim($this->_view->baseUrl(), '/');
		$links   = array();
		
		foreach ($config['localization']['languages']['details'] as $locale => $details) {
			$array = explode('|', $details);
			$links[$locale] = array(
				'href'		  => $baseUrl . '/' . $locale,
				'localName'   => $array[1],
				'englishName' => $array[2],
			);
		}
		
		$this->_view->assign('type', $type);
		$this->_view->assign('links', $links);
	}
}
