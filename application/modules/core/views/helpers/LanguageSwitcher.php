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
 * @version 	$Id: LanguageSwitcher.php 4808 2010-08-24 03:27:26Z huuphuoc $
 * @since		2.0.8
 */

class Core_View_Helper_LanguageSwitcher extends Zend_View_Helper_Abstract
{
	/**
	 * Show the links that allows user to switch to other language
	 * 
	 * @param string $style    Can take one of following values:
	 * - locale: Show the locale (en_US, for example)
	 * - language: Show the language name (English)
	 * - flag: Show the flag image
	 * 
	 * @param string $separate
	 * @return string
	 */
	public function languageSwitcher($style = 'locale', $separate = ' | ')
	{
		$request   = Zend_Controller_Front::getInstance()->getRequest();
		$baseUrl   = $this->view->baseUrl();
		$baseUrl   = rtrim($baseUrl, '/') . $request->getPathInfo();
		
		$output    = array();
		$config    = Tomato_Config::getConfig();
		$languages = isset($config->localization->languages->list) 
					? explode(',', $config->localization->languages->list)
					: array($config->localization->languages->default);

		$queries = $request->getQuery();
		$query   = '';
		if (is_array($queries) && count($queries) > 0) {
			$query = '?' . http_build_query($queries);
		}
		
		$label = '%s';
		switch ($style) {
			case 'flag':
				$label = '<img src="' . $this->view->APP_STATIC_SERVER . '/images/flags/%s.png" />';
				break;
			case 'locale':
			default:
				$label = '%s';
				break;
		}
					
		foreach ($languages as $lang) {
			$output[] = '<a href="' . $baseUrl . '/' . $lang . $query . '">' . sprintf($label, $lang) . '</a>';
		}
		return implode($separate, $output);
	}
}
