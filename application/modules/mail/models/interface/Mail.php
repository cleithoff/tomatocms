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
 * @version 	$Id: Mail.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.6
 */

interface Mail_Models_Interface_Mail
{
	/**
	 * List mails sent by given user
	 * 
	 * @param int $userId User's Id
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getMails($userId, $offset = null, $count = null);
	
	/**
	 * Count the number of mails sent by given user  
	 * 
	 * @param int $userId User's Id
	 * @return int
	 */
	public function count($userId);
	
	/**
	 * Add mail
	 * 
	 * @param Mail_Models_Mail $mail
	 */
	public function add($mail);
}
