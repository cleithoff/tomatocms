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
 * @version 	$Id: BannerPageAssoc.php 5470 2010-09-20 08:30:02Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents the relationship between the banner and the page that show the banner 
 */
class Ad_Models_BannerPageAssoc extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'route' 	 => null,	/** Page route */
	
		/**
		 * Page URL. We need to use this field when we want to show the banner 
		 * in a particular page
		 */
		'page_url' 	 => null,
	
		'page_title' => null,	/** Page title */
		'zone_id' 	 => null,	/** Id of zone */
		'banner_id'  => null,	/** Id of banner */
	);
}
