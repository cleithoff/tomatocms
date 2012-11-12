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
 * @version 	$Id: GwebmasterController.php 4823 2010-08-24 07:04:49Z huuphuoc $
 * @since		2.0.7
 */

class Seo_GwebmasterController extends Zend_Controller_Action
{
	/**
	 * @const string
	 */
	const GOOGLE_AUTH_SUB_TOKEN = 'GOOGLE_AUTH_SUB_TOKEN';
	
	/**
	 * @const string
	 */
	const GOOGLE_CLIENT_LOGIN_TOKEN = 'GOOGLE_CLIENT_LOGIN_TOKEN';
	
	/**
	 * Google Web Master service name.
	 * Do NOT change this value
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#gs_authentication
	 * @const string
	 */
	const GOOGLE_WEB_MASTER_SERVICE = 'sitemaps';
	
	/**
	 * Do NOT change this value
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#gs_authentication
	 * @const string
	 */
	const GOOGLE_WEB_MASTER_SCOPE = 'https://www.google.com/webmasters/tools/feeds/';

	/**
	 * @var Zend_Gdata_HttpClient
	 */
	private $_client = null;
	
	/* ========== Backend actions =========================================== */

	/**
	 * @return void
	 */
	public function init()
	{
		/**
		 * If user has not logged in on Google
		 */
		if (!isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]) || !isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			/**
		 	 * Check whether user have configured Google account or not
		 	 */
			$config = Tomato_Module_Config::getConfig('seo');
			if (isset($config->google->username) && isset($config->google->password)) {
				$username = $config->google->username;
				$password = $config->google->password;
				
				$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, self::GOOGLE_WEB_MASTER_SERVICE);
				$_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] = $client->getClientLoginToken();
			} else {	
				if (isset($_GET['token'])) {
					$_SESSION[self::GOOGLE_AUTH_SUB_TOKEN] = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
				} else {
					$next = $this->view->serverUrl().$this->view->url(array(), 'seo_gwebmaster_list');
					$this->_redirect(Zend_Gdata_AuthSub::getAuthSubTokenUri($next, self::GOOGLE_WEB_MASTER_SCOPE, 0, 1));
					exit();
				}
			}
		}
	}
	
	/**
	 * Add site
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#AD_Adding
	 * @return void
	 */
	public function addAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$url  = $request->getPost('url');

			$data =   "<atom:entry xmlns:atom='http://www.w3.org/2005/Atom'>"
					. '<atom:content src="' . $url . '" />'
					. '</atom:entry>';

			$client = new Zend_Gdata_HttpClient();
			if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
				$client->setClientLoginToken($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN]);
			} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
				$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]);
			}
			
			$gdata    = new Zend_Gdata($client);
			$response = $gdata->post($data, 'https://www.google.com/webmasters/tools/feeds/sites/', null, 'application/atom+xml');
			
			if ('201' == $response->getStatus() && 'Created' == $response->getMessage()) {
				/**
				 * Add site successfully
				 */
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('gwebmaster_add_site_success'));
			} else {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('gwebmaster_add_site_fail'));
			}
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'seo_gwebmaster_list'));
		}
	}
	
	/**
	 * Add sitemap
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#Sitemaps_Submitting
	 * @return void
	 */
	public function addsitemapAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$url 	  = $request->getPost('url');
			$verified = $request->getPost('verified');
			$sitemap  = $request->getPost('sitemap');
			$url 	  = urldecode($url);
			
			/**
			 * Could not use the format provided by Google guide as follow, 
			 * because the XML has no-namespace 
			 * (http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#Sitemaps_Submitting)
			 * 
			 * <code>
			 * $data = "<atom:entry>"
			 * 			.  '<atom:id>' . $sitemap . '</atom:id>'
			 * 			.  "<atom:category scheme='http://schemas.google.com/g/2005#kind'" 
			 *          .  " term='http://schemas.google.com/webmasters/tools/2007#sitemap-regular'/>"
			 *          .  '<wt:sitemap-type>WEB</wt:sitemap-type>'
			 *          . '</atom:entry>';
			 * </code>          			 
			 */
			$data =   '<entry xmlns="http://www.w3.org/2005/Atom"'
					. '    xmlns:wt="http://schemas.google.com/webmasters/tools/2007">'
					. '<id>' . $sitemap . '</id>'
					. "<category scheme='http://schemas.google.com/g/2005#kind'"
					. "    term='http://schemas.google.com/webmasters/tools/2007#sitemap-regular'/>"
					. '<wt:sitemap-type>WEB</wt:sitemap-type>'
					. '</entry>';

			$client = new Zend_Gdata_HttpClient();
			if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
				$client->setClientLoginToken($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN]);
			} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
				$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]);
			}
			
			$gdata    = new Zend_Gdata($client);
			$response = $gdata->post($data, 'https://www.google.com/webmasters/tools/feeds/' . urlencode($url) . '/sitemaps/', null, 'application/atom+xml');
			
			if ('201' == $response->getStatus()) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('gwebmaster_add_sitemap_success'));
			} else {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('gwebmaster_add_sitemap_fail'));
			}
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('url' => urlencode($url), 'verified' => $verified), 'seo_gwebmaster_details'));
		}
	}
	
	/**
	 * Delete site
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#AD_Deleting
	 * @return void
	 */
	public function deleteAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$url = $request->getPost('url');
			$url = urlencode($url);
			$url = 'https://www.google.com/webmasters/tools/feeds/sites/'.$url;
			
			$client = new Zend_Gdata_HttpClient();
			if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
				$client->setClientLoginToken($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN]);
			} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
				$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]);
			}
						
			$gdata    = new Zend_Gdata($client);
			$response = $gdata->delete($url);
			
			if ('200' == $response->getStatus() && 'OK' == $response->getMessage()) {
				$this->getResponse()->setBody('RESULT_OK');
			} else {
				$this->getResponse()->setBody('RESULT_ERROR');
			}
		}
	}

	/**
	 * Delete sitemap
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#Sitemaps_Deleting
	 * @return void
	 */
	public function deletesitemapAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$url 		= $request->getPost('url');
			$sitemapUrl = $request->getPost('sitemapUrl');
			
			$url 		= urlencode($url);
			$sitemapUrl = urlencode($sitemapUrl);
			
			/**
			 * There is typo on Google guide
			 * (http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#Sitemaps_Deleting)
			 * The correct URL is
			 * 'https://www.google.com/webmasters/tools/feeds/' . $url . '/sitemaps/' . $sitemapUrl
			 * not
			 * 'https://www.google.com/webmasters/tools/feeds/' . $url . '/sitemaps/' . $url . '/sitemaps/' . $sitemapUrl
			 */
			$url = 'https://www.google.com/webmasters/tools/feeds/' . $url . '/sitemaps/' . $sitemapUrl;
			
			$client = new Zend_Gdata_HttpClient();
			if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
				$client->setClientLoginToken($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN]);
			} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
				$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]);
			}
			
			$gdata    = new Zend_Gdata($client);
			$response = $gdata->delete($url);
			
			if ('200' == $response->getStatus() && 'OK' == $response->getMessage()) {
				$this->getResponse()->setBody('RESULT_OK');
			} else {
				$this->getResponse()->setBody('RESULT_ERROR');
			}			
		}
	}
	
	/**
	 * View site details
	 * 
	 * @return void
	 */
	public function detailsAction()
	{
		$request  = $this->getRequest();
		$url 	  = $request->getParam('url');
		$url 	  = urldecode($url);
		$verified = $request->getParam('verified');
		
		$client = new Zend_Gdata_HttpClient();
		$client->setMethod(Zend_Http_Client::GET);
		
		if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			$client->setHeaders('authorization', 'GoogleLogin auth="'.$_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN].'"');
		} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
			$client->setHeaders('authorization', 'AuthSub token="'.$_SESSION[self::GOOGLE_AUTH_SUB_TOKEN].'"');
		}
		
		$client->setParameterGet('alt', 'json');
		
		/**
		 * Get list of sitemaps
		 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#Sitemaps_Requesting
		 */
		$client->setUri('https://www.google.com/webmasters/tools/feeds/' . urlencode($url) . '/sitemaps/');
		$response = $client->request()->getBody();
		$response = Zend_Json::decode($response);
		
		$sitemaps = array();
		if (isset($response['feed']['entry'])) {
			foreach ($response['feed']['entry'] as $item) {
				$sitemaps[] = array(
					'link'   		 => $item['title']['$t'],
					'status' 		 => $item['wt$sitemap-status']['$t'],
					'lastDownloaded' => $item['wt$sitemap-last-downloaded']['$t'],
					'urlCount'		 => $item['wt$sitemap-url-count']['$t'],
				);
			}
		}
		
		/**
		 * Get list of keywords
		 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#Keywords_retrieving
		 */
		$client->setUri('https://www.google.com/webmasters/tools/feeds/' . urlencode($url) . '/keywords/');
		$response = $client->request()->getBody();
		$response = Zend_Json::decode($response);
		
		$keywords = array();
		if (isset($response['feed']['wt$keyword'])) {
			foreach ($response['feed']['wt$keyword'] as $item) {
				$keywords[] = array(
					'source'  => $item['source'],
					'keyword' => $item['$t'],
				);
			}
		}
		
		/**
		 * Allows user to verify website if he/she is viewing the current site
		 * and the site has not been verified
		 */
		$currentUrl   = Tomato_Config::getConfig()->web->url->base;
		$needToVerify = ($currentUrl == $url) && ('no' == $verified);
		
		$this->view->assign('url', $url);
		$this->view->assign('needToVerify', $needToVerify);
		$this->view->assign('verified', $verified);
		$this->view->assign('sitemaps', $sitemaps);
		$this->view->assign('keywords', $keywords);
	}
	
	/**
	 * List of sites
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#AD_Retrieving
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/reference.html#Feeds
	 * @return void
	 */
	public function listAction()
	{
		$request = $this->getRequest();
		$client  = new Zend_Gdata_HttpClient();
		$client->setMethod(Zend_Http_Client::GET);
		
		if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			$client->setHeaders('authorization', 'GoogleLogin auth="' . $_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] . '"');
		} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
			$client->setHeaders('authorization', 'AuthSub token="' . $_SESSION[self::GOOGLE_AUTH_SUB_TOKEN] . '"');
		}
		
		$client->setUri('https://www.google.com/webmasters/tools/feeds/sites/');
		$client->setParameterGet('alt', 'json');
		
		$response = $client->request()->getBody();
		$response = Zend_Json::decode($response);
		
		$sites = array();
		if (isset($response['feed']['entry'])) {
			foreach ($response['feed']['entry'] as $entry) {
				$sites[] = array(
					'siteId'   => $entry['id']['$t'],
					'link'	   => $entry['content']['src'],
					'verified' => $entry['wt$verified']['$t'], 
				);
			}
		}
		
		$this->view->assign('sites', $sites);
	}

	/**
	 * Verify site
	 * 
	 * @see http://code.google.com/apis/webmastertools/docs/2.0/developers_guide_protocol.html#AD_Verifying
	 * @return void
	 */
	public function verifyAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$url 	  = $request->getPost('url');
			$url	  = urldecode($url);
			$verified = $request->getPost('verified');
			$method   = $request->getPost('verifyMethod');
			$success  = true;
			
			switch ($method) {
				/**
				 * Verify using meta tag
				 */
				case 'metatag':
					$metaTag = $request->getPost('metaTagVerify');
					
					$writer = new Zend_Config_Writer_Ini();
					
					/**
					 * Save meta tag to module configuration file
					 */
					$file   = TOMATO_APP_DIR . DS . 'modules' . DS . 'seo' . DS . 'config' . DS . 'config.ini';
					$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
					$config = $config->toArray();
					$config['gwebmaster']['verify_metatag'] = $metaTag;
					$writer->write($file, new Zend_Config($config));
					
					/**
					 * Registry plugin
					 */
					$file 	= TOMATO_APP_DIR . DS . 'config' . DS . 'application.ini';
					$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
					$config = $config->toArray();
					$config['production']['resources']['frontController']['plugins']['seoMetagVerify'] = 'Seo_Controllers_Plugin_MetatagVerification';
					$writer->write($file, new Zend_Config($config));
					
					break;
					
				/**
				 * Verify using HTML page
				 */
				case 'htmlpage':
					$file    = $request->getPost('htmlFileVerify');
					$content = 'google-site-verification: ' . $file;
					
					/**
				 	 * Create file
				 	 */
					$file = TOMATO_ROOT_DIR . DS . $file;
					$f = fopen($file, 'w');
					fwrite($f, $content);
					fclose($f);
					break;
			}
			
			/**
			 * Submit verify request
			 */
			try {
				$client = new Zend_Gdata_HttpClient();
				if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
					$client->setClientLoginToken($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN]);
				} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
					$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]);
				}
				
				$gdata  = new Zend_Gdata($client);
				$data   = '<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"'
							. ' xmlns:wt="http://schemas.google.com/webmasters/tools/2007">'
							//. '<atom:id>http://www.example.com/news/sitemap-index.xml</atom:id>
  							. "<atom:category scheme='http://schemas.google.com/g/2005#kind'"
  							. " term='http://schemas.google.com/webmasters/tools/2007#site-info'/>"
  							. '<wt:verification-method type="' . $method .'" in-use="true"/>'
  							. '</atom:entry>';
  				$response = $gdata->put($data, 'https://www.google.com/webmasters/tools/feeds/sites/' . urlencode($url), null, 'application/atom+xml');
			} catch (Exception $ex) {
				$success = false;
			}
			
			if ($success) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('gwebmaster_verify_success'));
			} else {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('gwebmaster_verify_fail'));
			}
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('url' => urlencode($url), 'verified' => ($success ? 'yes' : 'no')), 'seo_gwebmaster_details'));
		}
	}
}
