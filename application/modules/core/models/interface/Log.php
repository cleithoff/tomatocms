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
 * @version 	$Id: Log.php 3989 2010-07-25 16:58:07Z huuphuoc $
 * @since		2.0.7
 */

interface Core_Models_Interface_Log
{
	/**
	 * Add log
	 * 
	 * @param Core_Models_Log $log
	 * @return int
	 */
	public function add($log);
	
	/**
	 * Get the total number of logs
	 * 
	 * @return int
	 */
	public function count();

	/**
	 * Delete log by given id
	 * 
	 * @param int $id
	 * @return int
	 */
	public function delete($id);

	/**
	 * Get the list of logs
	 * 
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset = null, $count = null);
}
