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
 * @version 	$Id: GanalyticController.php 5453 2010-09-16 08:37:58Z huuphuoc $
 * @since		2.0.7
 */

class Seo_GanalyticController extends Zend_Controller_Action
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
	 * Do NOT change this value
	 * 
	 * @see http://code.google.com/apis/analytics/docs/gdata/gdataAuthentication.html#understandingAuthSub
	 * @const string
	 */
	const GOOGLE_ANALYTIC_SCOPE = 'https://www.google.com/analytics/feeds/';
	
	/**
	 * Google Analytics service name. Will be used for client login method
	 * Do NOT change this value
	 * 
	 * @see http://code.google.com/apis/analytics/docs/gdata/gdataAuthentication.html#understandingClientLogin
	 * @const string
	 */
	const GOOGLE_ANALYTICS_SERVICE = 'analytics';
	
	/**
	 * Do NOT change this value
	 * 
	 * @const string
	 */
	const GOOGLE_ANALYTIC_DIMENSION_NOT_SET = '(not set)';
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Show the link to user for making authentication using Google account
	 * 
	 * @return void
	 */
	public function indexAction()
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
				
				$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, self::GOOGLE_ANALYTICS_SERVICE);
				$_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] = $client->getClientLoginToken();
			} else {
				if (isset($_GET['token'])) {
					$sessionToken = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
	        		$_SESSION[self::GOOGLE_AUTH_SUB_TOKEN] = $sessionToken;
				} else {
					$next = $this->view->serverUrl() . $this->view->url(array(), 'seo_ganalytic');
					$this->_redirect(Zend_Gdata_AuthSub::getAuthSubTokenUri($next, self::GOOGLE_ANALYTIC_SCOPE, 0, 1));
					exit();
				}
			}
		}
		
		$request = $this->getRequest();
		$act 	 = $request->getParam('act', 'feed');
		$client  = new Zend_Gdata_HttpClient();
		$client->setMethod(Zend_Http_Client::GET);
		
		if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			$client->setHeaders('authorization', 'GoogleLogin auth="' . $_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] . '"');
		} elseif (isset($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN])) {
			//$client = Zend_Gdata_AuthSub::getHttpClient($_SESSION[self::GOOGLE_AUTH_SUB_TOKEN]);
			$client->setHeaders('authorization', 'AuthSub token="' . $_SESSION[self::GOOGLE_AUTH_SUB_TOKEN] . '"');
		}
		
		switch ($act) {
			/**
			 * Show the list of sites
			 * @see http://code.google.com/apis/analytics/docs/gdata/gdataReferenceAccountFeed.html
			 */
			case 'feed':
				$client->setUri('https://www.google.com/analytics/feeds/accounts/default');
				$client->setParameterGet('start-index', 1);
				$client->setParameterGet('max-results', 50);
				$client->setParameterGet('v', 2);
				$client->setParameterGet('alt', 'json');
				$client->setParameterGet('prettyprint', 'true');

				$response = $client->request()->getBody();
				$response = Zend_Json::decode($response);
				
				$sites = array();
				
				if (isset($response['feed']['entry'])) {
					foreach ($response['feed']['entry'] as $item) {
						$sites[] = array(
							'title'   => $item['title']['$t'],
							'tableId' => $item['dxp$tableId']['$t'],
						);
					}
				}
				$this->view->assign('sites', $sites);
				break;
				
			/**
			 * Report
			 * @see http://code.google.com/apis/analytics/docs/gdata/gdataReferenceDataFeed.html
			 */
			case 'report':
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				
				$numDays   = $request->getParam('days');
				$tableId   = $request->getParam('tableId');
				$endDate   = date('Y-m-d');
				$startDate = date('Y-m-d', time() - $numDays * 24 * 60 * 60);
				
				$client->setUri('https://www.google.com/analytics/feeds/data');
				
				$params = array(
					'alt' 		  => 'json',
					'v' 		  => 2,
					'prettyprint' => 'true',
					'ids' 		  => $tableId,
					'start-date'  => $startDate,
					'end-date' 	  => $endDate,
					'start-index' => 1,
				);
				
				/**
				 * The response data
				 */
				$return = array(
					'visits'     	   => array(),
					'visitors'   	   => array(),
					'pageviews'  	   => array(),
					'timeOnSite' 	   => array(),
					'bounces'    	   => array(),
					'browser'    	   => array(),
					'operatingSystem'  => array(),
					'screenResolution' => array(),
					'source' 		   => array(),
					'keyword' 		   => array(),
					'pagePath'		   => array(),
					'exitPagePath'	   => array(),
				);

				/**
				 * Report by visitors
				 * 
				 * List of dimensions:
				 * - Date of visit: ga:date
				 * 
				 * List of metrics:
				 * - Number of visits: ga:visits
				 * - Number of unique visitors: ga:visitors
				 * - Number of page views: ga:pageviews
				 * - Time on site: ga:timeOnSite
				 * - Bounce rate: ga:bounces
				 * 
				 * @see http://code.google.com/apis/analytics/docs/gdata/gdataReferenceDimensionsMetrics.html
				 */
				$client->resetParameters();
				foreach ($params as $name => $value) {
					$client->setParameterGet($name, $value);
				}
				$client->setParameterGet('dimensions', 'ga:date');
				$client->setParameterGet('metrics', 'ga:visitors,ga:visits,ga:pageviews,ga:timeOnSite,ga:bounces,ga:entrances');
				$client->setParameterGet('sort', 'ga:date');
				$response = Zend_Json::decode($client->request()->getBody());
				
				$return['visitors']['total']   = $response['feed']['dxp$aggregates']['dxp$metric'][0]['value'];
				$return['visits']['total'] 	   = $response['feed']['dxp$aggregates']['dxp$metric'][1]['value'];
				$return['pageviews']['total']  = $response['feed']['dxp$aggregates']['dxp$metric'][2]['value'];
				$return['timeOnSite']['total'] = $response['feed']['dxp$aggregates']['dxp$metric'][3]['value'];
				$return['bounces']['total']    = $response['feed']['dxp$aggregates']['dxp$metric'][4]['value'];
				
				foreach ($response['feed']['entry'] as $item) {
					$return['visitors']['entry'][] = array(
						'dimension' => $item['dxp$dimension'][0]['value'], 
						'metric' 	=> $item['dxp$metric'][0]['value'],
					);
					$return['visits']['entry'][] = array(
						'dimension' => $item['dxp$dimension'][0]['value'], 
						'metric' 	=> $item['dxp$metric'][1]['value'],
					);
					$return['pageviews']['entry'][] = array(
						'dimension' => $item['dxp$dimension'][0]['value'], 
						'metric' 	=> $item['dxp$metric'][2]['value'],
					);
					$return['timeOnSite']['entry'][] = array(
						'dimension' => $item['dxp$dimension'][0]['value'], 
						'metric' 	=> (0 == $item['dxp$metric'][1]['value']) ? 0 : $item['dxp$metric'][3]['value'] / $item['dxp$metric'][1]['value'],
					);
					$return['bounces']['entry'][] = array(
						'dimension' => $item['dxp$dimension'][0]['value'], 
						'metric' 	=> (0 == $item['dxp$metric'][5]['value']) ? 0 : number_format(round($item['dxp$metric'][4]['value'] / $item['dxp$metric'][5]['value'] * 100, 2), 2),
					);
				}
				
				/**
				 * Get number of visits based on following dimensions:
				 * 
				 * Traffic source:
				 * - Name of browser: ga:browser
				 * - Operating system: ga:operatingSystem
				 * - Screen resolution: ga:screenResolution
				 * - Traffic source: ga:source
				 * - Keyword: ga:keyword
				 * 
				 * Content:
				 * - Page path: ga:pagePath
				 * - Exit page path: ga:exitPagePath
				 */
				$queries = array(
					array('dimension' => 'browser', 'metrics' => 'ga:visits', 'sort' => '-ga:visits', 'max-results' => null),
					array('dimension' => 'operatingSystem', 'metrics' => 'ga:visits', 'sort' => '-ga:visits', 'max-results' => null),
					array('dimension' => 'screenResolution', 'metrics' => 'ga:visits', 'sort' => '-ga:visits', 'max-results' => null),
					array('dimension' => 'source', 'metrics' => 'ga:visits', 'sort' => '-ga:visits', 'max-results' => 20),
					array('dimension' => 'keyword', 'metrics' => 'ga:visits', 'sort' => '-ga:visits', 'max-results' => 20),
					array('dimension' => 'pagePath', 'metrics' => 'ga:pageviews', 'sort' => '-ga:pageviews', 'max-results' => 20),
					array('dimension' => 'exitPagePath', 'metrics' => 'ga:exits', 'sort' => '-ga:exits', 'max-results' => 20),
				);
				foreach ($queries as $query) {
					$client->resetParameters();
					foreach ($params as $name => $value) {
						$client->setParameterGet($name, $value);
					}
					$client->setParameterGet('dimensions', 'ga:' . $query['dimension']);
					$client->setParameterGet('metrics', $query['metrics']);
					$client->setParameterGet('sort', $query['sort']);
					
					if ($query['max-results'] != null) {
						$client->setParameterGet('max-results', $query['max-results']);
					}
					
					$response = Zend_Json::decode($client->request()->getBody());
					
					$return[$query['dimension']]['total'] = $response['feed']['dxp$aggregates']['dxp$metric'][0]['value'];
					foreach ($response['feed']['entry'] as $item) {
						if ($item['dxp$dimension'][0]['value'] != self::GOOGLE_ANALYTIC_DIMENSION_NOT_SET) {
							$return[$query['dimension']]['entry'][] = array(
								'dimension' => $item['dxp$dimension'][0]['value'],
								'metric' 	=> $item['dxp$metric'][0]['value'],
							);
						}
					}
				}
				$this->getResponse()->setBody(Zend_Json::encode($return));
				break;
		}
	}
}
