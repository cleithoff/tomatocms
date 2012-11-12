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
 * @version 	$Id: Template.php 4539 2010-08-12 09:56:40Z huuphuoc $
 * @since		2.0.6
 */

interface Mail_Models_Interface_Template
{
	/**
	 * Get template by name
	 * 
	 * @param string $name
	 * @return Mail_Models_Template
	 */
	public function getByName($name);
	
	/**
	 * Get template by given Id
	 * 
	 * @param int $id
	 * @return Mail_Models_Template
	 */
	public function getById($id);
	
	/**
	 * List templates created by given user
	 * 
	 * @param int $userId User's Id
	 * @param int $offset
	 * @param int $count
	 * @return Tomato_Model_RecordSet
	 */
	public function getTemplates($userId, $offset = null, $count = null);
	
	/**
	 * Count the number of templates created by given user  
	 * 
	 * @param int $userId User's Id
	 * @return int
	 */
	public function count($userId);
	
	/**
     * Add new template
     * 
     * @param Mail_Models_Template $template
	 */
	public function add($template);
	
	/**
	 * Delete template
	 * 
	 * @param int $id
	 */
	public function delete($id);
	
	/**
	 * Update template
	 * 
	 * @param Mail_Models_Template $template
	 */
	public function update($template);
}
