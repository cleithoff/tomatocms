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
 * @version 	$Id: Translation.php 4621 2010-08-15 03:37:15Z huuphuoc $
 * @since		2.0.8
 */

interface Core_Models_Interface_Translation
{
	/**
	 * Add translation relation
	 * 
	 * @param Core_Models_Translation $translation
	 * @return int
	 */
	public function add($translation);

	/**
	 * Update translation relation
	 * 
	 * @param Core_Models_Translation $translation
	 * @return int
	 */
	public function update($translation);
	
	/**
	 * Delete translation items

	 * @param int $id Id of item or source item 
	 * @param string $class Class name
	 * @return int
	 */
	public function delete($id, $class);
	
	/**
	 * Get translation items based on it and class
	 * 
	 * @param int $id	    Id of source item
	 * @param string $class Class name
	 * @param string $lang
	 * @return Tomato_Model_RecordSet
	 */
	public function getItems($id, $class, $lang = null);
}
