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
 * @version 	$Id: BannerPageAssoc.php 5417 2010-09-14 03:55:57Z leha $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pgsql_BannerPageAssoc extends Tomato_Model_Dao
	implements Ad_Models_Interface_BannerPageAssoc
{
	public function convert($entity) 
	{
		return new Ad_Models_BannerPageAssoc($entity); 
	}
	
	public function removeByBanner($bannerId)
	{
		return pg_delete($this->_conn, $this->_prefix . 'ad_page_assoc',
						array(
							'banner_id' => $bannerId,
						));
	}

	public function add($bannerPageAssoc)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_page_assoc (route, page_url, page_title, zone_id, banner_id)
						VALUES ('%s', '%s', '%s', %s, %s)",
						pg_escape_string($bannerPageAssoc->route),
						pg_escape_string($bannerPageAssoc->page_url),
						pg_escape_string($bannerPageAssoc->page_title),
						pg_escape_string($bannerPageAssoc->zone_id),
						pg_escape_string($bannerPageAssoc->banner_id));
		pg_query($sql);
	}
	
	public function getBannerPageAssoc($bannerId)
	{
		$sql  = sprintf("SELECT * FROM " . $this->_prefix . "ad_page_assoc
						WHERE banner_id = '%s'",
						pg_escape_string($bannerId));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);		
	}
}
