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
 * @version 	$Id: Sitemap.php 3520 2010-07-10 11:29:01Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Seo_Sitemap
{
	/**
	 * Get sitemap items from file
	 * 
	 * @param string $file File name
	 * @return Array of sitemap items
	 */
	public static function getItems($file)
	{
		$items = array();
		if (file_exists($file)) {
			$xml = simplexml_load_file($file);
			foreach ($xml->url as $url) {	
				$items[] = new Tomato_Seo_Sitemap_Item(
					(string)$url->loc,
					(string)$url->changefreq,
					(string)$url->priority,
					(string)$url->lastmod
				);
			}
		}
		
		return $items;
	}
	
	/**
	 * Add sitemap item to file
	 *  
	 * @param string $file File name
	 * @param Tomato_Seo_Sitemap_Item $item Sitemap item
	 * @return boolean
	 */
	public static function addToSitemap($file, $item)
	{
		$items = self::getItems($file);
		$items[] = $item;
		
		return self::save($file, $items);
	}
	
	/**
	 * Remove sitemap item from file
	 *  
	 * @param string $file File name
	 * @param Tomato_Seo_Sitemap_Item $item Sitemap item
	 * @return boolean
	 */
	public static function removeFromSitemap($file, $item)
	{
		$items = self::getItems($file);
		$found = false;
		foreach ($items as $index => $value) {
			if ($value['loc'] = $item->getLoc()) {
				$found = true;
				unset($items[$index]);
			}
		}
		
		if ($found) {
			self::save($file, $items);
		}
		
		return true;
	}
	
	/**
	 * Save sitemap to file
	 *  
	 * @param string $file File name
	 * @param array $items Array of sitemap item
	 * @return boolean
	 */
	public static function save($file, $items)
	{
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
				. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL
				. '   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL
				. '   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . PHP_EOL
				. '   http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;
		foreach($items as $item) {
			$output .= $item->toString();
		}
		$output .= '</urlset>';		
		
		/**
		 * Write to file
		 */
		$f = fopen($file, 'w');
		fwrite($f, $output);
		fclose($f);
		
		return true;
	}	
}
