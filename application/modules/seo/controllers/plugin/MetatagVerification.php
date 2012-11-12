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
 * @version 	$Id: MetatagVerification.php 3904 2010-07-24 14:43:22Z huuphuoc $
 * @since		2.0.7
 */

/**
 * This plugin append Google Webmaster verification into head section
 */
class Seo_Controllers_Plugin_MetatagVerification extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		/**
		 * Get meta tag from configuration file
		 */
		$config = Tomato_Module_Config::getConfig('seo');
		if (!isset($config->gwebmaster->verify_metatag)) {
			return;
		}
		$metaTag = $config->gwebmaster->verify_metatag;
		
		/**
		 * Get view instance
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		
		/**
		 * Add verifying meta tag and set its value
		 */
		$view->headMeta()->setName('google-site-verification', $metaTag);
	}
}
