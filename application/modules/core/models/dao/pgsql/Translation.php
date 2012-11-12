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
 * @version 	$Id: Translation.php 5436 2010-09-15 01:58:24Z leha $
 * @since		2.0.8
 */

class Core_Models_Dao_Pgsql_Translation extends Tomato_Model_Dao
	implements Core_Models_Interface_Translation
{
	public function convert($entity)
	{
		return new Core_Models_Translation($entity); 
	}
	
	public function add($translation)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_translation (item_id, item_class, source_item_id, language, source_language)
						VALUES (%s, '%s', %s, '%s', '%s')
						RETURNING item_id",
						pg_escape_string($translation->item_id),
						pg_escape_string($translation->item_class),
						pg_escape_string($translation->source_item_id),
						pg_escape_string($translation->language),
						pg_escape_string($translation->source_language));
		$rs  = pg_query($sql);
		$row = pg_fetch_object($rs);
		pg_free_result($rs);		
		return $row->item_id;
	}
	
	public function update($translation)
	{
		return pg_update($this->_conn, $this->_prefix . 'core_translation', 
						array(
							'source_item_id'  => $translation->source_item_id,
							'language'        => $translation->language,
							'source_language' => $translation->source_language,
						), 
						array(
							'item_id'    => $translation->item_id,
							'item_class' => $translation->item_class, 
						));
	}
	
	public function delete($id, $class)
	{
		pg_update($this->_conn, $this->_prefix . 'core_translation', 
							array(
								'source_item_id'  => new Zend_Db_Expr('item_id'),
								'source_language' => null,
							),
							array(
								'source_item_id' => $id,
								'item_class'     => $class,
							));
				
		return pg_delete($this->_conn, $this->_prefix . 'core_translation', 
									array(
										'item_id'    => $id,
										'item_class' => $class,
									));
	}
	
	public function getItems($id, $class, $lang = null)
	{
		$sql = sprintf("SELECT MAX(tr1.item_id) AS item_id, tr1.translation_id, MAX(tr1.item_class) AS item_class, MAX(tr1.source_item_id) AS source_item_id, MAX(tr1.language) AS language, MAX(tr1.source_language) AS source_language
						FROM " . $this->_prefix . "core_translation AS tr1
						INNER JOIN " . $this->_prefix . "core_translation AS tr2
							ON (tr1.item_id = %s AND tr1.source_item_id = tr2.item_id)
							OR (tr2.item_id = %s AND tr1.item_id = tr2.source_item_id)
							OR (tr1.source_item_id = %s AND tr1.source_item_id = tr2.source_item_id)
						WHERE tr1.item_class = '%s'",
						($id) ? pg_escape_string($id) : 'null',
						($id) ? pg_escape_string($id) : 'null',
						($id) ? pg_escape_string($id) : 'null',
						pg_escape_string($class));
						
		if ($lang != null) {
			$sql .= sprintf(" AND tr1.language = '%s'", pg_escape_string($lang));
		}
		$sql .= sprintf(" AND tr2.item_class = '%s'
						GROUP BY tr1.translation_id",
						pg_escape_string($class));
						
		$rs   = pg_query($sql);
		$rows = array();
		while ($row = pg_fetch_object($rs)) {
			$rows[] = $row;
		}
		pg_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
	