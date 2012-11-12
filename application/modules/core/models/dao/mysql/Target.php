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
 * @version 	$Id: Target.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Mysql_Target extends Tomato_Model_Dao 
	implements Core_Models_Interface_Target
{
	public function convert($entity)
	{
		return new Core_Models_Target($entity); 
	}
	
	public function getTargets()
	{
		$sql  = "SELECT target_name, hook_module, hook_name, hook_type
				FROM " . $this->_prefix . "core_target
				ORDER BY target_id DESC";
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);						
	}
	
	public function add($target) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_target (target_module, target_name, description, hook_module, hook_name, hook_type)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", 
						mysql_real_escape_string($target->target_module), 
						mysql_real_escape_string($target->target_name),
						mysql_real_escape_string($target->description),
						mysql_real_escape_string($target->hook_module),
						mysql_real_escape_string($target->hook_name),
						mysql_real_escape_string($target->hook_type));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function delete($id)
	{
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_target WHERE target_id = '%s'",
						mysql_real_escape_string($id));
		mysql_query($sql);
		return mysql_affected_rows();	
	}
}
