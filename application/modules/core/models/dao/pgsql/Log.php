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
 * @version 	$Id: Log.php 5437 2010-09-15 06:36:08Z leha $
 * @since		2.0.7
 */

class Core_Models_Dao_Pgsql_Log extends Tomato_Model_Dao 
	implements Core_Models_Interface_Log
{
	public function convert($entity)
	{
		return new Core_Models_Log($entity); 
	}
	
	public function add($log)
    {
    	$sql = sprintf("INSERT INTO " . $this->_prefix . "core_log (created_date, uri, module, controller, action, class, file, line, message, trace)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s') 
						RETURNING log_id",
						pg_escape_string($log->created_date),
						pg_escape_string($log->uri),
						pg_escape_string($log->module),
						pg_escape_string($log->controller),
						pg_escape_string($log->action),
						pg_escape_string($log->class),
						pg_escape_string($log->file),
						pg_escape_string($log->line),
						pg_escape_string($log->message),
						pg_escape_string($log->trace));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		return $row->log_id;
    }
    
	public function count()
	{
		$sql = 'SELECT COUNT(*) AS num_logs FROM ' . $this->_prefix . 'core_log';
						
		$rs   = pg_query($sql);
		$row  = pg_fetch_object($rs);
		pg_free_result($rs);
		return $row->num_logs;	
	}
    
    public function delete($id)
    {
		return pg_delete($this->_conn, $this->_prefix . 'core_log', 
						array(
							'log_id' => $id,
						));
    }

	public function find($offset = null, $count = null)
	{
		$sql  = 'SELECT * FROM ' . $this->_prefix . 'core_log ORDER BY log_id DESC';
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
}
