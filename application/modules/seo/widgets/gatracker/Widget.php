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
 * @version 	$Id: Widget.php 5389 2010-09-12 05:15:44Z huuphuoc $
 * @since		2.0.9
 */

class Seo_Widgets_GaTracker_Widget extends Tomato_Widget
{
	/**
	 * @const string
	 */
	const GOOGLE_CLIENT_LOGIN_TOKEN = 'GOOGLE_CLIENT_LOGIN_TOKEN';
	
	/**
	 * Google Analytics service name.
	 * Do NOT change this value
	 * 
	 * @see http://code.google.com/apis/analytics/docs/gdata/gdataAuthentication.html#understandingClientLogin
	 * @const string
	 */
	const GOOGLE_ANALYTICS_SERVICE = 'analytics';
	
	private function _authenticate()
	{
		if (!isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			$config = Tomato_Module_Config::getConfig('seo');
			if (isset($config->google->username) && isset($config->google->password)) {
				$username = $config->google->username;
				$password = $config->google->password;
				
				$client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, self::GOOGLE_ANALYTICS_SERVICE);
				$_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] = $client->getClientLoginToken();
			}
		}
	}
	
	protected function _prepareShow()
	{
		$this->_authenticate();
		
		//$numDays = $this->_request->getParam('days', 1);
		$numDays   = 1;
		$tableId   = $this->_request->getParam('table_id');
		$endDate   = date('Y-m-d');
		$startDate = date('Y-m-d', time() - $numDays * 24 * 60 * 60);
		
		$client = new Zend_Gdata_HttpClient();
		$client->setMethod(Zend_Http_Client::GET);
		
		if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			$client->setHeaders('authorization', 'GoogleLogin auth="' . $_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] . '"');
		}
		
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
			'visits'    => array(),
			'visitors'  => array(),
			'pageviews' => array(),
		);

		$client->resetParameters();
		foreach ($params as $name => $value) {
			$client->setParameterGet($name, $value);
		}
		$client->setParameterGet('dimensions', 'ga:date')
				->setParameterGet('metrics', 'ga:visitors,ga:visits,ga:pageviews')
				->setParameterGet('sort', 'ga:date');
		$response = Zend_Json::decode($client->request()->getBody());
		
		$return['visitors']  = $response['feed']['dxp$aggregates']['dxp$metric'][0]['value'];
		$return['visits']    = $response['feed']['dxp$aggregates']['dxp$metric'][1]['value'];
		$return['pageviews'] = $response['feed']['dxp$aggregates']['dxp$metric'][2]['value'];
		
		$this->_view->assign('counter', $return);
	}
	
	protected function _prepareConfig()
	{
		$this->_authenticate();
		
		/**
		 * Show the list of sites
		 * @see http://code.google.com/apis/analytics/docs/gdata/gdataReferenceAccountFeed.html
		 */
		$client = new Zend_Gdata_HttpClient();
		$client->setMethod(Zend_Http_Client::GET);
		
		if (isset($_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN])) {
			$client->setHeaders('authorization', 'GoogleLogin auth="' . $_SESSION[self::GOOGLE_CLIENT_LOGIN_TOKEN] . '"');
		}
		$client->setUri('https://www.google.com/analytics/feeds/accounts/default')
				->setParameterGet('start-index', 1)
				->setParameterGet('max-results', 50)
				->setParameterGet('v', 2)
				->setParameterGet('alt', 'json')
				->setParameterGet('prettyprint', 'true');

		$response = $client->request()->getBody();
		$response = Zend_Json::decode($response);
		
		$sites = array();
		foreach ($response['feed']['entry'] as $item) {
			$sites[] = array(
				'title'   => $item['title']['$t'],
				'tableId' => $item['dxp$tableId']['$t'],
			);
		}
		$this->_view->assign('sites', $sites);
	}
}
