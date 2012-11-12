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
 * @version 	$Id: Zone.php 4514 2010-08-12 09:32:15Z huuphuoc $
 * @since		2.0.5
 */

interface Ad_Models_Interface_Zone
{
	/**
	 * Get zone by given Id
	 * 
	 * @param int $id Id of zone
	 * @return Ad_Models_Zone
	 */
	public function getById($id);
	
	/**
	 * Get all zones
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getZones();
	
	/**
	 * Delete zone by Id
	 * 
	 * @param int $id Id of zone
	 * @return int
	 */
	public function delete($id);

	/**
	 * Update zone
	 * 
	 * @param Ad_Models_Zone $zone
	 * @return int
	 */
	public function update($zone);

	/**
	 * Add new zone
	 * 
	 * @param Ad_Models_Zone $zone
	 * @return int
	 */
	public function add($zone);
	
	/**
	 * Check whether a zone exists or not
	 * 
	 * @param string $name Name (code) of zone
	 * @return bool
	 */
	public function exist($name);
}
