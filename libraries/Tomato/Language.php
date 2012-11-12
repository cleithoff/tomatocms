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
 * @version 	$Id: Language.php 3867 2010-07-22 07:50:30Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Language
{
	/**
	 * List of supported languages in order of alphabet
	 * @var array
	 */
	public static $LANGUAGES = array(
		/**
		 * English (Built-in language)
		 */
		'en_US' => array(
			'englishName' => 'English',
			'localName'   => 'English',	
		),
		/**
		 * Vietnamese (Built-in language)
		 */
		'vi_VN' => array(
			'englishName' => 'Vietnamese',
			'localName'   => 'Tiếng Việt',	
		),
		/**
		 * German
		 */
		'de_DE' => array(
			'englishName' => 'German',
			'localName'   => 'Deutsch',	
		),
		/**
		 * Spanish
		 */
		'es_ES' => array(
			'englishName' => 'Spanish',
			'localName'   => 'Español',	
		),
		/**
		 * Polish
		 */
		'pl_PL' => array(
			'englishName' => 'Polish',
			'localName'   => 'Polski',	
		),
		/**
		 * Russian
		 */
		'ru_RU' => array(
			'englishName' => 'Russian',
			'localName'   => 'Русский',	
		),
		/**
		 * Chinese
		 */
		'zh_CN' => array(
			'englishName' => 'Chinese',
			'localName'   => '中文',	
		),
	);
	
	/**
	 * Get available languages
	 * 
	 * @return array
	 */
	public static function getAvailableLanguages()
	{
		$languages = array();
		
		/**
		 * Get the list of modules
		 */
		$modules 	= Tomato_Utility_File::getSubDir(TOMATO_APP_DIR . DS . 'modules');
		$numModules = count($modules);
		foreach ($modules as $module) {
			$moduleDir = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'languages';
			if (!file_exists($moduleDir)) {
				continue;
			}
			
			/**
			 * Get list of language files in module
			 */
			$dirIterator = new DirectoryIterator($moduleDir);
			foreach ($dirIterator as $dir) {
				if (!$dir->isDot() && !$dir->isDir()) {
                	$file 	  = $dir->getFilename();
                	$sections = explode('.', $file);
                	$lang 	  = $sections[1];
                	$languages[$lang][] = $module;
            	}
			}
		}
		
		foreach ($languages as $lang => $modules) {
			if (count($modules) == $numModules) {
				if (isset(self::$LANGUAGES[$lang])) {
					$languages[$lang] = self::$LANGUAGES[$lang]; 
				} else {
					$languages[$lang] = array(
						'englishName' => $lang,
						'localName'   => $lang,
					);
				}
			} else {
				unset($languages[$lang]);
			}
		}
		
		return $languages;
	}

	/**
	 * Upload language package
	 * The upload file has to be *.zip file including files organized as follow:
	 * 
	 * TomatoCMS directory structure
	 * 	application
	 * 	|___modules
	 * 		|___<ModuleName>
	 * 			|___languages
	 * 			|	|___lang.LanguageCode.ini
	 * 			|___widgets
	 * 				|___<WidgetName>
	 * 					|___lang.LanguageCode.ini
	 * 
	 * The name of upload file has to be formatted as lang.xx_YY.zip
	 * 
	 * @param string $file Upload file ($_FILES[element])
	 * @return array
	 */
	public static function upload($file)
	{
		/**
		 * Define the language code based on the file's name
		 */
		$sections = explode('.', $file['name']);
		if (!is_array($sections) || count($sections) != 3) {
			throw new Exception('The file name (' . $file['name'] . ') is not valid');
		}
		$language = $sections[1];
		
		/**
		 * Upload file to server
		 */
		$prefix   = 'language_'.time();
		$zipFile  = TOMATO_TEMP_DIR . DS . $prefix . $file['name'];
		move_uploaded_file($file['tmp_name'], $zipFile);
		
		/**
		 * Process uploaded file
		 */
		$zip = Tomato_Zip::factory($zipFile);
		$res = $zip->open();
		if ($res === false) {
			return null;
		}
		
		$tempDir = TOMATO_TEMP_DIR . DS . $prefix;
		if (!file_exists($tempDir)) {
			mkdir($tempDir);
		}
		$zip->extract($tempDir);
		
		/**
		 * Copy language files to associated folder
		 */
		$dirIterator = new DirectoryIterator($tempDir);
		foreach ($dirIterator as $file) {
            if ($file->isDot()) {
                continue;
            }
			$name = $file->getFilename();
            if (preg_match('/^[^a-z]/i', $name) || ('CVS' == $name) 
            		|| ('.svn' == strtolower($name))) {
                continue;
            }
            if ($file->isDir() && 'application' == $name) {
            	Tomato_Utility_File::copyRescursiveDir($tempDir . DS . $name, TOMATO_APP_DIR);
            }
        }
		
		/**
		 * Remove all the temp files
		 */
		$zip->close();
		
		Tomato_Utility_File::deleteRescursiveDir($tempDir);
		unlink($zipFile);
		
		$result = array();
		if (isset(self::$LANGUAGES[$language])) {
			$result[$language] = self::$LANGUAGES[$language];
		} else {
			$result[$language] = array(
				'englishName' => $language,
				'localName'   => $language,
			);
		}
		
		return $result;
	}
}
