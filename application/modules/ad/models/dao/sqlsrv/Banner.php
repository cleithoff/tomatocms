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
 * @version 	$Id: Banner.php 5279 2010-09-02 04:08:59Z huuphuoc $
 * @since		2.0.5
 */

class Ad_Models_Dao_Sqlsrv_Banner extends Tomato_Model_Dao
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
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}

	public function getById($id) 
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'ad_banner
				WHERE banner_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$row = $stmt->fetch();
		$return = (null == $row) ? null : new Ad_Models_Banner($row);
		$stmt->closeCursor();
		return $return;
	}
	
	public function add($banner) 
	{
		$this->_conn->insert($this->_prefix . 'ad_banner', array(
			'name'		   => $banner->name,
			'text'		   => $banner->text,
			'created_date' => $banner->created_date,
			'start_date'   => $banner->start_date,
			'expired_date' => $banner->expired_date,
			'code'		   => $banner->code,
			'click_url'	   => $banner->click_url,
			'target'	   => $banner->target,
			'format'	   => $banner->format,
			'image_url'	   => $banner->image_url,
			'mode'		   => $banner->mode,
			'timeout'	   => $banner->timeout,
			'client_id'	   => $banner->client_id,
			'status'	   => $banner->status,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'ad_banner');
	}
	
	public function update($banner) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'ad_banner
				SET name = ?, text = ?, start_date = ?, expired_date = ?, code = ?, 
					click_url = ?, target = ?, format = ?, image_url = ?, mode = ?, 
					timeout = ?, client_id = ?, status = ?
				WHERE banner_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$banner->name, 
			$banner->text, 
			$banner->start_date, 
			$banner->expired_date, 
			$banner->code, 
			$banner->click_url, 
			$banner->target, 
			$banner->format, 
			$banner->image_url, 
			$banner->mode, 
			$banner->timeout, 
			$banner->client_id, 
			$banner->status, 
			$banner->banner_id, 
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function find($offset, $count, $exp = null) 
	{
		$sql = 'SELECT b.* FROM ' . $this->_prefix . 'ad_banner AS b';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['route'])) {
				$sql 	 .= ' INNER JOIN ' . $this->_prefix . 'ad_page_assoc AS pa ON b.banner_id = pa.banner_id';
				$where[]  = 'pa.route = ?';
				$params[] = $exp['route'];
			}
			if (isset($exp['banner_id'])) {
				$where[]  = 'b.banner_id = ?';
				$params[] = $exp['banner_id'];
			}
			if (isset($exp['status'])) {
				$where[]  = 'b.status = ?';
				$params[] = $exp['status'];
			}
			if (isset($exp['keyword'])) {
				$where[] = "b.name LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql .= ' ORDER BY banner_id DESC';
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
	
	public function count($exp = null) 
	{
		$sql = 'SELECT COUNT(*) AS num_banners FROM ' . $this->_prefix . 'ad_banner AS b';
		$params = array();
		if ($exp) {
			$where = array();
			
			if (isset($exp['route'])) {
				$sql 	 .= ' INNER JOIN ' . $this->_prefix . 'ad_page_assoc AS pa ON b.banner_id = pa.banner_id';
				$where[]  = 'pa.route = ?';
				$params[] = $exp['route'];
			}
			if (isset($exp['banner_id'])) {
				$where[]  = 'b.banner_id = ?';
				$params[] = $exp['banner_id'];
			}
			if (isset($exp['status'])) {
				$where[]  = 'b.status = ?';
				$params[] = $exp['status'];
			}
			if (isset($exp['keyword'])) {
				$where[] = "b.name LIKE '%" . addslashes($exp['keyword']) . "%'";
			}
			
			if (count($where) > 0) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}
		}
		$sql  = $this->_conn->limit($sql, 1);
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch();
		$stmt->closeCursor();
		return $row->num_banners;
	}
	
	public function delete($id) 
	{
		$sql  = 'DELETE FROM ' . $this->_prefix . 'ad_banner WHERE banner_id = ?'; 
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function updateStatus($id, $status) 
	{
		$sql  = 'UPDATE ' . $this->_prefix . 'ad_banner 
				SET status = ?
				WHERE banner_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($status, $id));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;	
	}
}
