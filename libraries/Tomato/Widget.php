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
 * @version 	$Id: Widget.php 4636 2010-08-15 09:52:47Z huuphuoc $
 * @since		2.0.0
 */

abstract class Tomato_Widget 
{
	/**
	 * @const string
	 * @since 2.0.3
	 */
	const CURRENT_WIDGET_KEY = 'Tomato_Widget_CurrentWidgetKey';

	/**
	 * Special parameters
	 */
	const PARAM_CACHE_LIFETIME = '___cacheLifetime';
	const PARAM_LOAD_AJAX	   = '___loadAjax';
	const PARAM_PREVIEW_MODE   = '___widgetPreviewMode'; 
	
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;
	
	/**
	 * @var Zend_Controller_Response_Abstract
	 */
	protected $_response;
	
	/**
	 * @var Zend_View_Abstract
	 */
	protected $_view;

	/**
	 * Name of module that widget belong to
	 * @var string
	 */
	protected $_module;
	
	/**
	 * Name of widget
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Path of view helpers
	 * @var array
	 */
	protected $_helperPaths;
	
	public function __construct($module, $name) 
	{
		$this->_module = strtolower($module);
		$this->_name   = strtolower($name);
		
		$front 	  = Zend_Controller_Front::getInstance();
		$request  = $front->getRequest();
		$response = $front->getResponse();
		
		$this->_request  = clone $request;
		$this->_response = clone $response;
		$viewRenderer 	 = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$this->_view 	 = clone $viewRenderer->view;
		
		$this->_helperPaths = $this->_view->getHelperPaths();
		
		$this->_view->addHelperPath(TOMATO_APP_DIR . DS . 'modules' . DS . $this->_module . DS . 'views' . DS . 'helpers', 
									$this->_module.'_View_Helper_');
		$this->_view->addHelperPath(TOMATO_APP_DIR . DS . 'modules' . DS . $this->_module . DS . 'widgets' . DS . $this->_name, 
									$this->_module.'_widgets_'.$this->_name.'_');
	}
	
	/**
	 * Get name of widget
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Get name of module that widget belong to
	 * 
	 * @return string
	 */
	public function getModule()
	{
		return $this->_module;
	}
	
	private function _reset() 
	{
		$params = $this->_request->getUserParams(); 
        foreach (array_keys($params) as $key) { 
            $this->_request->setParam($key, null); 
        } 
 
        $this->_response->clearBody();
        $this->_response->clearHeaders()->clearRawHeaders();
	}

	public function __call($name, $arguments) 
	{
		$this->_reset();
		
		if ($arguments != null && is_array($arguments) && count($arguments) > 0) {
			if ($arguments[0] != null && is_array($arguments[0])) {
				$this->_request->setParams($arguments[0]);
			}
		}
		
		/**
		 * We can get widget instance later
		 * @since 2.0.3
		 */
		Zend_Registry::set(self::CURRENT_WIDGET_KEY, $this);
		
		/**
		 * Prepare data
		 */
		$prepare = '_prepare' . ucfirst($name);
		if (method_exists($this, $prepare)) {
			$this->$prepare();
		}
		$name 	  = strtolower($name);
		$template = Zend_Registry::get(Tomato_GlobalKey::APP_TEMPLATE);
		$path1 	  = TOMATO_APP_DIR . DS . 'modules' . DS . $this->_module . DS . 'widgets' . DS . $this->_name;
		$path2 	  = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'views' . DS . $this->_module . DS . 'widgets' . DS . $this->_name;
		if (file_exists($path2 . DS . $name . '.phtml')) {
			$this->_view->addScriptPath($path2);
		} else {
			$this->_view->addScriptPath($path1);
		}
		
		$file = $this->_view->getScriptPath(null) . $name . '.phtml';
		if ($file != null && file_exists($file)) {
			$content = $this->_view->render($name . '.phtml');
			$this->_response->appendBody($content);
		}
		$body = $this->_response->getBody();
		$this->_reset();
		
		//Zend_Registry::set(self::CURRENT_WIDGET_KEY, null);

		/**
		 * Helpers for widgets have the same name ("helper"), hence in order to 
		 * the next widget call exactly its helper, we have to reset the helper paths
		 */
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view; 
		$view->setHelperPath(null);
		if ($this->_helperPaths) {
			foreach ($this->_helperPaths as $prefix => $paths) {
				foreach ($paths as $path) {
					$view->addHelperPath($path, $prefix);
				}
			}
		}
		
		return $body;
	}
	
	abstract protected function _prepareShow();
	
	protected function _prepareConfig() 
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			return;
		}
	}
}
