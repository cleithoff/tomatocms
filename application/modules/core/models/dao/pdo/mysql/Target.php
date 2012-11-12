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
 * @version 	$Id: Target.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pdo_Mysql_Target extends Tomato_Model_Dao 
	implements Core_Models_Interface_Target
{
	public function convert($entity)
	{
		return new Core_Models_Target($entity); 
	}
	
	public function getTargets()
	{
		$rs = $this->_conn
					->select()
					->from(array('t' => $this->_prefix . 'core_target'))
					->order('t.target_id DESC')
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);						
	}
	
	public function add($target) 
	{
		$this->_conn->insert($this->_prefix . 'core_target', 
							array(
								'target_module' => $target->target_module,
								'target_name' 	=> $target->target_name,
								'description' 	=> $target->description,
								'hook_module' 	=> $target->hook_module,
								'hook_name' 	=> $target->hook_name,
								'hook_type' 	=> $target->hook_type,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_target');
	}
	
	public function delete($id)
	{
		return $this->_conn->delete($this->_prefix . 'core_target', 
									array(
										'target_id = ?' => $id,
									));
	}
}
