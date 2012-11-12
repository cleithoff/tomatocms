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
 * @version 	$Id: Translation.php 5478 2010-09-20 09:59:05Z huuphuoc $
 * @since		2.0.8
 */

/**
 * Represents a translation item
 */
class Core_Models_Translation extends Tomato_Model_Entity 
{
	protected $_properties = array(
		'translation_id'  => null,		/** Id of translation */
		'item_id' 	      => null,		/** Id of new item */
		'item_class'      => null,		/** Name of source class */
		'source_item_id'  => null,		/** Id of source item */
		'language'        => null,		/** Language of new item */
		'source_language' => null,		/** Language of source item */
	);
}
