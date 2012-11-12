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
 * @version 	$Id: Gateway.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

abstract class Tomato_Model_Gateway 
{
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_conn;
	
	/**
	 * Database table prefix
	 * 
	 * @var string
	 * @since 2.0.3
	 */
	protected $_prefix;
	
	/**
	 * @since 2.0.3
	 * @return void
	 */
	public function __construct($conn = null)
	{
		$this->_prefix = Tomato_Db_Connection::getDbPrefix();
		if ($conn != null) {
			$this->setDbConnection($conn);
		}
	}
	
	/**
	 * @param Zend_Db_Adapter_Abstract $conn
	 */
	public function setDbConnection($conn) 
	{
		$this->_conn = $conn;
	}

	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDbConnection()
	{
		return $this->_conn;
	}
	
	/**
	 * Convert an object or array to entity instance
	 * @param mixed $entity
	 * @return Tomato_Model_Entity
	 */
	abstract function convert($entity);
}
