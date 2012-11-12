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
 * @version 	$Id: Track.php 5470 2010-09-20 08:30:02Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Represents the banner click
 */
class Ad_Models_Track extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'banner_id'    => null,		/** Id of banner */
		'zone_id' 	   => null,		/** Id of zone */
		'page_id' 	   => null,		/** Id of page */
		'clicked_date' => null,		/** Clicked date */
		'ip' 		   => null,		/** IP address of visitor */
		'from_url' 	   => null,		/** The URL of page that user click banner on */
	);
}
