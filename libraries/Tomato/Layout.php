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
 * @version 	$Id: Layout.php 5193 2010-08-30 15:01:09Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Layout 
{
	/**
	 * The doctype for layout file
	 * @var string
	 */
	const LAYOUT_DOCTYPE = '<!DOCTYPE layout SYSTEM "http://schemas.tomatocms.com/dtd/layout.dtd">';
	
	const WIDGET_JS_CLASS				 = 'Tomato.Core.Layout.Widget';
	const WIDGET_DEFAULT_OUTPUT_JS_CLASS = 'Tomato.Core.Layout.DefaultOutput';
	
	/**
	 * End of line
	 * @var string
	 */
	const EOL = "\n";
	
	/**
	 * Save layout config to XML file
	 * 
	 * @param string $file The file name
	 * @param array $layout
	 */
	public static function save($file, $layout) 
	{
		/**
		 * TODO: Should we use XMLElement to write the output file
		 * to avoid from generating not well-form XML
		 */
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . self::EOL
				. self::LAYOUT_DOCTYPE . self::EOL
				. '<layout>' . self::EOL
				. self::_saveContainer($layout)
				. '</layout>';
		
		/**
		 * Write to file
		 */
		$f = fopen($file, 'w');
		fwrite($f, $output);
		fclose($f);
	}
	
	private static function _saveContainer($container) 
	{
		$isRoot = ($container['isRoot'] == 1);
		
		/**
		 * Add the depth properties for generating more beautiful XML file
		 * @since 2.0.7
		 */
		$depth  = ($container['isRoot'] == 1) ? 0 : $container['depth'];
		
		$output = '';
		$pos = isset($container['position']) ? $container['position'] : '';
//		if (!$isRoot && $pos == 'first') {
//			$output .= '<container cols="12">';
//		}
		
		$pos2 = isset($container['position']) ? ' position="' . $container['position'] . '"' : '';
		if (!$isRoot) {
			$output .= str_repeat('    ', $depth) . '<container cols="' . $container['cols'] . '"' . $pos2 . '>' . self::EOL;
		}
		
		/**
		 * Output child containers
		 */
		foreach ($container['containers'] as $index => $childContainer) {
			$childContainer['depth'] = $depth + 1;
			$output .= self::_saveContainer($childContainer);
		}
		
		/**
		 * Output widgets
		 */
		foreach ($container['widgets'] as $index => $widget) {
			$widget['container'] = $container;
			$output .= self::_saveWidget($widget);
		}
		if (!$isRoot) {
			$output .= str_repeat('    ', $depth) . '</container>' . self::EOL;
		}
		
//		if (!$isRoot && $pos == 'last') {
//			$output .= '</container>';
//		}
		
		return $output;
	}
	
	private static function _saveWidget($widget) 
	{
		$depth   = $widget['container']['depth'];
		$indent = str_repeat('    ', $depth + 1);
		
		if ($widget['cls'] == self::WIDGET_DEFAULT_OUTPUT_JS_CLASS) {
			return $indent . '<defaultOutput />' . self::EOL;
		}
		
		$load = 'php';
		if (count($widget['params']) > 0 
			&& isset($widget['params'][Tomato_Widget::PARAM_LOAD_AJAX]) 
			&& $widget['params'][Tomato_Widget::PARAM_LOAD_AJAX]['value'] != '') 
		{
			$load = 'ajax';
		}
		unset($widget['params'][Tomato_Widget::PARAM_LOAD_AJAX]);
		
		$output = $indent . '<widget module="' . $widget['module'] . '" name="' . $widget['name'] . '" load="' . $load . '">' . self::EOL;
		
		/**
		 * Output title
		 */
		if ($widget['title']) {
			$output .= $indent . '    <title><![CDATA[' . $widget['title'] . ']]></title>' . self::EOL;	
		}
		
		/**
		 * Output resources
		 */
		if (count($widget['resources']['css']) > 0 || count($widget['resources']['javascript']) > 0) {
			$output .= $indent . '    <resources>' . self::EOL;
			foreach ($widget['resources']['css'] as $index => $css) {
				$output .= $indent . '        <resource type="css" src="' . $css . '" />' . self::EOL;
			}
			foreach ($widget['resources']['javascript'] as $index => $js) {
				if (is_array($js)) {
					if (!array_key_exists('file', $js)) {
						continue;	
					}
					$js = $js['file'];
				}
				$output .= $indent . '        <resource type="javascript" src="' . $js . '" />' . self::EOL;
			}
			$output .= $indent . '    </resources>' . self::EOL;
		}
		
		/**
		 * Output params
		 */
		$lifetime = null;
		if (isset($widget['params'][Tomato_Widget::PARAM_CACHE_LIFETIME])) {
			$lifetime = $widget['params'][Tomato_Widget::PARAM_CACHE_LIFETIME]['value'];
			$lifetime = ltrim($lifetime, ' ');
			$lifetime = rtrim($lifetime, ' ');
			
			unset($widget['params'][Tomato_Widget::PARAM_CACHE_LIFETIME]);
		}
		
		if (count($widget['params']) > 0) {
			$output .= $indent . '    <params>' . self::EOL;
			foreach ($widget['params'] as $param => $data) {
				if ($data['type'] == 'global') {
					$output .= $indent . '        <param name="' . $param . '" type="global" />' . self::EOL;
				} else {
					/**
					 * Use CDATA to store the value of param
					 */
					$value   = ltrim($data['value'], ' ');
					$value   = rtrim($value, ' ');
					$output .= $indent . '        <param name="' . $param . '"><value><![CDATA[' . $value . ']]></value></param>' . self::EOL;
				}
			}
			$output .= $indent . '    </params>' . self::EOL;
		}
		
		if ($lifetime && $lifetime != '') {
			$output .= $indent . '    <cache lifetime="' . $lifetime . '" />' . self::EOL;
		}
		
		$output .= $indent . '</widget>' . self::EOL;
		return $output;
	}
	
	/**
	 * Load layout from XML file
	 * 
	 * @param string $file
	 * @return array
	 */
	public static function load($file) 
	{
		$xml 	= simplexml_load_file($file);
		$array 	= self::_loadContainer($xml);
		$return = array(
			'isRoot' 	 => 1,
			'cols' 		 => 12,
			'containers' => $array['containers'],
			'widgets' 	 => $array['widgets'],
		);
		return $return;
	}
	
	private static function _loadContainer($containerNode) 
	{
		$return = array(
			'containers' => null,
			'widgets' 	 => null,
		);
		if (null == $containerNode) {
			return $return;
		}
		$attrs = $containerNode->attributes();
		$return = array(
			'isRoot' 	 => 0,
			'cols' 		 => (string) $attrs['cols'],
			'containers' => array(),
			'widgets' 	 => array(),
		);
		if (($pos = (string) $attrs['position'])) {
			$return['position'] = $pos;
		}
		foreach ($containerNode->container as $node) {
			$return['containers'][] = self::_loadContainer($node);
		}
		if ($containerNode->defaultOutput) {
			$return['widgets'][] = array(
				'cls' 		=> self::WIDGET_DEFAULT_OUTPUT_JS_CLASS,
				'module' 	=> null,
				'name' 		=> null,
				'title' 	=> null,
				'resources' => null,
				'params' 	=> null,
			);
		}
		foreach ($containerNode->widget as $node) {
			$return['widgets'][] = self::_loadWidget($node);
		}
		return $return;
	}
	
	private static function _loadWidget($widgetNode) 
	{
		if (null == $widgetNode) {
			return array();
		}
		$attrs 	= $widgetNode->attributes();
		$title 	= isset($widgetNode->title) ? (string)$widgetNode->title : '';
		$return = array(
			'cls' 		=> self::WIDGET_JS_CLASS,
			'module' 	=> (string) $attrs['module'],
			'name' 		=> (string) $attrs['name'],
			'title' 	=> $title,
			'resources' => array(),
			'params' 	=> array(),
		);
		
		/**
		 * Load method
		 */
		if (isset($attrs['load']) && ((string)$attrs['load'] == 'ajax')) {
			$return['params'][Tomato_Widget::PARAM_LOAD_AJAX] = array('value' => true);
		}
		
		if ($widgetNode->resources) {
			foreach ($widgetNode->resources->resource as $resource) {
				$attrs = $resource->attributes();
				$type  = (string) $attrs['type'];
				$src   = (string) $attrs['src'];
				if (!isset($return['resources'][$type])) {
					$return['resources'][$type] = array();
				}
				$return['resources'][$type][] = $src;
			}
		}
		if ($widgetNode->params) {
			foreach ($widgetNode->params->param as $param) {
				$attrs = $param->attributes();
				$name  = (string) $attrs['name'];
				$return['params'][$name] = array(
					'value' => (string) $param->value,
					'type'  => isset($attrs['type']) ? 'global' : '',
				);
			}
		}
		
		/**
		 * Cache setting
		 */
		if ($widgetNode->cache) {
			$attrs = $widgetNode->cache->attributes();
			$return['params'][Tomato_Widget::PARAM_CACHE_LIFETIME] = array('value' => (string)$attrs['lifetime']);
		}
		
		return $return;
	}
}
