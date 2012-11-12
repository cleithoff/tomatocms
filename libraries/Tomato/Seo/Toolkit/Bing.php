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
 * @version 	$Id: Bing.php 3526 2010-07-10 16:37:07Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Seo_Toolkit_Bing extends Tomato_Seo_Toolkit_Abstract
{
	/**
	 * @see http://www.bing.com/developers/s/API%20Basics.pdf
	 * @const string
	 */
	const REQUEST_URI = 'http://api.search.live.net/json.aspx';
	
	public function getBackLinksCount()
	{
		$url = self::_buildUrl(array(
			'Appid'   => $this->_apiKey,
			'query'   => 'inbody:' . $this->_url,
			'sources' => 'web',
		));
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);		         
		if (!isset($results['SearchResponse']['Web']['Total'])) {
			return 0;
		}
		return $results['SearchResponse']['Web']['Total'];
	}
	
	public function getBackLinks($offset, $count)
	{
		$url = self::_buildUrl(array(
			'Appid' 	 => $this->_apiKey,
			'query' 	 => 'inbody:' . $this->_url,
			'sources'    => 'web',
			'Web.Count'  => $count,
			'Web.Offset' => $offset,	
		));
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);
		return self::_convertResults($results);
	}

	public function getIndexedPagesCount()
	{
		$url = self::_buildUrl(array(
			'Appid'   => $this->_apiKey,
			'query'   => 'site:' . $this->_url,
			'sources' => 'web',
		));
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);
		if (!isset($results['SearchResponse']['Web']['Total'])) {
			return 0;
		}
		return $results['SearchResponse']['Web']['Total'];
	}
	
	public function getIndexedPages($offset, $count)
	{
		$url = self::_buildUrl(array(
			'Appid' 	 => $this->_apiKey,
			'query' 	 => 'site:' . $this->_url,
			'sources'    => 'web',
			'Web.Count'  => $count,
			'Web.Offset' => $offset,	
		));
		$content = Tomato_Seo_Request::getResponse($url);
		$results = Zend_Json::decode($content);
		return self::_convertResults($results);
	}
	
	private static function _buildUrl($params) {
		$url = self::REQUEST_URI . '?' . http_build_query($params);
		return $url;
	}
	
	private static function _convertResults($results) {
		if (!isset($results['SearchResponse']['Web']['Results'])) {
			return array();
		}
		$links = array();
		foreach ($results['SearchResponse']['Web']['Results'] as $result) {
			$links[] = array(
				'title' 	  => $result['Title'],
				'description' => $result['Description'],
				'url' 		  => $result['Url'],
				'displayUrl'  => $result['DisplayUrl'],
				'cacheUrl' 	  => isset($result['CacheUrl']) ? $result['CacheUrl'] : null,
			);
		}
		
		return $links;	
	}
}
