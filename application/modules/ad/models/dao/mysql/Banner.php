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
 * @version 	$Id: Banner.php 5277 2010-09-02 04:01:38Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Mysql_Banner extends Tomato_Model_Dao
	implements Ad_Models_Interface_Banner
{
	public function convert($entity) 
	{
		return new Ad_Models_Banner($entity); 
	}
	
	public function loadBanners()
	{
		$sql = "SELECT b.*, pa.zone_id AS banner_zone_id, pa.route, pa.page_url
				FROM " . $this->_prefix . "ad_banner AS b
				INNER JOIN " . $this->_prefix . "ad_page_assoc AS pa
					ON b.banner_id = pa.banner_id
				WHERE b.status = 'active'";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}

	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "ad_banner
						WHERE banner_id = '%s'
						LIMIT 1",
						mysql_real_escape_string($id));
		$rs  = mysql_query($sql);
		$return = (0 == mysql_num_rows($rs)) ? null : new Ad_Models_Banner(mysql_fetch_object($rs));
		mysql_free_result($rs);
		return $return;
	}
	
	public function add($banner) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_banner (name, text, created_date, start_date, expired_date, code, click_url, target, format, image_url, mode, timeout, client_id, status)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($banner->name),
						mysql_real_escape_string($banner->text),
						mysql_real_escape_string($banner->created_date),
						mysql_real_escape_string($banner->start_date),
						mysql_real_escape_string($banner->expired_date),
						mysql_real_escape_string($banner->code),
						mysql_real_escape_string($banner->click_url),
						mysql_real_escape_string($banner->target),
						mysql_real_escape_string($banner->format),
						mysql_real_escape_string($banner->image_url),
						mysql_real_escape_string($banner->mode),
						mysql_real_escape_string($banner->timeout),
						mysql_real_escape_string($banner->client_id),
						mysql_real_escape_string($banner->status));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($banner) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "ad_banner
						SET name = '%s', text  = '%s', start_date = '%s', expired_date = '%s', code = '%s', click_url = '%s', 
							target = '%s', format = '%s', image_url = '%s', mode = '%s', timeout = '%s', client_id = '%s', status = '%s'
						WHERE banner_id = '%s'",
						mysql_real_escape_string($banner->name),
						mysql_real_escape_string($banner->text),
						mysql_real_escape_string($banner->start_date),
						mysql_real_escape_string($banner->expired_date),
						mysql_real_escape_string($banner->code),
						mysql_real_escape_string($banner->click_url),
						mysql_real_escape_string($banner->target),
						mysql_real_escape_string($banner->format),
						mysql_real_escape_string($banner->image_url),
						mysql_real_escape_string($banner->mode),
						mysql_real_escape_string($banner->timeout),
						mysql_real_escape_string($banner->client_id),
						mysql_real_escape_string($banner->status),
						mysql_real_escape_string($banner->banner_id));
		mysql_query($sql);
		return mysql_affected_rows();		
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = "SELECT b.* FROM " . $this->_prefix . "ad_banner AS b";
		if ($exp) {
			$where = array();
			
			if (isset($exp['route'])) {
				$sql 	.= " INNER JOIN " . $this->_prefix . "ad_page_assoc AS pa ON b.banner_id = pa.banner_id";
				$where[] = sprintf("pa.route = '%s'", mysql_real_escape_string($exp['route']));
			}
			if (isset($exp['banner_id'])) {
				$where[] = sprintf("b.banner_id = '%s'", mysql_real_escape_string($exp['banner_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("b.status = '%s'", mysql_real_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "b.name LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " ORDER BY b.banner_id DESC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql = "SELECT COUNT(*) AS num_banners FROM " . $this->_prefix . "ad_banner AS b";
		if ($exp) {
			$where = array();
			
			if (isset($exp['route'])) {
				$sql 	.= " INNER JOIN " . $this->_prefix . "ad_page_assoc AS pa ON b.banner_id = pa.banner_id";
				$where[] = sprintf("pa.route = '%s'", mysql_real_escape_string($exp['route']));
			}
			if (isset($exp['banner_id'])) {
				$where[] = sprintf("b.banner_id = '%s'", mysql_real_escape_string($exp['banner_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("b.status = '%s'", mysql_real_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "b.name LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= " WHERE " . implode(" AND ", $where);
			}
		}
		$sql .= " LIMIT 1";
		
		$rs   = mysql_query($sql);
		$row  = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_banners;
	}
	
	public function delete($id) 
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "ad_banner WHERE banner_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function updateStatus($id, $status) 
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "ad_banner 
						SET status = '%s'
						WHERE banner_id = '%s'",
						mysql_real_escape_string($status),
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
	}
}
