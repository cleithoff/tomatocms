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
 * @version 	$Id: InstallController.php 5377 2010-09-10 07:44:37Z huuphuoc $
 * @since		2.0.1
 */

class Core_InstallController extends Zend_Controller_Action 
{
	/**
	 * Available date time formats
	 * TODO: Use Zend_Locale to show the available formats based on the language
	 * @since 2.0.3
	 */
	private static $_DATE_FORMATS = array(
		'm-d-Y',			// 02-22-2010
		'd-m-Y',			// 22-02-2010 
		'm.d.Y', 			// 02.22.2010
		'Y-m-d', 			// 2010-02-22
		'm/d/Y', 			// 02/22/2010
		'm/d/y',			// 02/22/10
		'F d, Y',			// February 22, 2010
		'M. d, y', 			// Feb. 22, 10
		'd F Y', 			// 22 February 2010
		'd-M-y',			// 22-Feb-10
		'l, F d, Y',		// Monday, February 22, 2010
	);
	
	private static $_DATETIME_FORMATS = array(
		'm-d-Y H:i:s', 'm-d-Y h:i:s A',
		'd-m-Y H:i:s', 'd-m-Y h:i:s A', 
		'm.d.Y H:i:s', 'm.d.Y h:i:s A', 
		'Y-m-d H:i:s', 'Y-m-d h:i:s A',
		'm/d/Y H:i:s', 'm/d/Y h:i:s A',
		'm/d/y H:i:s', 'm/d/y h:i:s A', 
		'F d, Y H:i:s', 'F d, Y h:i:s A', 
		'M. d, y H:i:s', 'M. d, y h:i:s A', 
		'd F Y H:i:s', 'd F Y h:i:s A', 
		'd-M-y H:i:s', 'd-M-y h:i:s A', 
		'l, F d, Y H:i:s', 'l, F d, Y h:i:s A',
	);
	
	/**
	 * Support multiple databases
	 * @since 2.0.5
	 */
	private static $_DATABASES = array(
		/**
		 * MySQL with Native driver
		 */ 
		'mysql'	=> array(
			'name' 		 => 'MySQL',
			'extensions' => array('mysql'),
			'data'		 => '/install/tomatocms_sample_db_mysql.sql',
		), 
		/**
		 * MySQL with Pdo driver
		 */
		'pdo_mysql' => array(
			'name' 		 => 'MySQL (Pdo)',
			'extensions' => array('mysql', 'pdo', 'pdo_mysql'),
			'data'		 => '/install/tomatocms_sample_db_mysql.sql',
		),
		/**
		 * SQL Server 2005
		 */
		'sqlsrv' => array(
			'name' 		 => 'Microsoft SQL Server 2005',
			'extensions' => array('sqlsrv'),
			'data'		 => null,
		),
		/**
		 * PostgreSQL
		 */
		'pgsql' => array(
			'name' 		 => 'PostgreSQL',
			'extensions' => array('pgsql'),
			'data'		 => null,
		),
	);
	
	/**
	 * Supported caching systems
	 * @since 2.0.5
	 */
	private static $_CACHES = array(
		'file' => array(
			'name' 		 => 'File',
			'extensions' => null,
		),
		'memcache' => array(
			'name' 		 => 'Memcache',
			'extensions' => array('memcache'),
		),
	);
	
	/**
	 * Default charset
	 */
	private static $_DEFAULT_CHARSET = 'utf-8';
	
	/**
	 * Charsets
	 * @see http://www.cs.tut.fi/~jkorpela/www/nvu-enc.html
	 * @since 2.0.6
	 */
	private static $_CHARSETS = array(
		'Arabic (ISO-8859-6)'			   => 'iso-8859-6',
		'Arabic (Windows-1256)'			   => 'windows-1256',
		'Baltic (ISO-8859-4)'			   => 'iso-8859-4',
		'Baltic (ISO-8859-13)'			   => 'iso-8859-13',
		'Baltic (Windows-1257)'			   => 'windows-1257',
		'Celtic (ISO-8859-14)'			   => 'iso-8859-14',
		'Central European (ISO-8859-2)'	   => 'iso-8859-2',
		'Central European (Windows-1250)'  => 'windows-1250',
		'Chinese Simplified (GBK)'		   => 'x-gbk',
		'Chinese Simplified (gb18030)'	   => 'gb18030',
		'Chinese Traditional (Big5)' 	   => 'big5',
		'Chinese Traditional (Big5-HKSCS)' => 'big5-hkscs',
		'Cyrillic (ISO-8859-5)'			   => 'iso-8859-5',
		'Cyrillic (Windows-1251)'		   => 'windows-1251',
		'Cyrillic (KOI8-R)'				   => 'koi8-r',
		'Cyrillic (KOI8-U)'				   => 'koi8-u',
		'Greek (ISO-8859-7)'			   => 'iso-8859-7',
		'Greek (Windows-1253)'			   => 'windows-1253',
		'Hebrew (ISO-8859-8)'			   => 'iso-8859-8',
		'Hebrew (ISO-8859-8-I)'			   => 'iso-8859-8-i',
		'Hebrew (Windows-1255)'			   => 'windows-1255',
		'Japanese (EUC)'				   => 'euc-jp',
		'Japanese (ISO-2022-JP)'		   => 'iso-2022-jp',
		'Japanese (Shift-JIS)'			   => 'shift-jis',
		'Korean (EUC)' 					   => 'euc-kr',
		'Nordic (ISO-8859-10)'			   => 'iso-8859-10',
		'Romanian (ISO-8859-16)'		   => 'iso-8859-16',
		'South European (ISO-8859-3)'	   => 'iso-8859-3',
		'Thai (ISO-8859-11)'			   => 'iso-8859-11',
		'Thai (Windows-874)'			   => 'windows-874',
		'Turkish (ISO-8859-9)'			   => 'iso-8859-9',
		'Turkish (Windows-1254)'		   => 'windows-1254',
		'Unicode (UTF-8)' 				   => 'utf-8',
		'Unicode (UTF-16LE)'			   => 'utf-16le',
		'Vietnamese (Windows-1258)'		   => 'windows-1258',
		'Western (ISO-8859-1)'			   => 'iso-8859-1',
		'Western (ISO-8859-15)'			   => 'iso-8859-15',
		'Western (Macintosh)'			   => 'macintosh',
		'Western (Windows-1252)'		   => 'windows-1252',
	);
	
	/**
	 * Allow user to select the language package at the first step
	 *
	 * @since 2.0.7
	 * @return void
	 */
	public function indexAction()
	{
		$request     = $this->getRequest();
		$currentLang = Tomato_Config::getConfig()->web->language->code;
		
		if ($request->isPost()) {
			$act = $request->getPost('act');
			
			if ('upload' == $act) {
				/**
				 * Upload new language package
				 */
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				
				$result = Tomato_Language::upload($_FILES['Filedata']);
				$this->getResponse()->setBody(Zend_Json::encode($result));
			} else {
				/**
				 * Update the language in application's configuration file
				 * if user select different language
				 */
				$lang = $request->getPost('lang');
				if ($currentLang != $lang) {
					$file 	= TOMATO_APP_DIR . DS . 'config' . DS . 'application.ini';			
					$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
					$config = $config->toArray();
					
					$config['web']['language']['code'] = $lang;
					
					$writer = new Zend_Config_Writer_Ini();
					$writer->write($file, new Zend_Config($config));
				}
				
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_install_requirement'));
			}
		}
		
		$this->view->assign('languages', Tomato_Language::getAvailableLanguages());
		$this->view->assign('lang', $currentLang);
	}
	
	/**
	 * Step 1: Show required extensions
	 * 
	 * @return void
	 */
	public function requirementAction() 
	{
		/**
		 * Required extensions
		 */
		$extensions = array(
			'gd',
			'json',
			'mbstring',
			'simplexml', 
			'xml',
			'xmlreader',
		);
		$pass = true;
		$requiredExtensions = array();
		foreach ($extensions as $ext) {
			$requiredExtensions[$ext] = extension_loaded($ext);
			$pass = $pass && $requiredExtensions[$ext];
		}
		
		/**
		 * Files/folders must have writing permission
		 */
		$files = array(
			'application' . DS . 'config', 
			'application' . DS . 'templates',
			'temp',
			'upload',
		);
		$writableFiles = array();
		foreach ($files as $f) {
			$writableFiles[$f] = is_writeable(TOMATO_ROOT_DIR . DS . $f);
			$pass = $pass && $writableFiles[$f];
		}
		
		if ($this->getRequest()->isPost() && $pass) {
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_install_config'));	
		}
		
		$this->view->assign('requiredExtensions', $requiredExtensions);
		$this->view->assign('writableFiles', $writableFiles);
		$this->view->assign('pass', $pass);
	}

	/**
	 * Step 2: Save settings
	 * 
	 * @return void
	 */
	public function configAction() 
	{
		/**
		 * Increase execution time
		 */
		@ini_set('execution_time', 0);
		
		$request  = $this->getRequest();
		$mode 	  = $request->getParam('mode', 'install');
		$sections = array();
		$config   = Tomato_Config::getConfig();
		foreach ($config->toArray() as $section => $data) {
			$sections[$section] = $data;				
		}
		
		/**
		 * Database
		 */		
		$master = isset($sections['db']['master']) ? $sections['db']['master'] : null;
		if (null != $master) {
			foreach ($master as $server => $value) {
				$master = $master[$server];
				break;
			}
		}
			
		/**
		 * Web
		 */
		$siteName 		 = isset($sections['web']['name']) ? $sections['web']['name'] : null;
		$defaultTitle 	 = isset($sections['web']['title']) ? $sections['web']['title'] : null;
		$currentTemplate = isset($sections['web']['template']) ? $sections['web']['template'] : null;
		$skin 			 = isset($sections['web']['skin']) ? $sections['web']['skin'] : null;
		$lang 			 = isset($sections['web']['language']['code']) ? $sections['web']['language']['code'] : null;
		$metaKeyword 	 = isset($sections['web']['meta']['keyword']) ? $sections['web']['meta']['keyword'] : null;
		$metaDescription = isset($sections['web']['meta']['description']) ? $sections['web']['meta']['description'] : null;
				
		$currentTimeZone = isset($sections['web']['datetime']['timezone']) ? $sections['web']['datetime']['timezone'] : null;
		$dateTimeFormat  = isset($sections['web']['datetime']['format']['datetime']) ? $sections['web']['datetime']['format']['datetime'] : null;
		$dateFormat 	 = isset($sections['web']['datetime']['format']['date']) ? $sections['web']['datetime']['format']['date'] : null;
		
		/**
		 * Get the list of templates
		 */
		$subDirs   = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'templates');
		$templates = array();
		foreach ($subDirs as $dir) {
			/**
			 * Load template info
			 */
			$file = TOMATO_APP_DIR . DS . 'templates' . DS . $dir . DS . 'about.xml';
			if (!file_exists($file)) {
				continue;
			}
			$xml = simplexml_load_file($file);
			if ((string)$xml->selectable == 'false') {
				continue;
			}
			$template = array();			
			foreach ($xml->skins->skin as $skin) {
				$attrs 		= $skin->attributes();
				$template[] = (string)$attrs['name'];
			}
			$templates[strtolower($xml->name)] = $template;
		}		
		
		$siteUrl  = $request->getScheme() . '://' . $request->getHttpHost();
		$basePath = $request->getBasePath();
		if ($basePath != '') {
			$basePath = ltrim($basePath, '/');
			$basePath = rtrim($basePath, '/');
		}
		$url 	   = ($basePath == '') ? $siteUrl : $siteUrl . '/' . $basePath . '/index.php';
		$staticUrl = ($basePath == '') ? $siteUrl : $siteUrl . '/' . $basePath;
		
		$this->view->assign('mode', $mode);
		
		$this->view->assign('master', $master);
		$this->view->assign('dbPrefix', $sections['db']['prefix']);
		
		/**
		 * Cache settings
		 * @since 2.0.5
		 */
		$cacheTypes = array();
		foreach (self::$_CACHES as $cache => $info) {
			$ok = true;
			if (is_array($info['extensions'])) {
				foreach ($info['extensions'] as $ext) {
					if (!extension_loaded($ext)) {
						$ok = false;
						break;
					}
				}
			}
			if ($ok) {
				$cacheTypes[] = $info['name'];		
			}
		}
		
		$currCacheType = isset($sections['cache']['backend']['name']) 
							? $sections['cache']['backend']['name'] : null;
		$memcacheHost  = isset($sections['cache']['backend']['options']['servers']['server1']['host']) 
							? $sections['cache']['backend']['options']['servers']['server1']['host'] : null;
		$memcachePort  = isset($sections['cache']['backend']['options']['servers']['server1']['port']) 
							? $sections['cache']['backend']['options']['servers']['server1']['port'] : null;
		$cacheLifetime = isset($sections['cache']['frontend']['options']['lifetime']) 
							? $sections['cache']['frontend']['options']['lifetime'] : null;
		$cachePrefix   = isset($sections['cache']['frontend']['options']['cache_id_prefix']) 
							? $sections['cache']['frontend']['options']['cache_id_prefix'] : null;

		$this->view->assign('cacheTypes', $cacheTypes);
		$this->view->assign('currCacheType', $currCacheType);
		$this->view->assign('memcacheHost', $memcacheHost);
		$this->view->assign('memcachePort', $memcachePort);
		$this->view->assign('cacheLifetime', $cacheLifetime);
		$this->view->assign('cachePrefix', $cachePrefix);
		
		/**
		 * Support multiple databases
		 * @since 2.0.5
		 */
		$supportedDatabases = self::$_DATABASES;
		foreach (self::$_DATABASES as $db => $info) {
			if (is_array($info['extensions'])) {
				foreach ($info['extensions'] as $ext) {
					if (!extension_loaded($ext)) {
						unset($supportedDatabases[$db]);
						break;
					}
				}
			}
		}
		$this->view->assign('databases', $supportedDatabases);
		$this->view->assign('databaseType', $sections['db']['adapter']);
		
		$this->view->assign('siteName', $siteName);
		$this->view->assign('url', $url);
		$this->view->assign('currentTemplate', $currentTemplate);
		
		/**
		 * List of all available languages
		 * @since 2.0.7
		 */
		$this->view->assign('languages', Tomato_Language::getAvailableLanguages());		
		$this->view->assign('lang', $lang);
		$this->view->assign('langDirection', isset($sections['web']['language']['direction']) ? $sections['web']['language']['direction'] : 'ltr');
		
		$this->view->assign('currentCharset', isset($sections['web']['charset']) ? $sections['web']['charset'] : self::$_DEFAULT_CHARSET);
		$this->view->assign('charsets', self::$_CHARSETS);
		
		$this->view->assign('metaKeyword', $metaKeyword);
		$this->view->assign('metaDescription', $metaDescription);
		$this->view->assign('defaultTitle', $defaultTitle);
		$this->view->assign('offline', isset($sections['web']['offline']['enable']) ? $sections['web']['offline']['enable'] : null);
		$this->view->assign('offlineMessage', isset($sections['web']['offline']['message']) ? $sections['web']['offline']['message'] : null);
		
		$this->view->assign('sessionLifetime', $sections['web']['session']['cookie_lifetime']);
		$this->view->assign('debugMode', $sections['web']['debug']);

		/**
		 * Use Zend_Locale to populate the available timezones based on the language
		 * @see http://www.php.net/manual/en/timezones.php
		 * @see http://unicode.org/repos/cldr/trunk/docs/design/formatting/zone_log.html#windows_ids
		 * @since 2.0.7
		 */
		Zend_Locale::disableCache(true);
		$timeZones = Zend_Locale::getTranslationList('WindowsToTimezone', $lang);
		ksort($timeZones);
		$this->view->assign('timeZones', $timeZones);

		$this->view->assign('availableDateTimeFormats', self::$_DATETIME_FORMATS);
		$this->view->assign('availableDateFormats', self::$_DATE_FORMATS);
		
		$this->view->assign('currentTimeZone', $currentTimeZone);
		$this->view->assign('dateTimeFormat', $dateTimeFormat);
		$this->view->assign('dateFormat', $dateFormat);
		
		$this->view->assign('templates', $templates);	
		
		if ('saveConfig' == $mode || $request->isPost()) {
			$url = $request->getPost('url');
			$url = rtrim($url, '/');
			
			$file 	= TOMATO_APP_DIR . DS . 'config' . DS . 'application.ini';			
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config = $config->toArray();
			
			/**
			 * Only update database settings in installing mode
			 */
			if ($mode == 'install') {
				unset($config['db']['master']);
				unset($config['db']['slave']);
				
				/**
				 * Allows user to set database prefix
				 * @since 2.0.3
				 */
				unset($config['db']['prefix']);
				$prefix = $request->getPost('prefix');
				$prefix = preg_replace("/\s+/", '', $prefix);
				
				$config['db']['prefix']  = $prefix;
				$config['db']['adapter'] = $request->getPost('databaseType');
				$config['db']['master']['server1']['host'] = $config['db']['slave']['server2']['host'] = $request->getPost('host');
				$config['db']['master']['server1']['port'] = $config['db']['slave']['server2']['port'] = $request->getPost('port');
				$config['db']['master']['server1']['dbname']   = $config['db']['slave']['server2']['dbname']   = $request->getPost('dbname');
				$config['db']['master']['server1']['username'] = $config['db']['slave']['server2']['username'] = $request->getPost('username');
				$config['db']['master']['server1']['password'] = $config['db']['slave']['server2']['password'] = $request->getPost('password');
				$config['db']['master']['server1']['charset']  = $config['db']['slave']['server2']['charset']  = 'utf8';
			}
			
			$config['web']['name']	        = $request->getPost('siteName');
			$config['web']['title']         = $request->getPost('title');
			$config['web']['url']['base']   = $url;
			$config['web']['url']['static'] = $staticUrl;
			$config['web']['template'] 	    = $request->getPost('template');
			$config['web']['skin'] 		    = $request->getPost('skin');
			
			/**
			 * Allows user to set charset
			 * @since 2.0.6
			 */
			$config['web']['charset'] 	            = $request->getPost('charset');
			
			$config['web']['language']['code'] 		= $request->getPost('lang');
			$config['web']['language']['direction'] = $request->getPost('langDirection');
			$config['web']['meta']['keyword']       = preg_replace("/\s+/", ' ', strip_tags($request->getPost('metaKeyword')));
			$config['web']['meta']['description']   = $request->getPost('metaDescription');

			/**
			 * Set baseURL
			 * @since 2.0.3
			 */
			if ('' != $basePath) {
				$config['production']['resources']['frontController']['baseUrl'] = '/' . $basePath . '/index.php';
			} else {
				$config['production']['resources']['frontController']['baseUrl'] = '';	
			}
			
			/**
			 * Allows user to set website in offline message
			 * @since 2.0.3
			 */
			if ((int)$request->getPost('offline') == 1) {
				$config['web']['offline']['enable'] = 'true';
				$config['production']['resources']['frontController']['plugins']['offlineMessage'] = 'Core_Controllers_Plugin_OfflineMessage';
			} else {				
				$config['web']['offline']['enable'] = 'false';
				if (isset($config['production']['resources']['frontController']['plugins']['offlineMessage'])) {
					unset($config['production']['resources']['frontController']['plugins']['offlineMessage']);			
				} 	
			}
			$config['web']['offline']['message'] = $request->getPost('offlineMessage');
			
			/**
			 * Allows user to set session lifetime and debug mode
			 * @since 2.0.5
			 */
			$config['web']['session']['cookie_lifetime'] = $request->getPost('sessionLifetime');
			$config['web']['debug']			             = (int)$request->getPost('debugMode') == 1 ? 'true' : 'false';			
			
			$config['web']['datetime']['timezone'] 			 = $request->getPost('timezone');
			$config['web']['datetime']['format']['datetime'] = $request->getPost('datetimeFormat');
			$config['web']['datetime']['format']['date'] 	 = $request->getPost('dateFormat');
			
			/**
			 * Cache settings
			 */
			if ($request->getPost('cacheType') != '') {
				$config['cache']['frontend']['name'] = 'Core';
				$config['cache']['frontend']['options']['lifetime'] 	   = $request->getPost('cacheLifetime');
				$config['cache']['frontend']['options']['cache_id_prefix'] = $request->getPost('cachePrefix');
				$config['cache']['frontend']['options']['automatic_serialization'] = 'true';

				$config['cache']['backend']['name'] = $request->getPost('cacheType');
				switch (strtolower($request->getPost('cacheType'))) {
					case 'file':
						$config['cache']['backend']['options']['cache_dir'] = '{TOMATO_TEMP_DIR}{DS}cache';
						break;
					case 'memcache':
						$config['cache']['backend']['options']['servers']['server1']['host'] = $request->getPost('memcacheHost');
						$config['cache']['backend']['options']['servers']['server1']['port'] = $request->getPost('memcachePort');
						break;
				}
			}
			
			/**
			 * Turn on MagicQuote plugin which disable magic quote setting if there is
			 * @since 2.0.3
			 */
			if (get_magic_quotes_gpc()) {
				$config['production']['resources']['frontController']['plugins']['magicQuote'] = 'Tomato_Controller_Plugin_MagicQuote';	
			} else {
				unset($config['production']['resources']['frontController']['plugins']['magicQuote']);
			}

			/**
			 * Write configuration to file
			 */
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			if ($mode != 'install') {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('install_config_success'));
				$this->_redirect($this->view->APP_URL . '/admin/core/config/app/');
			} else {			
				/**
				 * Unregistry objects
				 */
				Zend_Registry::_unsetInstance();
				
				/**
				 * Create database tables and init data
				 */
				$ok = true;
				try {
					$conn = Tomato_Db_Connection::factory()->getMasterConnection();
				} catch(Exception $ex) {
					$ok = false;
					$this->_helper->getHelper('FlashMessenger')
						->addMessage(sprintf($this->view->translator('install_config_database_connect_error'), $request->getPost('dbname')));					
				}
				if (!$ok) {
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_install_config'));
				}
				
				try {
					$moduleDirs = Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
					
					$moduleDao 	  = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getModuleDao();
					$widgetDao 	  = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getWidgetDao();
					$resourceDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getResourceDao();
					$privilegeDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPrivilegeDao();
					$templateDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTemplateDao();
					$moduleDao->setDbConnection($conn);
					$widgetDao->setDbConnection($conn);
					$resourceDao->setDbConnection($conn);
					$privilegeDao->setDbConnection($conn);
					$templateDao->setDbConnection($conn);
						
					/**
					 * Install modules
					 */
					$modules = array();
					foreach ($moduleDirs as $module) {
						$modules[] = $moduleDao->install($module);
					}
					foreach ($modules as $module) {
						if ($module) {
							$moduleDao->add($module);
						}
					}
					
					/**
					 * Install widgets
					 * @since 2.0.3
					 */
					$widgetInstaller = new Core_Services_Install_Widget();
					$widgetInstaller->setWidgetInterface($widgetDao);
					foreach ($moduleDirs as $module) {
						$widgetInstaller->install($module);
					}
					
					/**
					 * Create resources and previleges
					 */
					$privilegeInstaller = new Core_Services_Install_Privilege();
					$privilegeInstaller->setPrivilegeInterface($privilegeDao);
					$privilegeInstaller->setResourceInterface($resourceDao);
					foreach ($moduleDirs as $module) {
						$privilegeInstaller->install($module);
					}
					
					/**
					 * Install selected template
					 */
					$templateDao->install($config['web']['template']);
					
					/**
					 * Finally, init data
					 */
					$dbFile = TOMATO_ROOT_DIR . DS . 'install' . DS . 'db.xml';
					$adapter = $request->getPost('databaseType');
					if (file_exists($dbFile)) {
						$xml = simplexml_load_file($dbFile);
						$xpath = $xml->xpath('init[contains(@adapter, "' . $adapter . '")]/module/query');
						if (is_array($xpath) && count($xpath) > 0) {
							foreach ($xpath as $query) {
								$q = str_replace('###', $config['db']['prefix'], (string)$query);
								Tomato_Db_Connection::factory()->query($q);
							}
						}
					}
					
					/**
					 * Allows user to import sample data
					 * @since 2.0.4
					 */
					$importSampleData = $request->getPost('importSampleData');
					if ($importSampleData) {
						/**
						 * Get database adapter from form
						 */
						$file 	  = isset(self::$_DATABASES[$adapter]['data']) ? self::$_DATABASES[$adapter]['data'] : null;
						$importer = Core_Import_ImporterFactory::getInstance();
						if ($importer != null && $file != null) {
							$importer->import(TOMATO_ROOT_DIR . $file);
						}
					}
				} catch (Exception $ex) {
					$ok = false;
					$this->_helper->getHelper('FlashMessenger')->addMessage($ex->getMessage());
				}
				
				if ($ok) {
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_install_complete'));
				} else {
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_install_config'));
				}
			}
		}
	}
	
	/**
	 * Step 3: Complete installing
	 * 
	 * @return void
	 */
	public function completeAction()
	{
		$request = $this->getRequest();
		$baseUrl = $this->view->baseUrl();
		if ('' != $baseUrl) {
			$baseUrl = rtrim($baseUrl, '/');
			$baseUrl = ltrim($baseUrl, '/');
		}
		$url = ('' == $baseUrl) ? $this->view->APP_URL : $this->view->APP_URL . '/index.php';
		
		if ($request->isPost()) {
			/**
			 * Generate install info 
			 */
			$file 	= TOMATO_APP_DIR . DS . 'config' . DS . 'application.ini';			
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config = $config->toArray();
			$config['install']['date'] = date('m-d-Y H:i:s');
			
			/**
			 * Add version info
			 * @since 2.0.3
			 */
			$config['install']['version'] = Tomato_Version::getVersion();
			
			$writer = new Zend_Config_Writer_Ini();
			$writer->write($file, new Zend_Config($config));
			
			$frontend = $request->getPost('gotoFrontend');
			$backend  = $request->getPost('gotoBackend', null);
			if ($backend != null) {
				$this->_redirect($url . '/admin/');
			} else {
				$this->_redirect($url);
			}
		} else {
			/**
			 * Set random password for admin account
			 * @since 2.0.3
			 */
			$password = substr(md5(rand(100000, 999999)), 0, 8);
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$userDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
			$userDao->setDbConnection($conn);
			$userDao->updatePasswordFor('admin', $password);
			
			$this->view->assign('password', $password);
		}

		$this->view->assign('url', $url);
	}
}
