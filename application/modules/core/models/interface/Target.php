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
 * @version 	$Id: Target.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.5
 */

interface Core_Models_Interface_Target
{
	/**
	 * List all hook targets
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getTargets();
	
	/**
	 * Add new target
	 * 
	 * @param Core_Models_Target $target
	 * @return int
	 */
	public function add($target);
	
	/**
	 * Delete target by Id
	 * 
	 * @param int $id Id of target
	 * @return int
	 */
	public function delete($id);
}
