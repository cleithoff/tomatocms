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
 * @version 	$Id: SessionHandler.php 5377 2010-09-10 07:44:37Z huuphuoc $
 * @since		2.0.0
 */

class Core_Services_SessionHandler implements Zend_Session_SaveHandler_Interface 
{
	/**
	 * @var Core_Services_SessionHandler
	 */
	private static $_instance;
	
	/**
	 * Session lifetime
	 * @since 2.0.3
	 * @var int
	 */
	private $_lifetime;
	
	/**
	 * @var Core_Models_Interface_Session
	 */
	private $_sessionDao;
	
	private function __construct()
	{
		$config = Tomato_Config::getConfig();
		$this->_lifetime = (isset($config->web->session->cookie_lifetime))
							? $config->web->session->cookie_lifetime
							: (int) ini_get('session.gc_maxlifetime');
							
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$this->_sessionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getSessionDao();
		$this->_sessionDao->setDbConnection($conn);
	}
	
	/**
	 * Add this destructor to fix the error: 
	 * "sqlsrv_errors contains an invalid type in ..." when use Sqlsrv
	 * 
	 * @return void
	 */
	public function __destruct()
	{
		session_write_close();
	}
	
	/**
	 * @return Core_Services_SessionHandler
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function close() 
	{
		return true;
	}
	
	public function destroy($id) 
	{
		return $this->_sessionDao->delete($id);
	}
	
	public function gc($maxlifetime) 
	{
		$this->_sessionDao->destroy(time());
		return true;
	}
	
	public function open($save_path, $name) 
	{
		return true;	
	}
	
	public function read($id) 
	{
		$return  = '';
		$session = $this->_sessionDao->getById($id);
		
		if ($session != null) {
			$expirationTime = (int) $session->modified + $session->lifetime;
			if ($expirationTime > time()) {
				$return = $session->data;
			} else {
				$this->destroy($id);
			}
		}
		return $return;
	}
	
	public function write($id, $data) 
	{
		$obj = new stdClass();
		$obj->session_id = $id;
		$obj->data 		 = $data;
		$obj->modified 	 = time();
		
		$session = $this->_sessionDao->getById($id);
		if (null == $session) {
			$obj->lifetime = $this->_lifetime;
			/**
			 * We could not call:
			 * <code>
			 * $this->_sessionDao->insert(new Core_Models_Session(...))
			 * </code>
			 */
			return $this->_sessionDao->insert($obj);
		} else {
			$obj->lifetime = $session->lifetime;
			return $this->_sessionDao->update($obj);
		}
	}
}
