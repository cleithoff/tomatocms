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
 * @version 	$Id: BannerPageAssoc.php 4948 2010-08-25 13:41:47Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Mysql_BannerPageAssoc extends Tomato_Model_Dao
	implements Ad_Models_Interface_BannerPageAssoc
{
	public function convert($entity) 
	{
		return new Ad_Models_BannerPageAssoc($entity); 
	}
	
	public function removeByBanner($bannerId)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "ad_page_assoc WHERE banner_id = '%s'", 
						mysql_real_escape_string($bannerId));
		mysql_query($sql);
		return mysql_affected_rows();
	}

	public function add($bannerPageAssoc)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_page_assoc (route, page_url, page_title, zone_id, banner_id)
						VALUES ('%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($bannerPageAssoc->route),
						mysql_real_escape_string($bannerPageAssoc->page_url),
						mysql_real_escape_string($bannerPageAssoc->page_title),
						mysql_real_escape_string($bannerPageAssoc->zone_id),
						mysql_real_escape_string($bannerPageAssoc->banner_id));
		mysql_query($sql);
	}
	
	public function getBannerPageAssoc($bannerId)
	{
		$sql  = sprintf("SELECT * FROM " . $this->_prefix . "ad_page_assoc
						WHERE banner_id = '%s'",
						mysql_real_escape_string($bannerId));
						
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);	
	}
}
