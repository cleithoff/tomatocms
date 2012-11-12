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
 * @version 	$Id: Log.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.7
 */

class Core_Models_Dao_Pdo_Mysql_Log extends Tomato_Model_Dao 
	implements Core_Models_Interface_Log
{
	public function convert($entity)
	{
		return new Core_Models_Log($entity); 
	}
	
	public function add($log)
    {
  		$this->_conn->insert($this->_prefix . 'core_log', 
					  		array(
					    		'created_date' => $log->created_date,
					  			'uri'		   => $log->uri,
					    		'module'       => $log->module,
					    		'controller'   => $log->controller,
					    		'action' 	   => $log->action,
					    		'class' 	   => $log->class,
					  			'file' 	       => $log->file,
					  			'line' 	   	   => $log->line,
					    		'message' 	   => $log->message,
					  			'trace'		   => $log->trace,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_log');
    }
    
	public function count()
	{
		return $this->_conn
					->select()
					->from($this->_prefix . 'core_log', array('num_logs' => 'COUNT(*)'))
					->query()
					->fetch()
					->num_logs;
	}
    
    public function delete($id)
    {
		return $this->_conn->delete($this->_prefix . 'core_log', 
									array(
										'log_id = ?' => $id,
									));
    }

	public function find($offset = null, $count = null)
	{
		$select = $this->_conn
						->select()
						->from($this->_prefix . 'core_log')
						->order('log_id DESC');
		if (is_int($offset) && is_int($count)) {
			$select->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
}
