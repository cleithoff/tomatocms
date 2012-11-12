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
 * @version 	$Id: Banner.php 5469 2010-09-20 08:21:02Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents a banner
 */
class Ad_Models_Banner extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'banner_id'    => null,		/** Id of banner */
		'name' 		   => null,		/** Name of banner */
		'text' 		   => null,		/** Text of banner */
		'num_clicks'   => null,		/** Number of clicks */
		'created_date' => null,		/** Banner's creation date */
		'start_date'   => null,		/** Banner's starting date */
		'expired_date' => null,		/** Banner's expired date */
		'client_id'    => null,		/** Id of client */
		'code' 		   => null,		/** HTML code */
		'click_url'    => null,		/** Target URL */
	
		/** 
		 * The Target. Can take one of three values: 
		 * - new_tab: Opening banner will open new tab (default)
		 * - new_window
		 * - same_window 
		 */
		'target' 	   => null,

		/**
		 * Format of banner. Can take one of the following values:
		 * - image: If banner is image
		 * - flash
		 * - text
		 * - html
		 */
		'format' 	   => null,
	
		'image_url'    => null,		/** URL of banner's image */
		'ordering' 	   => null,		/** Ordering index */
	
		/**
		 * Banner's mode. Can take one of two values:
		 * - unique: There is only one banner in the zone (default)
		 * - share: There are many banners in the same zone.
		 */
		'mode' 		   => null,
	
		'timeout' 	   => null,		/** The timeout in seconds. It will be used when user use the sharing mode */
		'status' 	   => null,		/** Status of banner: active or inactive */
	);
}
