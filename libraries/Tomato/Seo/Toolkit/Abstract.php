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
 * @version 	$Id: Abstract.php 3546 2010-07-11 07:42:17Z huuphuoc $
 * @since		2.0.7
 */

abstract class Tomato_Seo_Toolkit_Abstract
{
	/**
	 * The API key
	 * @var string
	 */
	protected $_apiKey;
	
	/**
	 * The URL
	 * @var string
	 */
	protected $_url;
	
	/**
	 * The connection timeout
	 * @var int
	 */
	protected $_timeout = 10;

	/**
	 * Set API key
	 * 
	 * @param string $apiKey
	 * @return Tomato_Seo_Toolkit_Abstract
	 */
	public function setApiKey($apiKey)
	{
		$this->_apiKey = $apiKey;
		return $this;
	}
	
	/**
	 * Set URL
	 * 
	 * @param string $url
	 * @return Tomato_Seo_Toolkit_Abstract
	 */
	public function setUrl($url)
	{
		$this->_url	= $url;
		return $this;
	}
	
	/**
	 * Get number of back links
	 * 
	 * @return int
	 */
	abstract public function getBackLinksCount();
	
	/**
	 * Get back links
	 * 
	 * @param int $offset
	 * @param int $count
	 * @return array Array of back links
	 */
	abstract public function getBackLinks($offset, $count);

	/**
	 * Get number of indexed pages
	 * 
	 * @return int
	 */
	abstract public function getIndexedPagesCount();
	
	/**
	 * Get list of indexed pages
	 * Each page has following properties:
	 * - title
	 * - description
	 * - url
	 * - displayUrl
	 * - cacheUrl
	 * 
	 * @param int $offset
	 * @param int $count
	 * @return array Array of indexed pages
	 */
	abstract public function getIndexedPages($offset, $count);
}
