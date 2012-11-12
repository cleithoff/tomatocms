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
 * @version 	$Id: Dao.php 4806 2010-08-24 03:26:56Z huuphuoc $
 * @since		2.0.0
 */

abstract class Tomato_Model_Dao
{
	/**
	 * @var Tomato_Db_Connection
	 */
	protected $_conn;
	
	/**
	 * Database table prefix
	 * 
	 * @var string
	 * @since 2.0.3
	 */
	protected $_prefix = '';
	
	/**
	 * The language content
	 * @var string
	 * @since 2.0.8
	 */
	protected $_lang;
	
	/**
	 * @since 2.0.3
	 * @return void
	 */
	public function __construct($conn = null)
	{
		$this->_prefix = Tomato_Db_Connection_Abstract::getDbPrefix();
		$this->_lang   = Tomato_Config::getConfig()->localization->languages->default;
		if ($conn != null) {
			$this->setDbConnection($conn);
		}
	}
	
	/**
	 * @param Tomato_Db_Connection $conn
	 * @return Tomato_Model_Dao
	 */
	public function setDbConnection($conn)
	{
		$this->_conn = $conn;
		return $this;
	}

	/**
	 * @return Tomato_Db_Connection
	 */
	public function getDbConnection()
	{
		return $this->_conn;
	}
	
	/**
	 * @since 2.0.8
	 * @param string $lang
	 * @return Tomato_Model_Dao
	 */
	public function setlang($lang)
	{
		$this->_lang = $lang;
		return $this;
	}
	
	/**
	 * Convert an object or array to entity instance
	 * @param mixed $entity
	 * @return Tomato_Model_Entity
	 */
	abstract function convert($entity);
	
	/* ========== For translation =========================================== */
	
	/**
	 * Get translation items
	 * 
	 * @since 2.0.8
	 * @param Tomato_Model_Entity $item
	 * @return Tomato_Model_RecordSet
	 */
	public function getTranslations($item)
	{
		return null;
	}
}
