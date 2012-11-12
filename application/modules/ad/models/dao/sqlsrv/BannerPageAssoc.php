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
 * @version 	$Id: BannerPageAssoc.php 4951 2010-08-25 17:59:29Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Sqlsrv_BannerPageAssoc extends Tomato_Model_Dao
	implements Ad_Models_Interface_BannerPageAssoc
{
	public function convert($entity) 
	{
		return new Ad_Models_BannerPageAssoc($entity); 
	}
	
	public function removeByBanner($bannerId)
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'ad_page_assoc WHERE banner_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($bannerId));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}

	public function add($bannerPageAssoc)
	{
		$this->_conn->insert($this->_prefix . 'ad_page_assoc', array(
			'route'		 => $bannerPageAssoc->route,
			'page_url'	 => $bannerPageAssoc->page_url,
			'page_title' => $bannerPageAssoc->page_title,
			'zone_id'	 => $bannerPageAssoc->zone_id,
			'banner_id'	 => $bannerPageAssoc->banner_id,
		));
	}
	
	public function getBannerPageAssoc($bannerId)
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'ad_page_assoc
				WHERE banner_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($bannerId));
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);		
	}
}
