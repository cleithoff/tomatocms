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
 * @version 	$Id: RecordSet.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Model_RecordSet implements Countable, Iterator, ArrayAccess 
{
	/**
	 * @var int
	 */
	protected $_count = 0;
	
	private $_iteratorIndex = 0;
	
	/**
	 * @var Tomato_Model_Gateway
	 */
	protected $_gateway;
	
	/**
	 * @var string
	 */
	protected $_entityClass;
	
	/**
	 * @var mixed
	 */
	protected $_results;
	
	public function __construct($results, $gateway, $entityClass = null) 
	{
		$this->_results 	= $results;
		$this->_gateway 	= $gateway;
		$this->_entityClass = $entityClass;
	}
	
	/*
	 * Implement Countable interface
	 */
	
	public function count() 
	{
		if (null == $this->_count) {
			$this->_count = count($this->_results);
		}
		return $this->_count;
	}
	
	/*
	 * Implement Iterator interface
	 */
	
	public function key() 
	{
		return key($this->_results);	
	}
	
	public function next() 
	{
		$this->_iteratorIndex++;
		return next($this->_results);
	}
	
	public function rewind() 
	{
		$this->_iteratorIndex = 0;
		return reset($this->_results);
	}
	
	public function valid() 
	{
		return $this->_iteratorIndex < $this->count();
//		return (bool)$this->current();
	}
	
	public function current() 
	{
		$key = ($this->_results instanceof Iterator)
				? $this->_results->key() 
				: key($this->_results);
		$result = $this->_results[$key];
//		if (get_class($result) != $this->_entityClass) {
			$result = $this->_gateway->convert($result);
			
			// Commented on Feb 3rd, 2010 by huuphuoc
			//$this->_results[$key] = $result;
//		}
		return $result;
	}
	
	/*
	 * Implement ArrayAccess interface
	 */
	
	public function offsetExists($key) 
	{
		return array_key_exists($key, $this->_results);
	}
	
	public function offsetGet($key) 
	{
		$result = $this->_gateway->convert($this->_results[$key]);
        return $result;
    }
    
	public function offsetSet($key, $element) 
	{
        $this->_results[$key] = $element;
        $this->_count = count($this->_results);
    }
    
	public function offsetUnset($key) 
	{
        unset($this->_results[$key]);
        $this->_count = count($this->_results);
    }
}