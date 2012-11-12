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
 * @version 	$Id: Abstract.php 3762 2010-07-17 12:25:01Z huuphuoc $
 * @since		2.0.5
 */

abstract class Tomato_Db_Connection_Abstract
{
	const KEY 		 = 'Tomato_Db_Connection_Abstract_Key';
	const PREFIX_KEY = 'Tomato_Db_Connection_Abstract_TablePrefix';
	
	/**
	 * Default table prefix
	 * 
	 * @var const
	 * @since 2.0.3
	 */
	const DEFAULT_PREFIX = 't_';

	protected $_adapter;
	
	public function __construct($adapter)
	{
		$this->_adapter = $adapter;
	}
	
	/**
	 * @return string
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}
	
	/**
	 * Support master connection type
	 * 
	 * @return mixed
	 */
	public function getMasterConnection() 
	{
		return $this->_getConnection('master');
	}
	
	/**
	 * Support slave connection type
	 * 
	 * @return mixed
	 */
	public function getSlaveConnection() 
	{
		return $this->_getConnection('slave');
	}
	
	/**
	 * Get database table prefix
	 * 
	 * @since 2.0.3
	 * @return string
	 */
	public static function getDbPrefix()
	{
		if (!Zend_Registry::isRegistered(self::PREFIX_KEY)) {
			$config = Tomato_Config::getConfig();
			
			/**
			 * Note that I use === operator that allows user to use empty prefix
			 */
			$prefix = (null === $config->db->prefix) ? self::DEFAULT_PREFIX : $config->db->prefix;
			Zend_Registry::set(self::PREFIX_KEY, $prefix);
		}
		return Zend_Registry::get(self::PREFIX_KEY);
	}
	
	/**
	 * @param string $type Type of connection. Must be slave or master
	 * @return mixed
	 */
	protected function _getConnection($type)
	{
		$key = self::KEY.'_'.$type;
		if (!Zend_Registry::isRegistered($key)) {
			$config  = Tomato_Config::getConfig();
			$servers = $config->db->$type;
			
			/**
			 * Connect to random server
			 */
			$servers = $servers->toArray();
			$randomServer = array_rand($servers);
			
			/**
			 * Get database prefix
			 * @since 2.0.3
			 */
			$prefix = (null == $config->db->prefix) ? self::DEFAULT_PREFIX : $config->db->prefix;
			
			$servers[$randomServer]['prefix'] = $prefix;
			
			$db = $this->_connect($servers[$randomServer]);
			
			Zend_Registry::set($key, $db);
		}
		return Zend_Registry::get($key);
	}
	
	/**
	 * Abstract connection
	 * @param array $config Database connection settings, includes parameters:
	 * - host
	 * - port
	 * - dbname
	 * - username
	 * - password
	 * - charset
	 * @return mixed Database connection
	 */
	protected abstract function _connect($config);
	
	/**
	 * Get database server version
	 */
	public abstract function getVersion();

	/**
	 * Execute SQL query
	 * 
	 * @param string $sql
	 */
	public abstract function query($sql);
}
