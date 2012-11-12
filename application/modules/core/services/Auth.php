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
 * @version 	$Id: Auth.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_Services_Auth implements Zend_Auth_Adapter_Interface 
{
	/**
	 * Authenticated success
	 * Its value must be greater than 0
	 */
	const SUCCESS = 1;
	
	/**
	 * Constant define that user has not been active
	 * Its value must be smaller than 0
	 */
	const NOT_ACTIVE = -1;
	
	/**
	 * General failure
	 * Its value must be smaller than 0
	 */
	const FAILURE = -2;
	
	private $_username;
	private $_password;
	
	public function __construct($username, $password) 
	{
		$this->_username = $username;
		$this->_password = $password;
	}

	/**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate() 
    {    	
		$password = md5($this->_password);
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$dao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getUserDao();
		$dao->setDbConnection($conn);
		
		$user = $dao->authenticate($this->_username, $password);
		if (null == $user) {
			return new Zend_Auth_Result(self::FAILURE, null);
		}
    	if (!$user->is_active) {
    		return new Zend_Auth_Result(self::NOT_ACTIVE, null);
    	}
    	return new Zend_Auth_Result(self::SUCCESS, $user);
    }		
}
