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
 * @version 	$Id: Widget.php 4823 2010-08-24 07:04:49Z huuphuoc $
 * @since		2.0.7
 */

class Seo_Widgets_DashboardBacklink_Widget extends Tomato_Widget_Dashboard
{
	protected function _prepareShow()
	{
		$url   = Tomato_Config::getConfig()->web->url->base;
		$limit = $this->_request->getParam('limit', 8);
		if ('' == $limit) {
			$limit = 5;
		}
		
		$toolkit = Tomato_Seo_Toolkit::factory('google');
		$toolkit->setUrl($url);
		
		$config = Tomato_Module_Config::getConfig('seo');
		if (isset($config->api->google)) {
			$toolkit->setApiKey($config->api->google);
		}
		
		$sites = $toolkit->getBackLinks(0, $limit);
		$this->_view->assign('sites', $sites);
	}
}
