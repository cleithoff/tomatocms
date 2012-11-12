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
 * @version 	$Id: Log.php 5031 2010-08-28 17:26:40Z huuphuoc $
 * @since		2.0.7
 */

class Core_Models_Dao_Pdo_Sqlsrv_Log extends Tomato_Model_Dao 
	implements Core_Models_Interface_Log
{
	public function convert($entity)
	{
		return new Core_Models_Log($entity); 
	}
	
	public function add($log)
    {
  		$this->_conn->insert($this->_prefix . 'core_log', array(
    		'created_date' => (string)$log->created_date,
  			'uri'		   => (string)$log->uri,
    		'module'       => (string)$log->module,
    		'controller'   => (string)$log->controller,
    		'action' 	   => (string)$log->action,
    		'class' 	   => (string)$log->class,
  			'file' 	       => (string)$log->file,
  			'line' 	   	   => (string)$log->line,
    		'message' 	   => (string)$log->message,
  			'trace'		   => (string)$log->trace,
		));
		return $this->_conn->lastInsertId($this->_prefix . 'core_log');
    }
    
	public function count()
	{
		$sql  = 'SELECT COUNT(*) AS num_logs FROM ' . $this->_prefix . 'core_log'; 
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$num_logs = $stmt->fetch()->num_logs;
		$stmt->closeCursor();
		return $num_logs;
	}
    
    public function delete($id)
    {
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_log WHERE log_id = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id));
		$numRows = $stmt->rowCount(); 
		$stmt->closeCursor();
		return $numRows;
		
    }

	public function find($offset = null, $count = null)
	{
		$sql = 'SELECT * FROM ' . $this->_prefix . 'core_log';	
		if (is_int($offset) && is_int($count)) {
			$sql = $this->_conn->limit($sql, $count, $offset);
		}
		$sql .= ' ORDER BY log_id DESC';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
