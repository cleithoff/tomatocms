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
 * @version 	$Id: Plugin.php 3985 2010-07-25 16:14:44Z huuphuoc $
 * @since		2.0.0
 */

/**
 * This plugin will switch the current template to mobile template which 
 * support browse by mobile device
 */
class Plugins_MobileAgent_Plugin extends Tomato_Controller_Plugin 
{
	/**
	 * The agent from browser on mobile devices will be supported
	 */
	private static $_MOBILE_DEVICE = array(
		'sony', 'symbian', 'nokia', 'samsung', 'mobile', 'windows ce', 
		'epoc', 'opera mini', 'nitro', 'j2me', 'midp-', 'cldc-', 'netfront', 'mot',
		'up.browser', 'up.link', 'audiovox', 'blackberry', 'ericsson', 'panasonic',
		'philips', 'sanyo', 'sharp', 'sie-', 'portalmmm', 'blazer', 'avantgo',
		'danger', 'palm', 'series60', 'palmsource', 'pocketpc', 'smartphone',
		'rover', 'ipaq', 'au-mic,', 'alcatel', 'ericy', 'vodafone/', 
		'wap1.', 'wap2.', 'iPhone',   
	);
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) 
	{
		/**
		 * Get the web agent
		 */
		$operaMini 	= $request->getServer('HTTP_X_OPERAMINI_PHONE'); 
		$httpAccept = $request->getServer('HTTP_ACCEPT');
		$userAgent 	= $request->getServer('HTTP_USER_AGENT');
		$isMobile = false;
		if ($operaMini != null && $operaMini != '') {
			$isMobile = true;
		} else if ($httpAccept != null && strpos($httpAccept, 'application/vnd.wap.xhtml+xml') !== false) {
			$isMobile = true;
		} else {
			foreach (self::$_MOBILE_DEVICE as $device) {
				if (strpos($userAgent, $device) !== false) {
					$isMobile = true;
					break;
				}
			}	
		}
		if ($isMobile) {
			/**
			 * Set the template
			 */
			Zend_Registry::set(Tomato_GlobalKey::APP_TEMPLATE, 'mobile');
		}
	}
}
