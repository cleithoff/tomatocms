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
 * @version 	$Id: LanguageSelector.php 4812 2010-08-24 03:46:31Z huuphuoc $
 * @since		2.0.8
 */

class Core_View_Helper_LanguageSelector extends Zend_View_Helper_Abstract
{
	const DEFAULT_ID   = 'languageSelector';
	const DEFAULT_NAME = 'languageSelector';
	
	/**
	 * Show the select box element that lists all of available languages
	 * 
	 * @return string
	 */
	public function languageSelector($attributes = array('id' => self::DEFAULT_ID, 'name' => self::DEFAULT_NAME, 'selected' => null, 'jsCallback' => null), $translatableData = array())
	{
		$config 	 = Tomato_Config::getConfig();
		$defaultLang = $config->localization->languages->default;
		if (!isset($attributes['selected']) || null == $attributes['selected']) {
			$attributes['selected'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang', $defaultLang); 
		}
		if (!isset($attributes['id'])) {
			$attributes['id'] = self::DEFAULT_ID;
		}
		if (!isset($attributes['name'])) {
			$attributes['name'] = self::DEFAULT_NAME;
		}
		
		$languages = array($attributes['selected'] => $attributes['selected']);
		if (isset($config->localization->languages->list)) {
			foreach (explode(',', $config->localization->languages->list) as $l) {
				$languages[$l] = explode('|', $config->localization->languages->details->$l);
			}
		}
		
		if (isset($attributes['disable']) && $attributes['disable'] != '') {
			unset($languages[$attributes['disable']]);
		}
		
		$this->view->assign('defaultLang', $defaultLang);
		$this->view->assign('languages', $languages);
		$this->view->assign('attributes', $attributes);
		$this->view->assign('translatableData', $translatableData);
		$this->view->assign('autoTranslate', ('true' == $config->localization->translate->auto));
		
		/**
		 * FIXME: Why do we have to add this
		 * If not, there is error in page of articles listing (news module)
		 */
		$this->view->addScriptPath(TOMATO_APP_DIR . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'scripts');
		return $this->view->render('_partial/_languageSelector.phtml');
	}
}
