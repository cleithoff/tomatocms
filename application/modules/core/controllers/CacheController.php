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
 * @version 	$Id: CacheController.php 4933 2010-08-25 07:21:04Z huuphuoc $
 * @since		2.0.0
 */

class Core_CacheController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Response output taken from cache
	 * 
	 * @return void
	 * @since 2.0.6
	 */
	public function htmlAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if (Zend_Layout::getMvcInstance() != null) {
			$this->_helper->getHelper('layout')->disableLayout();	
		}
		
		$request = $this->getRequest();
		$type 	 = $request->getParam('__cacheType');
		$key 	 = $request->getParam('__cacheKey');

		$content = Tomato_Cache_File::fromCache($type, $key);
		$this->getResponse()->setBody($content);
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Clear cache
	 * 
	 * @return void
	 */
	public function clearAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$type = $request->getPost('type');
			switch ($type) {
				case 'data':
					$cache = Tomato_Cache::getInstance();
					if ($cache) {
						$cache->clean();
					}
					break;
					
				/**
				 * Clear files cached
				 * @since 2.0.6
				 */
				case 'css':
					Core_Services_FileCache::clear(TOMATO_TEMP_DIR . DS . 'cache' . DS . 'css');
					break;
				case 'js':
					Core_Services_FileCache::clear(TOMATO_TEMP_DIR . DS . 'cache' . DS . 'js');
					break;
				case 'pages':
					Core_Services_FileCache::clear(TOMATO_TEMP_DIR . DS . 'cache' . DS . 'url');
					break;
			}
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * Make caching configurations
	 * 
	 * @since 2.0.6
	 * @return void
	 */
	public function configAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$pageDao->setDbConnection($conn);
		
		$pages   = array();
		$dbPages = $pageDao->getOrdered();
		$file    = TOMATO_APP_DIR . DS . 'config' . DS . 'cache.ini';
		$config  = array();
		if (file_exists($file)) {
			$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
			$config = $config->toArray();
		}
		foreach ($dbPages as $page) {
			$cache    = false;
			$lifetime = 0;
			if (isset($config['cache']['routes'][$page->route]['cache'])) {
				$cache    = $config['cache']['routes'][$page->route]['cache']['enable'];
				$lifetime = (int)$config['cache']['routes'][$page->route]['cache']['lifetime']; 
			}
			$pages[] = array(
				'page' 	=> $page,
				'cache'	=> array(
					'enable'   => $cache,
					'lifetime' => $lifetime,
				),
			);
		}
		$appFile   = TOMATO_APP_DIR . DS . 'config' . DS . 'application.ini';
		$appConfig = new Zend_Config_Ini($appFile, null, array('allowModifications' => true));
		$appConfig = $appConfig->toArray();
		
		$compressCss  = isset($appConfig['cache']['compress']['css']) ? (string)$appConfig['cache']['compress']['css'] : 'false';
		$compressJs   = isset($appConfig['cache']['compress']['js']) ? (string)$appConfig['cache']['compress']['js'] : 'false';
		$compressHtml = isset($appConfig['cache']['compress']['html']) ? (string)$appConfig['cache']['compress']['html'] : 'false';
		$this->view->assign('compressCss', $compressCss);
		$this->view->assign('compressJs', $compressJs);
		$this->view->assign('compressHtml', $compressHtml);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$selectedPages = $request->getPost('selectedPages');
			$lifetime      = $request->getPost('lifetime');
			$compressCss   = $request->getPost('compressCss');
			$compressJs    = $request->getPost('compressJs');
			$compressHtml  = $request->getPost('compressHtml');
			
			$layoutWriter  = new Zend_Config_Writer_Ini();
			
			/**
			 * Caching entire pages
			 */
			if (is_array($selectedPages) && count($selectedPages) > 0) {
				foreach ($selectedPages as $index => $pageName) {
					$t = $lifetime[$pageName];
					if ($t > 0) {
						$config['cache']['routes'][$pageName]['cache']['enable']   = 'true';
						$config['cache']['routes'][$pageName]['cache']['lifetime'] = $t;	
					}
				}
				
				/**
				 * Turn on plugin that cache entire pages
				 */
				$appConfig['production']['resources']['frontController']['plugins']['urlCache'] = 'Tomato_Controller_Plugin_UrlCache';
				
			} else {
				if (isset($appConfig['production']['resources']['frontController']['plugins']['urlCache'])) {
					unset($appConfig['production']['resources']['frontController']['plugins']['urlCache']);
				}
				if (isset($config['cache']['routes'])) {
					unset($config['cache']['routes']);
				}
			}
			
			/**
			 * Save to layout file
			 */
			$layoutWriter->write($file, new Zend_Config($config));
			
			/**
			 * Compress
			 */
			$appConfig['cache']['compress']['css']  = ($compressCss != null && (int)$compressCss == 1) ? 'true' : 'false';
			$appConfig['cache']['compress']['js']   = ($compressJs != null && (int)$compressJs == 1) ? 'true' : 'false';
			$appConfig['cache']['compress']['html'] = ($compressHtml != null && (int)$compressHtml == 1) ? 'true' : 'false';
			
			if ($appConfig['cache']['compress']['html'] == 'true') {
				$appConfig['production']['resources']['frontController']['plugins']['htmlCompressor'] = 'Tomato_Controller_Plugin_HtmlCompressor';
			} else {
				if (isset($appConfig['production']['resources']['frontController']['plugins']['htmlCompressor'])) {
					unset($appConfig['production']['resources']['frontController']['plugins']['htmlCompressor']);
				}
			} 
			
			$appWriter = new Zend_Config_Writer_Ini();
			$appWriter->write($appFile, new Zend_Config($appConfig));
			
			$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('cache_config_save_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_cache_config'));
		}
		
		$this->view->assign('pages', $pages);
	}
	
	/**
	 * Delete cache
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$type  = $request->getPost('type');
			$id    = $request->getPost('id');
			$cache = Tomato_Cache::getInstance();
			if ($cache) {
				switch ($type) {
					case 'id':
						$cache->remove($id);
						break;
					case 'tag':
						$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($id));
						break;
				}
			}
			$this->getResponse()->setBody('RESULT_OK');
		}
	}
	
	/**
	 * List cached items
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		/**
		 * Get information of files cached (CSS, Javascript, pages)
		 * @since 2.0.6
		 */
		$dir = TOMATO_TEMP_DIR . DS . 'cache';
		$this->view->assign('cssCached', Core_Services_FileCache::getInfo($dir . DS . 'css', '.htaccess'));
		$this->view->assign('jsCached', Core_Services_FileCache::getInfo($dir . DS . 'js', '.htaccess'));
		$this->view->assign('pagesCached', Core_Services_FileCache::getInfo($dir . DS . 'url', '.htaccess'));
		
		$cache = Tomato_Cache::getInstance();
		if (!$cache) {
			return;
		}
		
		$backend 		= $cache->getBackend();
		$supportListIds = false;
		$supportTags 	= false;
		if ($backend instanceof Zend_Cache_Backend_ExtendedInterface) {
			$capabilities 	= $backend->getCapabilities();
			$supportListIds = $capabilities['get_list'];
			$supportTags	= $capabilities['tags'];
		}
		
		$fillingPercentage = !($cache instanceof Zend_Cache_Backend_ExtendedInterface)
							? null
							: $cache->getFillingPercentage();
		if ($supportListIds && $supportTags) {
			$tags = $cache ? $cache->getTags() : null;
			$cacheIds = array();
			if ($tags) {
				foreach ($tags as $tag) {
					$cacheIds[$tag] = $cache->getIdsMatchingTags(array($tag));
				}
			}
			
			$this->view->assign('tags', $tags);
			$this->view->assign('cacheIds', $cacheIds);
		}
		
		$this->view->assign('cache', $cache);
		$this->view->assign('supportListIds', $supportListIds);
		$this->view->assign('supportTags', $supportTags);
		$this->view->assign('fillingPercentage', $fillingPercentage);
		$this->view->assign('backend', get_class($backend));
	}
}
