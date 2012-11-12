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
 * @version 	$Id: Resource.php 4518 2010-08-12 09:35:54Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Resource
{
	/**
	 * For ACL
	 * Get all resources associated with given module
	 * 
	 * @param string $module Name of module
	 * @return Tomato_Model_RecordSet
	 */
	public function getResources($module = null);
	
	/**
	 * Get resource by given Id
	 * 
	 * @param int $id Id of resource
	 * @return Core_Models_Resource
	 */
	public function getById($id);
	
	/**
	 * Add new resource
	 * 
	 * @param Core_Models_Resource $resource
	 * @return int
	 */
	public function add($resource);
	
	/**
	 * Delete resource by given Id
	 * 
	 * @param int $id Id of resource
	 * @return int
	 */
	public function delete($id);
}
