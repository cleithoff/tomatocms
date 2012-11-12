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
 * @version 	$Id: Article.php 5485 2010-09-20 10:00:00Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents an article
 */
class News_Models_Article extends Tomato_Model_Entity
{
	protected $_properties = array(
		'article_id' 		 => null,	/** Id of article */
		'title' 			 => null,	/** Main title of article */
		'sub_title' 		 => null,	/** Sub-title of article */
		'slug' 				 => null,	/** Slug of article */
		'description' 		 => null,	/** Description of article */	
		'content' 			 => null,	/** Content of article */
		'icons' 			 => null,	/** Article icons */
	
		/**
		 * URL of thumbnail image that represent article
		 */
		'image_square' 		 => null,	/** The thumbnail in square size */
		'image_thumbnail' 	 => null,	/** Thumbnail size */
		'image_small' 		 => null,	/** Small size */
		'image_crop' 		 => null,	/** Crop size */
		'image_medium' 		 => null,	/** Medium size */
		'image_large' 		 => null,	/** Large size */
	
		/** 
		 * Article's status. Can be one of following values:
		 * - active
		 * - inactive
		 * - draft
		 * - deleted
		 */
		'status' 			 => 'inactive',
		
		'num_views' 		 => 0,		/** Number of views */
		
		'created_date' 		 => null,	/** Article's creation date */
		'created_user_id' 	 => null,	/** Id of user who create article */
		'created_user_name'  => null,	/** Username of user who create article */

		'updated_date' 		 => null,	/** Article's modification date */
		'updated_user_id' 	 => null,	/** Id of user who update article */
		'updated_user_name'  => null,	/** Username of user who update article */
	
		'activate_date' 	 => null,	/** Article's activation date */
		'activate_user_id' 	 => null,	/** Id of user who activate article */
		'activate_user_name' => null,	/** Username of user who activate article */
		
		'author'			 => null,	/** Author of article */
		'allow_comment' 	 => 0,		/** Defines that user can comment on article or not */
		'sticky' 			 => 0,		/** Defines that article is sticky of main category or not */
		'language'			 => null,	/** Language of article (@since 2.0.8) */
	);
	
	public function getProperties()
	{
		$pros = $this->_properties;
		
		/**
		 * Allow user to use {year}, {month}, {day} in article URL
		 * @since 2.0.7
		 */
		$date = $this->_properties['created_date'];
		if (null == $date) {
			$pros['year']  = date('Y');
			$pros['month'] = date('m');
			$pros['day']   = date('d');
		} else {
			$timestamp 	   = strtotime($date);
			$pros['year']  = date('Y', $timestamp);
			$pros['month'] = date('m', $timestamp);
			$pros['day']   = date('d', $timestamp);
		}
		
		return $pros;
	}
}
