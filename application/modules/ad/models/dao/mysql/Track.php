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
 * @version 	$Id: Track.php 4948 2010-08-25 13:41:47Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Mysql_Track extends Tomato_Model_Dao
	implements Ad_Models_Interface_Track
{
	public function convert($entity) 
	{
		return new Ad_Models_Track($entity); 
	}
	
	public function add($track) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_click (banner_id, zone_id, page_id, clicked_date, ip, from_url)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($track->banner_id),
						mysql_real_escape_string($track->zone_id),
						mysql_real_escape_string($track->page_id),
						mysql_real_escape_string($track->clicked_date),
						mysql_real_escape_string($track->ip),
						mysql_real_escape_string($track->from_url));
		mysql_query($sql);
		return mysql_insert_id();
	}
}
