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
 * @version 	$Id: Session.php 4520 2010-08-12 09:36:49Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Session
{
	/**
	 * Delete all session data by given Id
	 * 
	 * @param string $id Id of session
	 * @return bool
	 */
	public function delete($id);

	/**
	 * Destroy all timeout session
	 * 
	 * @param int $time The timestamp
	 */
	public function destroy($time);
	
	/**
	 * Get session by given Id
	 * 
	 * @param int $id Id of session
	 * @return Core_Models_Session
	 */
	public function getById($id);
	
	/**
	 * Create new session
	 * 
	 * @param Core_Models_Session $session
	 * @return int
	 */
	public function insert($session);
	
	/**
	 * Update session data
	 * 
	 * @param Core_Models_Session $session
	 * @return int
	 */
	public function update($session);
}
