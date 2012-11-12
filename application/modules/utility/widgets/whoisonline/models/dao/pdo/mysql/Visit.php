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
 * @version 	$Id: Visit.php 5395 2010-09-12 17:03:03Z huuphuoc $
 * @since		2.0.9
 */

class Utility_Widgets_WhoIsOnline_Models_Dao_Pdo_Mysql_Visit extends Tomato_Model_Dao
	implements Utility_Widgets_WhoIsOnline_Models_Interface_Visit
{
	
	public function convert($entity) 
	{
		return new Utility_Widgets_WhoIsOnline_Models_Visit($entity);
	}
	
	public function isOnline($ip)
	{
		$numVisits = $this->_conn
						->select()
						->from($this->_prefix . 'utility_whoisonline_visit', array('num_visits' => 'COUNT(visit_id)'))
						->where('ip = ?', $ip)
						->limit(1)
						->query()
						->fetch()
						->num_visits;
		return ($numVisits > 0);
	}
	
	public function add($visit)
	{
		$this->_conn->insert($this->_prefix . 'utility_whoisonline_visit',
							array(
								'ip' 		   => $visit->ip,
								'access_time'  => $visit->access_time,
								'country' 	   => $visit->country,
								'country_code' => $visit->country_code,
								'user_id' 	   => $visit->user_id,
								'user_name'    => $visit->user_name,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'utility_whoisonline_visit');
	}
	
	public function update($visit)
	{
		return $this->_conn->update($this->_prefix . 'utility_whoisonline_visit',
							array(
								'access_time' => new Zend_Db_Expr('NOW()'),
								'user_id' 	  => $visit->user_id,
								'user_name'   => $visit->user_name,
							),
							array(
								'ip = ?' => $visit->ip,
							));
	}
	
	public function deleteByTime($time)
	{
		if (is_int($time)) {
			return $this->_conn->delete($this->_prefix . 'utility_whoisonline_visit',
								array(
									'access_time < ?' => date('Y-m-d H:i:s', strtotime('-' . $time . 'seconds')),
								));
		}
		return 0;
	}
	
	public function count($isRegistered)
	{
		$select = $this->_conn
						->select()
						->from($this->_prefix . 'utility_whoisonline_visit', array('num_visits' => 'COUNT(visit_id)'));
		if ($isRegistered === true) {
			$select->where('user_id IS NOT NULL');
		} elseif ($isRegistered === false) {
			$select->where('user_id IS NULL');
		}
		return $select->limit(1)->query()->fetch()->num_visits;
	}
	
	public function getOnlineUsers()
	{
		$rs = $this->_conn
					->select()
					->from($this->_prefix . 'utility_whoisonline_visit')
					->where('user_id IS NOT NULL')
					->query()
					->fetchAll();
		return (null == $rs) ? null : new Tomato_Model_RecordSet($rs, $this);
	}
}
