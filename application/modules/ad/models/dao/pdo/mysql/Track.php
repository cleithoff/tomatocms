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
 * @version 	$Id: Track.php 5330 2010-09-07 06:47:36Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pdo_Mysql_Track extends Tomato_Model_Dao
	implements Ad_Models_Interface_Track
{
	public function convert($entity) 
	{
		return new Ad_Models_Track($entity); 
	}
	
	public function add($track) 
	{
		$this->_conn->insert($this->_prefix . 'ad_click', 
							array(
								'banner_id'    => $track->banner_id,
								'zone_id' 	   => $track->zone_id,
								'page_id' 	   => $track->page_id,
								'clicked_date' => $track->clicked_date,
								'ip' 		   => $track->ip,
								'from_url' 	   => $track->from_url,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'ad_click');
	}
}
