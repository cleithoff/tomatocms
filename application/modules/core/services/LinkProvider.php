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
 * @version 	$Id: LinkProvider.php 4579 2010-08-12 19:13:03Z huuphuoc $
 * @since		2.0.7
 */

class Core_Services_LinkProvider
{
	/**
	 * Get all the links provided by hooks applying for Core_LinkProvider target
	 * 
	 * @param string $lang The language (since 2.0.8)
	 * @return array
	 */
	public static function getLinks($lang = null)
	{
		$router  = Zend_Controller_Front::getInstance()->getRouter();
		$view    = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		if (null == $lang) {
			$lang = Tomato_Config::getConfig()->web->lang;
		}
		
		$results = array();		
		$links   = array();
		
		/**
		 * Add the homepage link
		 */
		$links['core_index_index'][] = array(
			'href' => $view->url(array('language' => $lang), 'core_index_index'),
		);
		
		$links = Tomato_Hook_Registry::getInstance()->executeFilter('Core_LinkProvider', $links, array($lang));
		foreach ($links as $routeName => $value) {
			/**
			 * Get route description
			 */
			$defaults = $router->getRoute($routeName)->getDefaults();
			$description = isset($defaults['langKey'])
						? $view->translator($defaults['langKey'], $defaults['module'])
						: $routeName;
			$results[$routeName] = array(
				'description' => $description,
				'links'		  => $value,
			);
		}
		
		return $results;
	}
}
