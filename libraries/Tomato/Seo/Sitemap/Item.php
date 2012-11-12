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
 * @version 	$Id: Item.php 3517 2010-07-10 10:59:19Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Seo_Sitemap_Item
{
	/**
	 * Item URL
	 * 
	 * @var string
	 */
	private $_loc;

	/**
     * Item change frequency
     * Can take one of following values:
     * always, hourly, daily, monthy, yearly, never
     * 
     * @var string
	 */
	private $_frequency;
	
	/**
	 * Item priority
	 * Can take one of following values:
	 * 0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1
	 * Default value is 0.5
	 * 
	 * @var string
	 */
	private $_priority = 0.5;
	
	/**
	 * Item's last modification
	 */
	private $_lastmod;
	
	public function __construct($loc, $frequency = 'hourly', $priority = 0.5, $lastmod = null)
	{
		$this->_loc 	  = $loc;
		$this->_frequency = $frequency;
		$this->_priority  = $priority;
		$this->_lastmod   = $lastmod;	
	}
	
	/**
	 * Get item's location
	 * 
	 * @return string
	 */
	public function getLoc()
	{
		return $this->_loc;
	}
	
	/**
	 * Get item frequency
	 * 
	 * @return string
	 */
	public function getFrequency()
	{
		return $this->_frequency;
	}
	
	/**
	 * Get item priority
	 * 
	 * @return string
	 */
	public function getPriority()
	{
		return $this->_priority;
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		$tab       = '    ';//chr(9);
		$endOfLine = PHP_EOL;
		
		$return = $tab . '<url>' . $endOfLine
				. $tab . $tab . '<loc>' . $this->_loc . '</loc>' . $endOfLine
				. $tab . $tab . '<priority>' . $this->_priority . '</priority>' . $endOfLine
				. $tab . $tab . '<changefreq>' . $this->_frequency . '</changefreq>' . $endOfLine;
		if ($this->_lastmod != null) {
			$return .= $tab . $tab . '<lastmod>' . $this->_lastmod . '</lastmod>' . $endOfLine;
		}
		$return .= $tab . '</url>' . $endOfLine;
		
		return $return;
	}
}
