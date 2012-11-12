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
 * @version 	$Id: PlaceHolder.php 4392 2010-08-08 13:13:31Z huuphuoc $
 * @since		2.0.8
 */

class Tomato_Controller_Plugin_PlaceHolder extends Zend_Controller_Plugin_Abstract 
{
	const HEAD_TOP    = 'top';
	const HEAD_BOTTOM = 'bottom';
	
	/**
	 * Name of container
	 * @var string
	 */
	private $_name;
	
	/**
	 * The position that content of container will be shown
	 * @var string
	 */
	private $_position;
	
	public function __construct($name, $position = self::HEAD_BOTTOM)
	{
		$this->_name     = $name;
		$this->_position = $position;
	}
	
	public function dispatchLoopShutdown()
	{
		if ($this->getRequest()->isXmlHttpRequest()) {
			return;
		}
		
		/**
		 * Get the view instance
		 */
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null == $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		
		/**
		 * Get content captured by place holder
		 */
		$script = $view->placeholder($this->_name);
		
		/**
		 * Put the content into head section
		 */
		$response = $this->getResponse();
		$body     = $response->getBody();
		
		switch ($this->_position) {
			case self::HEAD_TOP:
				$response->setBody(preg_replace('/(</head.*>)/i', '$1' . $script, $body));
				break;
			case self::HEAD_BOTTOM:
			default:
				$response->setBody(preg_replace('/(.*<\/head>)/i', $script . '$1', $body));
				break;
		}
	}
}
	