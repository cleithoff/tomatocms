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
 * @version 	$Id: Target.php 5435 2010-09-15 01:57:15Z leha $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Target extends Tomato_Model_Dao 
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
		
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);						
	}
	
	public function add($target) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_target (target_module, target_name, description, hook_module, hook_name, hook_type)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s')
						RETURNING target_id", 
						pg_escape_string($target->target_module), 
						pg_escape_string($target->target_name),
						pg_escape_string($target->description),
						pg_escape_string($target->hook_module),
						pg_escape_string($target->hook_name),
						pg_escape_string($target->hook_type));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->target_id;
	}
	
	public function delete($id)
	{
		return pg_delete($this->_conn, $this->_prefix . 'core_target', 
									array(
										'target_id' => $id,
									));	
	}
}
