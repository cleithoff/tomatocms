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
 * @version 	$Id: Banner.php 4512 2010-08-12 09:31:13Z huuphuoc $
 * @since		2.0.5
 */

interface Ad_Models_Interface_Banner
{
	/**
	 * Load all banners
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function loadBanners();
	
	/**
	 * Get banner by given Id
	 * 
	 * @param int $id Id of banner
	 * @return Ad_Models_Banner
	 */
	public function getById($id);

	/**
	 * Add new banner
	 * 
	 * @param Ad_Models_Banner $banner
	 * @return int
	 */
	public function add($banner);
	
	/**
	 * Update banner
	 * 
	 * @param Ad_Models_Banner $banner
	 * @return int
	 */
	public function update($banner);
	
	/**
	 * Find all banners by expression
	 * 
	 * @param int $offset
	 * @param int $count
	 * @param array $exp Search expression. An array contain various conditions, keys including:
	 * - route
	 * - banner_id
	 * - status
	 * - keyword
	 * @return Tomato_Model_RecordSet
	 */
	public function find($offset, $count, $exp = null);
	
	/**
	 * Count number of banners by expression
	 * 
	 * @param array $exp Search expression (@see find)
	 * @return int
	 */
	public function count($exp = null);
	
	/**
	 * Delete a banner by Id
	 * 
	 * @param int $id Id of banner
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Update banner's status
	 * 
	 * @param int $id Id of banner
	 * @param string $status New banner status
	 * @return int
	 */
	public function updateStatus($id, $status);
}
