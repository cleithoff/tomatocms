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
 * @version 	$Id: Translation.php 4921 2010-08-25 02:57:17Z huuphuoc $
 * @since		2.0.8
 */

class Core_Models_Dao_Sqlsrv_Translation extends Tomato_Model_Dao
	implements Core_Models_Interface_Translation
{
	public function convert($entity)
	{
		return new Core_Models_Translation($entity); 
	}
	
	public function add($translation)
	{
		$this->_conn->insert($this->_prefix . 'core_translation', array(
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
		$sql  = 'UPDATE ' . $this->_prefix . 'core_translation
				SET source_item_id = ?, language = ?, source_language = ?
				WHERE item_id = ? AND item_class = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array(
			$translation->source_item_id,
			$translation->language,
			$translation->source_language,
			$translation->item_id,
			$translation->item_class,
		));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function delete($id, $class)
	{
		$id   = $this->_conn->quote($id);
		
		$sql  = 'UPDATE ' . $this->_prefix . 'core_translation
				SET source_item_id = item_id, source_language = NULL
				WHERE source_item_id = ? AND item_class = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id, $class));
		
		$sql  = 'DELETE FROM ' . $this->_prefix . 'core_translation WHERE item_id = ? AND item_class = ?';
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute(array($id, $class));
		$numRows = $stmt->rowCount();
		$stmt->closeCursor();
		return $numRows;
	}
	
	public function getItems($id, $class, $lang = null)
	{
		$id     = $this->_conn->quote($id);
		$sql    = 'SELECT tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language
					FROM ' . $this->_prefix . 'core_translation AS tr1
					INNER JOIN ' . $this->_prefix . 'core_translation AS tr2
						ON (tr1.item_id = ? AND tr1.source_item_id = tr2.item_id)
						OR (tr2.item_id = ? AND tr1.item_id = tr2.source_item_id)
						OR (tr1.source_item_id = ? AND tr1.source_item_id = tr2.source_item_id)
					WHERE tr1.item_class = ?';
		$params = array((int)$id, (int)$id, (int)$id, $class);
		if ($lang != null) {
			$sql     .= ' AND tr1.language = ?';
			$params[] = $lang; 
		}
		$sql     .= ' AND tr2.item_class = ? 
					GROUP BY tr1.translation_id, tr1.item_id, tr1.item_class, tr1.source_item_id, tr1.language, tr1.source_language';
		$params[] = $class;
		$stmt = $this->_conn->prepare($sql);
		$stmt->execute($params);
		$rows = $stmt->fetchAll();
		$stmt->closeCursor();
		return new Tomato_Model_RecordSet($rows, $this);
	}
}
	