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
 * @version 	$Id: Translation.php 5028 2010-08-28 16:44:55Z huuphuoc $
 * @since		2.0.8
 */

class Core_Models_Dao_Mysql_Translation extends Tomato_Model_Dao
	implements Core_Models_Interface_Translation
{
	public function convert($entity)
	{
		return new Core_Models_Translation($entity); 
	}
	
	public function add($translation)
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_translation (item_id, item_class, source_item_id, language, source_language)
						VALUES (%s, '%s', %s, '%s', %s)",
						mysql_real_escape_string($translation->item_id), 
						mysql_real_escape_string($translation->item_class), 
						mysql_real_escape_string($translation->source_item_id), 
						mysql_real_escape_string($translation->language), 
						(null == $translation->source_language) ? 'null' : mysql_real_escape_string($translation->source_language));
		mysql_query($sql);
		return mysql_insert_id();
	}
	
	public function update($translation)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_translation
						SET source_item_id = %s, language = '%s', source_language = '%s'
						WHERE item_id = %s AND item_class = '%s'",
						mysql_real_escape_string($translation->source_item_id),
						mysql_real_escape_string($translation->language),
						mysql_real_escape_string((null == $translation->source_language) ? 'null' : $translation->source_language),
						mysql_real_escape_string($translation->item_id),
						mysql_real_escape_string($translation->item_class));
		
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function delete($id, $class)
	{
		$sql = sprintf("UPDATE " . $this->_prefix . "core_translation
						SET source_item_id = item_id, source_language = NULL
						WHERE source_item_id = %s AND item_class = '%s'",
						mysql_real_escape_string($id),
						mysql_real_escape_string($class));
		mysql_query($sql);
		
		$sql = sprintf("DELETE FROM " . $this->_prefix . "core_translation
						WHERE item_id = %s AND item_class = '%s'",
						mysql_real_escape_string($id),
						mysql_real_escape_string($class));
		mysql_query($sql);
		return mysql_affected_rows();
	}
	
	public function getItems($id, $class, $lang = null)
	{
		$sql = sprintf("SELECT tr1.*
						FROM " . $this->_prefix . "core_translation AS tr1
						INNER JOIN " . $this->_prefix . "core_translation AS tr2
							ON (tr1.item_id = '%s' AND tr1.source_item_id = tr2.item_id)
							OR (tr2.item_id = '%s' AND tr1.item_id = tr2.source_item_id)
							OR (tr1.source_item_id = '%s' AND tr1.source_item_id = tr2.source_item_id)
						WHERE tr1.item_class = '%s'",
						mysql_real_escape_string($id),
						mysql_real_escape_string($id),
						mysql_real_escape_string($id),
						mysql_real_escape_string($class));
						
		if ($lang != null) {
			$sql .= sprintf(" AND tr1.language = '%s'", mysql_real_escape_string($lang));
		}
		$sql .= sprintf(" AND tr2.item_class = '%s'
						GROUP BY tr1.translation_id",
						mysql_real_escape_string($class));
		
		$rs   = mysql_query($sql);
		$rows = array();
		while ($row = mysql_fetch_object($rs)) {
			$rows[] = $row;
		}
		mysql_free_result($rs);
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
	