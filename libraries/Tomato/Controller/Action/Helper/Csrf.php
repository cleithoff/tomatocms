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
 * @version 	$Id: Csrf.php 5376 2010-09-10 07:40:05Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Controller_Action_Helper_Csrf extends Zend_Controller_Action_Helper_Abstract
{
	protected $_salt = 'salt';
	
	protected $_name = 'csrf';
	
	protected $_timeout = 300;
	
	protected $_session = null;
	
	protected $_csrfEnable = false;
	
	/**
	 * CSRF request method to attack site. It can take value of POST or GET
	 * @var string
	 */
	protected $_csrfRequestMethod = 'POST';
	
	/**
	 * Defines where to get the taken value from. It can take value of POST or GET
	 * @var string
	 */
	protected $_csrfRetriveMethod = 'POST';
	
	protected $_token;
	
	public function __construct(array $config = array())
	{
		if (isset($config['salt'])) {
			$this->_salt = $config['salt']; 
		}
		if (isset($config['name'])) {
			$this->_name = $config['name'];
		}
		if (isset($config['timeout'])) {
			$this->_timeout = $config['timeout'];
		}
	}
	
	public function init()
	{
		/**
		 * Do NOT continue processing if there is any error
		 */
		$request = $this->getRequest();
		$errorHandler = $request->getParam('error_handler'); 
		if ($errorHandler && $errorHandler->exception) {
			return;
		}
		
		$router   = $this->getFrontController()->getRouter();
		$route    = $router->getCurrentRoute();
		
		/**
		 * @since 2.0.9
		 */
		if ($route instanceof Zend_Controller_Router_Route_Chain) {
			return;	
		}
		
		$defaults = $route->getDefaults();
		if (isset($defaults['csrf']) && 'true' == (string)$defaults['csrf']['enable']) {
			$this->_csrfEnable = true;
			$this->_csrfRequestMethod = strtoupper($defaults['csrf']['request']);
			$this->_csrfRetriveMethod = strtoupper($defaults['csrf']['retrive']);	
		}
	}
	
	public function preDispatch()
	{
		if ($this->_csrfEnable) {
			$session = $this->_getSession();
	        $session->setExpirationSeconds($this->_timeout);
	        
	        $this->_token = $session->token;
	        $session->token = $this->_generateToken();
	        
			$request = $this->getRequest();
			$isValid = null;
			
			if (($request->isPost() && $this->_csrfRequestMethod == 'POST')
				|| ($request->isGet() && $this->_csrfRequestMethod == 'GET')) 
			{
				switch ($this->_csrfRetriveMethod) {
					case 'POST':
						$token = $request->getPost($this->_name);
						break;
					case 'GET':
						$token = $request->getQuery($this->_name);
						break;
				}
				$isValid = $this->isValidToken($token);
			}
			
			if ($isValid === false) {
				throw new RuntimeException('Token does not match');
			}
		}
	}
	
	public function postDispatch()
	{
		if ($this->_csrfEnable) {
			$element = sprintf('<input type="hidden" name="%s" value="%s" />',
				$this->_name,
				$this->getToken()
			);
			
			$this->getActionController()->view->assign('tokenElement', $element);
		}
	}
	
	public function getTokenName() 
	{
		return $this->_name;	
	}
		
	public function isValidToken($token)
	{
		if (null == $token || '' == $token) {
			return false;
		}
		return ($token == $this->_token);
	}
	
	public function getToken()
	{
		$session = $this->_getSession();
		if (!isset($session->token)) {
			/**
			 * We need to regenerate token
			 */
	        $session->token = $this->_generateToken();
		}
		return $session->token;
	}	
	
	private function _getSession()
	{
		if (null == $this->_session) {
			$this->_session = new Zend_Session_Namespace($this->_getSessionName());
		}
		return $this->_session;
	}
	
	private function _getSessionName() 
	{
		return __CLASS__ . $this->_salt . $this->_name;
	}
	
	/**
	 * @return string
	 */
	private function _generateToken()
	{
		$token = md5(
            mt_rand(1, 1000000)
            .  $this->_salt
            .  $this->_name
            .  mt_rand(1, 1000000)
        );
        return $token;
	}
}
