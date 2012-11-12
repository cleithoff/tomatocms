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
 * @version 	$Id: Yahoo.php 3526 2010-07-10 16:37:07Z huuphuoc $
 * @since		2.0.7
 */

/**
 * Use Yahoo Site Explorer API:
 * http://developer.yahoo.com/search/siteexplorer/
 * 
 * The API limits 5,000 queries per IP address per day
 * http://developer.yahoo.com/search/rate.html
 * 
 * Follow the guides at http://developer.yahoo.com/wsregapp/ to get API key
 */
class Tomato_Seo_Toolkit_Yahoo extends Tomato_Seo_Toolkit_Abstract
{
	/**
	 * @const string
	 */
	const REQUEST_URI = 'http://search.yahooapis.com/SiteExplorerService/V1/';
	
	/**
	 * @see http://developer.yahoo.com/search/siteexplorer/V1/inlinkData.html
	 */
	public function getBackLinksCount()
	{
		$url = self::_buildUrl('inlinkData', array(
			'appid' 	  => $this->_apiKey,
			'query' 	  => $this->_url,
			'entire_site' => 1,
			'results' 	  => 1,
			'output' 	  => 'json',	
		));
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);		
		if (!isset($results['ResultSet']['totalResultsAvailable'])) {
			return 0;	
		}
		return $results['ResultSet']['totalResultsAvailable'];
	}

	/**
	 * @see http://developer.yahoo.com/search/siteexplorer/V1/inlinkData.html
	 */
	public function getBackLinks($offset, $count)
	{
		/**
		 * Note that with Yahoo Site Explorer, $offset has to be greater than or equal 1
		 */
		if (0 == $offset) {
			$offset = 1;
		}
		$url = self::_buildUrl('inlinkData', array(
			'appid'   => $this->_apiKey,
			'query'   => $this->_url,
			'start'   => $offset,
			'results' => $count,
			'output'  => 'json',	
		));
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);
		return self::_convertResults($results);
	}

	/**
	 * @see http://developer.yahoo.com/search/siteexplorer/V1/pageData.html
	 */
	public function getIndexedPagesCount()
	{
	 	$url = self::_buildUrl('pageData', array(
			'appid'  => $this->_apiKey,
			'query'  => $this->_url,
	 		'output' => 'json',
		));	
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);
		if (!isset($results['ResultSet']['totalResultsAvailable'])) {
			return 0;	
		}
		return $results['ResultSet']['totalResultsAvailable'];
	}
	
	/**
	 * @see http://developer.yahoo.com/search/siteexplorer/V1/pageData.html
	 */
	public function getIndexedPages($offset, $count)
	{
		/**
		 * Note that with Yahoo Site Explorer, $offset has to be greater than or equal 1
		 */
		if (0 == $offset) {
			$offset = 1;
		}		  
		$url = self::_buildUrl('pageData', array(
			'appid'   => $this->_apiKey,
			'query'   => $this->_url,
			'start'   => $offset,
			'results' => $count,
			'output'  => 'json',	
		));  
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);
		return self::_convertResults($results);
	}

	private static function _buildUrl($typeData, $params) {
		$url = self::REQUEST_URI . $typeData . '?' . http_build_query($params);
		return $url;
	}
	
	private static function _convertResults($results) {
		if (!isset($results['ResultSet']['Result'])) {
			return array();
		}
		$links = array();
		foreach ($results['ResultSet']['Result'] as $result) {
			$links[] = array(
				'title' 	  => $result['Title'],
				'description' => null,
				'url' 		  => $result['Url'],
				'displayUrl'  => $result['ClickUrl'],
				'cacheUrl' 	  => null,
			);
		}
		
		return $links;					
	}
}
