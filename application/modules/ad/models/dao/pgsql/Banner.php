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
 * @version 	$Id: Banner.php 5416 2010-09-14 03:55:40Z leha $
 * @since		2.0.5
 */

class Ad_Models_Dao_Pgsql_Banner extends Tomato_Model_Dao
	implements Ad_Models_Interface_Banner
{
	public function convert($entity) 
	{
		return new Ad_Models_Banner($entity); 
	}
	
	public function loadBanners()
	{
		$sql  = "SELECT b.*, pa.zone_id AS banner_zone_id, pa.route, pa.page_url
				FROM " . $this->_prefix . "ad_banner AS b
				INNER JOIN " . $this->_prefix . "ad_page_assoc AS pa
				ON b.banner_id = pa.banner_id
				WHERE b.status = 'active'";
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}

	public function getById($id) 
	{
		$sql = sprintf("SELECT * FROM " . $this->_prefix . "ad_banner
						WHERE banner_id = %s
						LIMIT 1",
						pg_escape_string($id));
		$rs  = pg_query($sql);
		$return = (0 == pg_num_rows($rs)) ? null : new Ad_Models_Banner(pg_fetch_object($rs));
		pg_free_result($rs);
		return $return;
	}
	
	public function add($banner) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "ad_banner (name, text, created_date, start_date, expired_date, code, click_url, target, format, image_url, mode, timeout, client_id, status)
						VALUES ('%s', '%s', '%s', %s, %s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s')
						RETURNING banner_id",
						pg_escape_string($banner->name),
						pg_escape_string($banner->text),
						pg_escape_string($banner->created_date),
						($banner->start_date) ? "'" . pg_escape_string($banner->start_date) . "'" : 'null',
						($banner->expired_date) ? "'" . pg_escape_string($banner->expired_date) . "'" : 'null',
						pg_escape_string($banner->code),
						pg_escape_string($banner->click_url),
						pg_escape_string($banner->target),
						pg_escape_string($banner->format),
						pg_escape_string($banner->image_url),
						pg_escape_string($banner->mode),
						pg_escape_string($banner->timeout),
						pg_escape_string(($banner->client_id) ? $banner->client_id : 'null'),
						pg_escape_string($banner->status));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->banner_id;
	}
	
	public function update($banner) 
	{
		return pg_update($this->_conn, $this->_prefix . 'ad_banner', 
						array(
							'name'		   => $banner->name,
							'text' 		   => $banner->text,
							'start_date'   => $banner->start_date,
							'expired_date' => $banner->expired_date,
							'code' 		   => $banner->code,
							'click_url'    => $banner->click_url,
							'target' 	   => $banner->target,
							'format' 	   => $banner->format,
							'image_url'    => $banner->image_url,
							'mode' 		   => $banner->mode,
							'timeout' 	   => $banner->timeout,
							'client_id'    => $banner->client_id,
							'status' 	   => $banner->status,
						), 
						array(
							'banner_id'    => $banner->banner_id,
						));	
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = 'SELECT b.* FROM ' . $this->_prefix . 'ad_banner AS b';
		if ($exp) {
			$where = array();
			
			if (isset($exp['route'])) {
				$sql 	.= ' INNER JOIN ' . $this->_prefix . 'ad_page_assoc AS pa ON b.banner_id = pa.banner_id';
				$where[] = sprintf("pa.route = '%s'", pg_escape_string($exp['route']));
			}
			if (isset($exp['banner_id'])) {
				$where[] = sprintf("b.banner_id = %s", pg_escape_string($exp['banner_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("b.status = '%s'", pg_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "b.name LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' ORDER BY b.banner_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(' LIMIT %s OFFSET %s', $count, $offset);
		}
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql = 'SELECT COUNT(*) AS num_banners FROM ' . $this->_prefix . 'ad_banner AS b';
		if ($exp) {
			$where = array();
			
			if (isset($exp['route'])) {
				$sql 	.= ' INNER JOIN ' . $this->_prefix . 'ad_page_assoc AS pa ON b.banner_id = pa.banner_id';
				$where[] = sprintf("pa.route = '%s'", pg_escape_string($exp['route']));
			}
			if (isset($exp['banner_id'])) {
				$where[] = sprintf("b.banner_id = %s", pg_escape_string($exp['banner_id']));
			}
			if (isset($exp['status'])) {
				$where[] = sprintf("b.status = '%s'", pg_escape_string($exp['status']));
			}
			if (isset($exp['keyword'])) {
				$where[] = "b.name LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' LIMIT 1';
		
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_banners;
	}
	
	public function delete($id) 
	{
		return pg_delete($this->_conn, $this->_prefix . 'ad_banner', 
						array(
							'banner_id' => $id,
						));
	}
	
	public function updateStatus($id, $status) 
	{
		return pg_update($this->_conn, $this->_prefix . 'ad_banner', 
						array(
							'status'    => $status,
						), 
						array(
							'banner_id' => $id,
						));		
	}
}
