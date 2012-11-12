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
 * @version 	$Id: Category.php 4618 2010-08-15 02:46:13Z huuphuoc $
 * @since		2.0.5
 */

interface Category_Models_Interface_Category
{
	/**
	 * Get category by given Id
	 * 
	 * @param int $id Id of category
	 * @return Category_Models_Category
	 */
	public function getById($id);
	
	/**
	 * Get sub-categories of given category
	 * 
	 * @param int $categoryId Id of category
	 * @return Tomato_Model_RecordSet
	 */
	public function getSubCategories($categoryId);
	
	/**
	 * Add new category
	 * 
	 * @param Category_Models_Category $category
	 * @return int
	 */
	public function add($category);
	
	/**
	 * Update category
	 * 
	 * @param Category_Models_Category $category
	 * @return void
	 */
	public function update($category);
	
	/**
	 * Delete category
	 * 
	 * @param Category_Models_Category $category
	 * @return void
	 */
	public function delete($category);
	
	/**
	 * Build category tree with depth for each item
	 * 
	 * @return Tomato_Model_RecordSet
	 */
	public function getTree();
	
	/**
	 * Get parent categories (From root to parent one)
	 * 
	 * @param int $categoryId Id of category
	 * @return Tomato_Model_RecordSet
	 */
	public function getParents($categoryId);
	
	/**
	 * Get parent category
	 * 
	 * @param int $categoryId Id of category
	 * @return Category_Models_Category
	 */	
	public function getParentId($categoryId);
	
	/**
	 * Update category order
	 * 
	 * @since 2.0.7
	 * @param Category_Models_Category $category
	 * @return int
	 */
	public function updateOrder($category);
	
	/**
	 * Get translable items which haven't been translated of the default language
	 * 
	 * @since 2.0.8
	 * @param string $lang
	 * @return Tomato_Model_RecordSet
	 */
	public function getTranslatable($lang);
	
	/**
	 * Get translation item which was translated to given category
	 * 
	 * @since 2.0.8
	 * @param Category_Models_Category $category
	 * @return Category_Models_Category
	 */
	public function getSource($category);
}
