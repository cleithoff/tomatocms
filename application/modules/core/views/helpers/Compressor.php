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
 * @version 	$Id: Compressor.php 4366 2010-08-05 17:23:31Z huuphuoc $
 * @since		2.0.6
 */

class Core_View_Helper_Compressor extends Zend_View_Helper_Abstract 
{
	/**
	 * CSS compress level defined by CssTidy
	 * @const string
	 */
	const CSS_COMPRESS_DEFAULT = 'default';
	const CSS_COMPRESS_LOW	   = 'low_compression';
	const CSS_COMPRESS_HIGHT   = 'high_compression';
	const CSS_COMPRESS_HIGHEST = 'highest_compression';
	
	/**
	 * CSS compress level
	 * @var string
	 */
	private static $_CSS_COMPRESS = self::CSS_COMPRESS_HIGHEST;
	
	/**
	 * Url to caching file
	 * @var string
	 */
	private $_cacheUrl;
	
	/**
	 * Type of file cached
	 * Can be css or js
	 * @var string
	 */
	private $_type;
	
	/**
	 * Compress the JavaScripts or CSS files
	 * 
	 * @param string $type Can be js or css
	 * @param string $prefix
	 * @return Core_View_Helper_Compressor The compressor instance
	 */
	public function compressor($type = 'js', $prefix = 'head')
	{
		$type        = strtolower($type);
		$this->_type = $type;
		
		/**
		 * Check whether the compression is enable or not
		 */
		$config 	 = Tomato_Config::getConfig();
		$compressCss = isset($config->cache->compress_css) ? (string)$config->cache->compress_css : 'false';
		$compressJs  = isset($config->cache->compress_js) ? (string)$config->cache->compress_js : 'false';

		if ($type == 'css' && $compressCss == 'false') {
			$return = $this->view->headLink()->toString() . $this->view->headStyle()->toString();
			return $return;
		} elseif ($type == 'js' && $compressJs == 'false') {
			$return = $this->view->headScript()->toString();
			$this->_resetHeadScript();
			return $return;
		}
		
		$request    = Zend_Controller_Front::getInstance()->getRequest();
		$module     = $request->getModuleName();
		$controller = $request->getControllerName();
		$action     = $request->getActionName();
		$cacheFile  = md5(implode('_', array($prefix, $module, $controller, $action))) . '.' . $type;

		/**
		 * Build cache dir and URL to show cache data
		 */
		$cacheDir = TOMATO_TEMP_DIR . DS . 'cache' . DS . $type;
		$htaccess = $cacheDir . DS . '.htaccess';
		if (!file_exists($cacheDir)) {
			mkdir($cacheDir);
		}
		/**
		 * Create .htaccess file that allows browser to access
		 */
		if (!file_exists($htaccess)) {
			if ($fp = fopen($htaccess, 'wb')) {
				fwrite($fp, 'Allow from all');
				fclose($fp);
			}
		}
		
		$staticUrl       = $config->web->static_server;
		$cachePath       = $cacheDir . DS . $cacheFile;
		$this->_cacheUrl = $staticUrl . '/temp/cache/' . $type . '/' . $cacheFile;
		
		$files     = array();
		$urls      = array();
		$cssStyles = array();
		$jsScripts = array();
		switch ($type) {
			case 'css':
				$iterator = $this->view->headLink()->getIterator();
				foreach ($iterator as $item) {
					if ($item->type == 'text/css') {
						$urls[] = $item->href;
					}
				}
				$styleIterator = $this->view->headStyle()->getIterator();
				foreach ($styleIterator as $item) {
					$cssStyles[] = $item->content;
				}
				break;
				
			case 'js':
				$iterator = $this->view->headScript()->getIterator();
				foreach ($iterator as $item) {
					if ($item->source == null) {
						$urls[] = $item->attributes['src'];
					} else {
						$jsScripts[] = $item->source;
					}
				}
				$this->_resetHeadScript();
				break;
		}
		
		if (count($urls) == 0) {
			return '';
		}
		
		$start = strlen($staticUrl);
		foreach ($urls as $url) {
			if (substr($url, 0, $start) == $staticUrl) {
				$url = substr($url, $start);
				$url = ltrim($url, '/');
				$url = str_replace('/', DS, $url);
				$url = TOMATO_ROOT_DIR . DS . $url;
			}
			$files[] = $url;
		}
		
		/**
		 * Compare the timestamp of the cached file with
		 * timestamp of all included file
		 */
		$maxTimeStamp = 0;
		foreach ($files as $index => $f) {
			if (file_exists($f)) {
				$maxTimeStamp = max($maxTimeStamp, filemtime($f));
			}
		}
		if (file_exists($cachePath) && $maxTimeStamp > filemtime($cachePath)) {
			unlink($cachePath);
		}
		
		if (!file_exists($cachePath)) {
			$content = '';
			switch ($type) {
				case 'css':
					foreach ($files as $index => $f) {
						$path = pathinfo($f);
						$url  = $urls[$index];
						$url  = substr($url, 0, -strlen($path['basename']));
						$url  = rtrim($url, '/');
						
						$str = file_get_contents($f);
						/**
						 * Replace value of background and background-image properties
						 * with the full URL.
						 * We have to handle some expressions:
						 * - background: #Color_Code url(images/...)
						 * - background-image: url(images/...)
						 * - background: url('images/...')
						 * - jQueryUI theme (/js/jquery.ui/themes/base/ui.theme.css)
						 */
						$str = preg_replace("/background([-image]*):(\s*)([#0-9a-zA-Z]*)([\/\*\{a-zA-Z\}\*\/]*)(\s*)url\(([']*)images\/([\w-.]+)([']*)\)/", 
											'background$1: $3 url(' . $url . '/images/$7)', 
											$str);
						$content .= "\n\n" . trim($str);
					}
					foreach ($cssStyles as $style) {
						$content .= "\n\n" . trim($style);
					}
					
					/**
					 * Use CSSTidy to compress CSS content
					 */
					require_once 'csstidy/class.csstidy.php';
					$csstidy = new csstidy();
					$csstidy->set_cfg('remove_last_;', true);
					$csstidy->load_template(self::$_CSS_COMPRESS);
					$csstidy->parse($content);
					$content = $csstidy->print->plain();
					break;
					
				case 'js':
					require_once 'jsmin/jsmin.php';
					foreach ($files as $index => $f) {
						if (file_exists($f)) {
							$str = file_get_contents($f);
						} else {
							$str = file_get_contents($urls[$index]);
						}
						$content .= "\n\n" . trim(JSMin::minify($str));
					}
					foreach ($jsScripts as $script) {
						$content .= "\n\n" . trim(JSMin::minify($script));
					}					
					break;
			}
			if ($fp = fopen($cachePath, 'wb')) {
				fwrite($fp, $content);
				fclose($fp);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		switch ($this->_type) {
			case 'css':
				return '<link rel="stylesheet" type="text/css" href="' . $this->_cacheUrl . '" />';
				break;
			case 'js':
				return '<script type="text/javascript" src="' . $this->_cacheUrl . '"></script>';
				break;
		}
	}
	
	/**
	 * Allows user to call
	 * <code>
	 * 	$this->compressor(...)
	 * </code>
	 * in view script
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}
	
	/**
	 * Reset head scripts collection
	 * 
	 * @return void
	 */
	private function _resetHeadScript()
	{
		/**
		 * Reset the head scripts array
		 * Do NOT work: $this->view->headScript()->set()
		 */
		$this->view->headScript()->getContainer()->exchangeArray(array());
	}
} 
