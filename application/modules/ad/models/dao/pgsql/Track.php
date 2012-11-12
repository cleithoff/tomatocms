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
 * @version 	$Id: Track.php 4950 2010-08-25 17:54:16Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pgsql_Track extends Tomato_Model_Dao
	implements Ad_Models_Interface_Track
{
	public function convert($entity) 
	{
		return new Ad_Models_Track($entity); 
	}
	
	public function add($track) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_click (banner_id, zone_id, page_id, clicked_date, ip, from_url)
						VALUES (%s, %s, %s, '%s', '%s', '%s')",
						pg_escape_string($track->banner_id),
						pg_escape_string($track->zone_id),
						pg_escape_string($track->page_id),
						pg_escape_string($track->clicked_date),
						pg_escape_string($track->ip),
						pg_escape_string($track->from_url));
		$rs  = pg_query($sql);
	}
}
