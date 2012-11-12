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
 * @version 	$Id: ToolkitController.php 4823 2010-08-24 07:04:49Z huuphuoc $
 * @since		2.0.7
 */

class Seo_ToolkitController extends Zend_Controller_Action
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Retrieve backlinks, indexed pages, rank
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$config  	= Tomato_Config::getConfig();
		$currentUrl = $config->web->url->base;
		
		$request = $this->getRequest();
		$act     = $request->getParam('act', '');
		$url     = $request->getParam('url'); 
		
		if ($act != '') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$this->_helper->getHelper('layout')->disableLayout();
		}
		
		$moduleConfig = Tomato_Module_Config::getConfig('seo');
		$adapters = array(
			array(
				'searchEngine' => 'bing',
				'apiKey' 	   => $moduleConfig->api->bing,
			),
			array(
				'searchEngine' => 'yahoo',
				'apiKey'       => $moduleConfig->api->yahoo,
			),
			array(
				'searchEngine' => 'google',
				'apiKey'       => $moduleConfig->api->google,
			),
		);
		
		switch ($act) {
			/**
			 * Get rank
			 */
			case 'rank':
				$googleRank = Tomato_Seo_Rank_Google::getRank($url);
				$alexaRank  = Tomato_Seo_Rank_Alexa::getRank($url);
				$result 	= array(
					'google' => ($googleRank != null) ? $googleRank : 'RESULT_ERROR',
					'alexa'  => ($alexaRank != null) ? $alexaRank : 'RESULT_ERROR',
				);
				$result = Zend_Json::encode($result);
				$this->getResponse()->setBody($result);
				break;

			/**
			 * Get indexed pages
			 */
			case 'indexed':
				$result = array();
				foreach ($adapters as $adapter) {
					$searchEngine = $adapter['searchEngine'];
					$toolkit      = Tomato_Seo_Toolkit::factory($searchEngine);
					$toolkit->setUrl($url)
							->setApiKey($adapter['apiKey']);
							
					$result[$searchEngine] = array(
						'total' => $toolkit->getIndexedPagesCount(),
						'pages' => $toolkit->getIndexedPages(0, 10),
					);
				}
				$result = Zend_Json::encode($result);
				$this->getResponse()->setBody($result);
				break;
			
			/**
			 * Get back links
			 */
			case 'backlink':
				$result = array();
				foreach ($adapters as $adapter) {
					$searchEngine = $adapter['searchEngine'];
					$toolkit      = Tomato_Seo_Toolkit::factory($searchEngine);
					$toolkit->setUrl($url)
							->setApiKey($adapter['apiKey']);
							
					$result[$searchEngine] = array(
						'total' => $toolkit->getBackLinksCount(),
						'pages' => $toolkit->getBackLinks(0, 10),
					);
				}
				$result = Zend_Json::encode($result);
				$this->getResponse()->setBody($result);
				break;
		}		
		
		$this->view->assign('url', $currentUrl);
	}
}
