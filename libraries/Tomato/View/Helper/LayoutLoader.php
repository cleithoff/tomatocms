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
 * @version 	$Id: LayoutLoader.php 5376 2010-09-10 07:40:05Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_View_Helper_LayoutLoader extends Zend_View_Helper_Abstract 
{
	const CONTAINER_ID_PREFIX = 'container_';
	const CONTAINER_CLASS 	  = 'widget_container';
	const WIDGET_CLASS 		  = 'widget';
	
	/**
	 * Use 960 grid framework for layout with total of 12 columns
	 */
	const TOTAL_COLUMNS = 12;
	
	private static $_posToClass = array(
		'first' => 'alpha',
		'last'  => 'omega',
	); 
	
	/**
	 * Widget resources
	 * @var array
	 */
	private $_resources = array(
		'javascript' => array(), 
		'css' 		 => array(),
	);
	
	/**
	 * Inline Javascripts
	 * @var array
	 */
	private $_inlineScripts = array();
	
	/**
	 * This helper load the layout configuration file for current viewing URL
	 * From 2.0.7.1 version, the file will be loaded by current route
	 * 
	 * @param boolean $cache Save the output to cache file
	 * @return string
	 */
	public function layoutLoader($cache = false)
	{
		/**
		 * Get the name of current route
		 */
		$router    = Zend_Controller_Front::getInstance()->getRouter();
		$route     = $router->getCurrentRoute();
		
		/**
		 * In case we need to support paginator. 
		 * For example,
		 * /news/category/view/{categoryId}/
		 * and
		 * /news/category/view/{categoryId}/page-{pageIndex}
		 * are the same actions, but these URLs uses different routes. 
		 * 
		 * Therefore, instead of
		 * $routeName = $router->getCurrentRouteName();
		 * we can define the route name via module, controller and action name
		 * @since 2.0.8 
		 */

		/**
		 * Avoid the error when user use Zend_Controller_Router_Route_Chain
		 * @since 2.0.9
		 */	
		$defaults   = ($route instanceof Zend_Controller_Router_Route_Chain) 
						? $route->match(Zend_Controller_Front::getInstance()->getRequest()) 
						: $route->getDefaults();
		
		$routeName = $defaults['module'] . '_' . $defaults['controller'] . '_' . $defaults['action'];
		$routeName = strtolower($routeName);
		
		/**
		 * Get the parameters from request
		 * and maybe we have to use them inside widgets later
		 */
		$requestParams = array();
		if ($route instanceof Zend_Controller_Router_Route_Regex) {
			$map = $route->getVariables();
			foreach ($map as $name) {
				/**
				 * I wish I can get the mapped values 
				 * ($_values property from Zend_Controller_Router_Route_Regex instance)
				 * Currently, I get the parameter's value from request 
				 */
				$value = Zend_Controller_Front::getInstance()->getRequest()->getParam($name);
				$requestParams[$name] = $value;
				//$this->view->assign($name, $value);
			}
		}
		
		/**
		 * The file define the layout of route
		 */
		$file = Zend_Layout::getMvcInstance()->getLayoutPath() . DS . $routeName . '.xml';
		if (!file_exists($file)) {
			/**
			 * If the layout file does not exist, just show output of route action as normal
			 */
			return $this->view->layout()->content;
		}
		
		/**
		 * Process layout file
		 */
		ob_start();
		
		if ($cache) {
			$cacheFile = TOMATO_TEMP_DIR . DS . 'cache' . DS . 'layout_' . $routeName . '.php';
			if (file_exists($cacheFile)) {
				include $cacheFile;
				return ob_get_clean();
			}
			$f = fopen($cacheFile, 'w');
		}
		
		$reader = new XMLReader();
		$reader->open($file, 'UTF-8');
		
		$module 	= null;
		$widgetName = null;
		$load 		= null;
		
		$containerId 	   = 0;
		$widgetContainerId = 0;
		$tabId 			   = 0; 
		$tabContainerId    = 0;
		
		$params    = array();
		$params2   = array();
		$paramName = null;
		$from 	   = null;
		$search    = array('{APP_URL}', '{APP_STATIC_SERVER}');
		$replace   = array($this->view->baseUrl(), $this->view->APP_STATIC_SERVER);
		
		while ($reader->read()) {
			$str = $reader->nodeType . '_' . $reader->localName;
			switch ($str) {
				/* ========== Meet the open tag ============================= */
				
				case XMLReader::ELEMENT . '_layout':
					break;
					
				case XMLReader::ELEMENT . '_container':
					$containerId++;
					$widgetContainerId = 0;
					$cols = $reader->getAttribute('cols');
					if ($cols == null) {
						$class = '';
					} else {
						$class = (($position = $reader->getAttribute('position')) != null) 
									? 'grid_' . $cols . ' ' . self::$_posToClass[$position]
									: 'grid_' . $cols;
					}
								
					if ($cols == self::TOTAL_COLUMNS) {
						$class .= ' t_space_bottom';
					}
					$cssClass = $reader->getAttribute('cssClass');
					if (isset($cssClass)) {
						$class .= ' ' . $cssClass;
					}
					$str = '<div class="' . self::CONTAINER_CLASS . ' ' . $class . '" id="' . self::CONTAINER_ID_PREFIX . $containerId . '">'; 
					echo $str;
					
					/**
					 * Save to cache
					 */
					if ($cache) {
						fwrite($f, $str);
					}
					break;
					
				case XMLReader::ELEMENT . '_tabs':
					$tabContainerId++;
					$tabId = 0;
					echo '<div id="t_g_tab_container_' . $tabContainerId . '">';
					$xml = new SimpleXMLElement($reader->readOuterXml());
					echo '<ul>';
					for ($i = 0; $i < count($xml->tab); $i++) {
						$attrs = $xml->tab[$i]->attributes();
						echo '<li><a href="#t_g_tab_' . $tabContainerId . '_' . ($i + 1) . '"><span>' . (string)$attrs['label'] . '</span></a></li>';
					}
					echo '</ul>';
					echo '<script type="text/javascript">$(document).ready(function() { $("#t_g_tab_container_' . $tabContainerId . '").tabs(); });</script>';
					break;
					
				case XMLReader::ELEMENT . '_tab':
					$tabId++;
					echo '<div id="t_g_tab_' . $tabContainerId . '_' . $tabId . '">';
					break;
					
				case XMLReader::ELEMENT . '_defaultOutput':
					/**
					 * Render the script normally
					 */
					echo $this->view->layout()->content;
					
					/**
					 * Save to cache
					 */
					if ($cache) {
						fwrite($f, '<?php echo $this->view->layout()->content; ?>');
					}
					break;
					
				case XMLReader::ELEMENT . '_widget':
					$module     = $reader->getAttribute('module');
					$widgetName = $reader->getAttribute('name');
					$load       = $reader->getAttribute('load');
					if (!isset($load)) {
						$load = 'php';
					}
					
					$cssClass = $reader->getAttribute('cssClass');
					$cssClass = (!isset($cssClass)) ? '' : ' ' . $cssClass;
					
					$widgetContainerId++;
					$divId = self::CONTAINER_ID_PREFIX . $containerId . '_' . $widgetContainerId;
					$params['container']  = $divId;
					$params2['container'] = '"' . $divId . '"';
					$str = '<div class="' . self::WIDGET_CLASS . $cssClass . '" id="' . $divId . '">';
					echo $str;
					
					/**
					 * Save to cache
					 */
					if ($cache) {
						fwrite($f, $str);
					}
					break;
					
				case XMLReader::ELEMENT . '_params':
					break;
					
				case XMLReader::ELEMENT . '_param':
					$paramName = $reader->getAttribute('name');
					$from      = ($reader->getAttribute('from') == null) 
									? $paramName : $reader->getAttribute('from');
					if ($reader->getAttribute('type') == 'global' && isset($requestParams[$from])) {
						$params[$paramName]  = $requestParams[$from];
						$params2[$paramName] = '$this->view->' . $from;
					}
					break;
				
				case XMLReader::ELEMENT . '_cache':
					$params['___cacheLifetime'] = $params2['___cacheLifetime'] = $reader->getAttribute('lifetime');
					break;
					
				case XMLReader::CDATA:
					$paramValue = ($reader->value == null) 
								? $reader->readString() : $reader->value;
					if ($reader->getAttribute('type') != 'global' || !isset($requestParams[$from])) {
						$params[$paramName]  = $paramValue;
						$params2[$paramName] = '"' . addslashes($paramValue) . '"';
					}
					break;
					
				case XMLReader::ELEMENT . '_resources':
					break;
					
				case XMLReader::ELEMENT . '_resource':
					$resourceType = $reader->getAttribute('type');
					$src          = $reader->getAttribute('src');
					
					if (in_array($resourceType, array('javascript', 'css'))) {
						if (!in_array($src, $this->_resources[$resourceType])) {
							$this->_resources[$resourceType][] = $src;				
						}
						
						/**
						 * TODO: Load CSS at the head section by javascript
						 */
						if ($resourceType == 'css') {
							echo '<link rel="stylesheet" type="text/css" href="' . str_replace($search, $replace, $src) . '" />';
							//echo $this->view->headLink()->appendStylesheet(str_replace($search, $replace, $src));
						}
					} else {
						throw new Exception('Does not support ' . $resourceType . ' for type of resource');
					}
					break;
					
				/* ========== Meet the close tag ============================ */
				
				case XMLReader::END_ELEMENT . '_layout':
					break;
					
				case XMLReader::END_ELEMENT . '_container':
					echo '</div>';
					
					/**
					 * Save to cache
					 */
					if ($cache) {
						fwrite($f, '</div>');
					}
					break;
					
				case XMLReader::END_ELEMENT . '_tabs':
					echo '</div>';
					break;
					
				case XMLReader::END_ELEMENT . '_tab':
					echo '</div>';
					break;
					
				case XMLReader::END_ELEMENT . '_widget':
					if ($module != null) {
						if ('php' == $load) {
							echo $this->view->widget($module, $widgetName, $params);
							
							/**
							 * Save to cache
							 */
							if ($cache) {
								fwrite($f, '<?php echo $this->view->widget("' . $module . '", "' . $widgetName . '", ' . $this->_arrayToString($params2) . '); ?>');
							}
						} elseif ('ajax' == $load) {
							/**
							 * Load widget by ajax call
							 */
							$data = Zend_Json::encode($params);
							$id   = self::CONTAINER_ID_PREFIX . $containerId . '_' . $widgetContainerId;
							$this->_inlineScripts[] = "Tomato.Core.Widget.Loader.queue('$module', '$widgetName', '$data', '$id');"; 
						}
					}
					/**
					 * Reset variables
					 */
					$module 	  = null;
					$widgetName   = null;
					$load 		  = null;
					$paramName 	  = null;
					$from 		  = null;
					$params 	  = array();
					$params2 	  = array();
					
					echo '</div>';
					
					/**
					 * Save to cache
					 */
					if ($cache) {
						fwrite($f, '</div>');
					}
					break;
					
				case XMLReader::END_ELEMENT . '_params':
					break;
					
				case XMLReader::END_ELEMENT . '_param':
					break;
			}
		}
		$reader->close();
		
		/**
		 * Improve performance by placing the script section at the bottom of page
		 */
		foreach ($this->_resources['javascript'] as $resource) {
			$resource = str_replace($search, $replace, $resource);
			$this->view->headScript()->appendFile($resource);
			
			/**
			 * Save to cache
			 */
			if ($cache) {
				fwrite($f, $str);
			}
		}
		
		echo '<script type="text/javascript">';
		
		/**
		 * Save to cache
		 */
		if ($cache) {
			fwrite($f, '<script type="text/javascript">');
		}
		foreach ($this->_inlineScripts as $script) {
			$this->view->headScript()->appendScript($script);
			
			/**
			 * Save to cache
			 */
			if ($cache) {
				fwrite($f, $script);
			}
		}
		echo '</script>';
		
		/**
		 * Save to cache
		 */
		if ($cache) {
			fwrite($f, '</script>');
			fclose($f);
		}
		
		$return = ob_get_clean();
		return $return;
	}
	
	private function _arrayToString($param) 
	{
		$str = 'array(';
		foreach ($param as $key => $value) {
			$str .= '"' . $key . '" => ' . $value . ', ';
		}
		$str = substr($str, 0, -2) . ')';
		return $str;
	}
}
