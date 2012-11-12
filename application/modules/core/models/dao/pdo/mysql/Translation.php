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
 * @version 	$Id: Translation.php 5336 2010-09-07 08:15:47Z huuphuoc $
 * @since		2.0.8
 */

class Core_Models_Dao_Pdo_Mysql_Translation extends Tomato_Model_Dao
	implements Core_Models_Interface_Translation
{
	public function convert($entity)
	{
		return new Core_Models_Translation($entity); 
	}
	
	public function add($translation)
	{
		$this->_conn->insert($this->_prefix . 'core_translation', 
							array(
								'item_id' 	      => $translation->item_id,
								'item_class'      => $translation->item_class,
								'source_item_id'  => $translation->source_item_id,
								'language'        => $translation->language,
								'source_language' => $translation->source_language,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'core_translation');
	}
	
	public function update($translation)
	{
		return $this->_conn->update($this->_prefix . 'core_translation', 
									array(
										'source_item_id'  => $translation->source_item_id,
										'language'        => $translation->language,
										'source_language' => $translation->source_language,
									), 
									array(
										'item_id = ?'    => $translation->item_id,
										'item_class = ?' => $translation->item_class, 
									));
	}
	
	public function delete($id, $class)
	{
		$this->_conn->update($this->_prefix . 'core_translation', 
							array(
								'source_item_id'  => new Zend_Db_Expr('item_id'),
								'source_language' => null,
							),
							array(
								'source_item_id = ?' => $id,
								'item_class = ?'     => $class,
							));
				
		return $this->_conn->delete($this->_prefix . 'core_translation', 
									array(
										'item_id = ?'    => $id,
										'item_class = ?' => $class,
									));
	}
	
	public function getItems($id, $class, $lang = null)
	{
		$select = $this->_conn
						->select()
						->from(array('tr1' => $this->_prefix . 'core_translation'))
						->joinInner(array('tr2' => $this->_prefix . 'core_translation'),
									'(tr1.item_id = ? AND tr1.source_item_id = tr2.item_id)
									OR (tr2.item_id = ? AND tr1.item_id = tr2.source_item_id)
									OR (tr1.source_item_id = ? AND tr1.source_item_id = tr2.source_item_id)',			
									array())
						->where('tr1.item_class = ?', $class);
		if ($lang != null) {
			$select->where('tr1.language = ?', $lang);
		}
		$rs = $select->where('tr2.item_class = ?', $class)
					->group('tr1.translation_id')
					->bind(array($id, $id, $id))
					->query()
					->fetchAll();
		return new Tomato_Model_RecordSet($rs, $this);
	}
}
	