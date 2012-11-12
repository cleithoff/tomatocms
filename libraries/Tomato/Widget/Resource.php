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
 * @version 	$Id: Resource.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Widget_Resource 
{
	public static function getResources($module, $widget) 
	{
		$ret  = array('javascript' => array(), 'css' => array());
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $widget . DS . 'about.xml';
		if (!file_exists($file)) {
			return $ret;
		}
		$config = simplexml_load_file($file);
		if (($resources = $config->resources)) {
			if ($resources = $resources->resource) {
				foreach ($resources as $res) {
					$attr = $res->attributes();
					$ret[(string)$attr['type']][] = (string)$attr['src'];
				}
			}
		}
		return $ret;
	}
}
