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
 * @version 	$Id: Tag.php 5484 2010-09-20 09:59:30Z huuphuoc $
 * @since		2.0.2
 */

/**
 * Represents a tag
 */
class Tag_Models_Tag extends Tomato_Model_Entity
{
	protected $_properties = array(
		'tag_id'   => null,		/** Id of tag */
		'tag_text' => null,		/** Tag text */
	);
}
