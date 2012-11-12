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
 * @version 	$Id: Log.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.7
 */

class Core_Models_Dao_Mysql_Log extends Tomato_Model_Dao 
	implements Core_Models_Interface_Log
{
	public function convert($entity)
	{
		return new Core_Models_Log($entity); 
	}
	
	public function add($log)
    {
    	$sql = sprintf("INSERT INTO " . $this->_prefix . "core_log (created_date, uri, module, controller, action, class, file, line, message, trace)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						mysql_real_escape_string($log->created_date),
						mysql_real_escape_string($log->uri),
						mysql_real_escape_string($log->module),
						mysql_real_escape_string($log->controller),
						mysql_real_escape_string($log->action),
						mysql_real_escape_string($log->class),
						mysql_real_escape_string($log->file),
						mysql_real_escape_string($log->line),
						mysql_real_escape_string($log->message),
						mysql_real_escape_string($log->trace));
		mysql_query($sql);
		return mysql_insert_id();
    }
    
	public function count()
	{
		$sql = "SELECT COUNT(*) AS num_logs FROM " . $this->_prefix . "core_log LIMIT 1";
		$rs  = mysql_query($sql);
		$row = mysql_fetch_object($rs);
		mysql_free_result($rs);
		return $row->num_logs;
	}
    
    public function delete($id)
    {
    	$sql = sprintf("DELETE FROM " . $this->_prefix . "core_log WHERE log_id = '%s'", 
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();
    }

	public function find($offset = null, $count = null)
	{
		$sql = "SELECT * FROM " . $this->_prefix . "core_log
				ORDER BY log_id DESC";
		if (is_int($offset) && is_int($count)) {
			$sql .= sprintf(" LIMIT %s, %s", $offset, $count);
		}
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
