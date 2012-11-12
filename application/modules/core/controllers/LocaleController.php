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
 * @version 	$Id: LocaleController.php 5270 2010-09-01 06:08:20Z huuphuoc $
 * @since		2.0.8
 */

class Core_LocaleController extends Zend_Controller_Action
{
	/**
	 * Configure localization settings
	 * 
	 * @return void
	 */
	public function configAction()
	{
		$availableLocales = new Zend_Config_Ini(TOMATO_APP_DIR . DS . 'config'  .DS . 'locales.ini', 'locales');
		$availableLocales = $availableLocales->languages->toArray();
		
		$locales = array();
		foreach ($availableLocales as $language) {
			$arr = explode('|', $language);
			$locales[$arr[0]] = array(
				'code'		  => $arr[0],
				'localName'   => $arr[1],
				'englishName' => $arr[2],
			);
		}
		
		/**
		 * Determine the default language
		 */
		$file      = TOMATO_APP_DIR . DS . 'config' . DS . 'application.ini';
		$config    = new Zend_Config_Ini($file, null, array('allowModifications' => true));
		$config    = $config->toArray();
		$enable    = isset($config['localization']['enable'])
						? ('true' == $config['localization']['enable'])
						: false;
		$default   = isset($config['localization']['languages']['default'])
						? $config['localization']['languages']['default']
						: $config['web']['lang'];
		$available = isset($config['localization']['languages']['list'])
						? explode(',', $config['localization']['languages']['list'])
						: array($default);
						
		$request = $this->getRequest();
		if ($request->isPost()) {
			$config['localization']['enable'] = ($request->getPost('localizeEnable') != null)
												? 'true' : 'false';
			
			$default   = $request->getPost('defaultLanguage');
			$available = $request->getPost('supportedLanguages');
			$listLangs = array($default);
			if ($available != null) {
				foreach ($available as $index => $locale) {
					if ($locale != $default) {
						$listLangs[] = $locale;
					}
				}
			}
			
			$config['localization']['languages']['default'] = $default;
			$config['localization']['languages']['list']    = implode(',', $listLangs);
			$config['localization']['languages']['details'] = array();
			
			foreach ($listLangs as $lang) {
				$config['localization']['languages']['details'][$lang] = implode('|', array($locales[$lang]['code'], $locales[$lang]['localName'], $locales[$lang]['englishName'])); 
			}
			
			/**
			 * Translator service settings
			 */
			$config['localization']['translate']['auto']                           
					= ($request->getPost('autoTranslate') != null) ? 'true' : 'false';
			$config['localization']['translate']['service']['using']			   
					= $request->getPost('service', 'google');
			$config['localization']['translate']['service']['google']['apikey']    
					=  $request->getPost('googleApiKey');
			$config['localization']['translate']['service']['microsoft']['apikey'] 
					=  $request->getPost('microsoftApiKey');
			
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			
			$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('locale_config_updated_success'));
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_locale_config'));
		}
		
		$this->view->assign('enable', $enable);
		$this->view->assign('defaultLanguage', $default);
		$this->view->assign('availableLanguages', $available);
		$this->view->assign('config', $config['localization']);
		$this->view->assign('locales', $locales);
	}
	
	/**
	 * Translate the text automatically using the external service
	 * 
	 * @return void
	 */
	public function translateAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->getResponse()->setBody('RESULT_AUTH_REQUIRED');
			exit();
		}
		
		$config  = Tomato_Config::getConfig();
		$request = $this->getRequest();
		if ($request->isPost() && ('true' == $config->localization->translate->auto)) {
			$name     = $request->getPost('name');
			$text     = $request->getPost('text');
			$source   = explode('_', $request->getPost('source')); 
			$target   = explode('_', $request->getPost('target'));
			
			$response = array('name' => $name, 'text' => $text);
			
			/**
			 * Get translator service
			 */
			$service = $config->localization->translate->service->using;
			switch ($service) {
				/**
				 * Use MicrosoftTranslator service
			 	 * - Get supported languages: 
			 	 * http://api.microsofttranslator.com/V1/Http.svc/GetLanguages?appId=xxx
			 	 * @see http://msdn.microsoft.com/en-us/library/ff512399.aspx
			 	 * 
			 	 * - Translate:
			 	 * http://api.microsofttranslator.com/V2/Ajax.svc/Translate?appId=xxx&text=xxx&from=en&to=fr
			 	 * @see http://msdn.microsoft.com/en-us/library/ff512406.aspx
				 */
				case 'microsoft':
					$url    = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate';
					$params = array(
						'text' => htmlentities($text),
						'from' => $source[0],
						'to'   => $target[0],
					);
					if (isset($config->localization->translate->service->microsoft->apikey)) {
						$params['appId'] = $config->localization->translate->service->microsoft->apikey;
					}
					
					$content = file_get_contents($url . '?' . http_build_query($params));
					if (strpos($content, 'ArgumentException') === false) {
						$content = ltrim($content, '"');
						$content = rtrim($content, '"');
						
						$response['text'] = $content;
					} else {
						/**
						 * The target language is not supported or the API key is not valid
						 */
						$response['text'] = 'RESULT_ERROR';					
					}
					break;
				
				/**
				 * Use Google translator service
				 * @see http://code.google.com/apis/ajaxlanguage/documentation/reference.html#_intro_fonje
				 */
				case 'google':
				default:
					/**
					 * The alternative way: Use Zend_Http_Client
					 * <code>
					 * 	$client = new Zend_Http_Client();
					 * 	$client->setConfig(array('timeout' => 30))
					 * 			->setUri($url)
					 * 			->setMethod('GET')
					 * 			->setParameterGet('v', '1.0')
					 * 			->setParameterGet('q', $text)
					 * 			->setParameterGet('langpair', $source[0] . '|' . $target[0]);
					 *	$content = $client->request()->getBody();
					 * </code>
					 */
					$url    = 'http://ajax.googleapis.com/ajax/services/language/translate';
					$params = array(
						'v' 	   => '1.0',
						'q' 	   => $text,
						'langpair' => $source[0] . '|' . $target[0],
					);
					if (isset($config->localization->translate->service->google->apikey)) {
						$params['key'] = $config->localization->translate->service->google->apikey;
					}
					$content          = file_get_contents($url . '?' . http_build_query($params));
					$result           = Zend_Json::decode($content);
					$response['text'] = (200 == $result['responseStatus']) ? $result['responseData']['translatedText'] : 'RESULT_ERROR';
					break;
			}
			
			$this->getResponse()->setBody(Zend_Json::encode($response));
		}
	}
	
	/**
	 * Render the view helper
	 * 
	 * @return void
	 */
	public function viewhelperAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->getResponse()->setBody('RESULT_AUTH_REQUIRED');
			exit();
		} 
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$lang       = $request->getPost('language');
			$class      = $request->getPost('viewHelperClass');
			$attributes = $request->getPost('viewHelperAttributes', '');
			
			$array      = explode('_', $class);
			$moduleName = strtolower($array[0]);
			$helperName = array_pop($array);
			
			$this->getResponse()->setBody($this->view->helperLoader($moduleName)->$helperName(Zend_Json::decode($attributes), $lang));
		}
	}
}
