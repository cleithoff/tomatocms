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
 * @version 	$Id: Sorter.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Sort the layout config by its priority
 */
class Tomato_Layout_Sorter 
{
	private $_layouts;
	private $_ascending;
	
	const PRIORITY_KEY = 'priority';

	public function __construct($layouts) 
	{
		$this->_layouts = $layouts;
	}

	public function sortByPriority($ascending = true) 
	{
		$this->_ascending = $ascending;
		$array = $this->_layouts; 
		uksort($array, array($this, 'cmp'));
		return $array;
	}

	public function cmp($a, $b) 
	{
		if (!is_array($a) && !is_array($b)) {
	 		$a = $this->_layouts[$a][self::PRIORITY_KEY];
	 		$b = $this->_layouts[$b][self::PRIORITY_KEY];
	 	}
	 	if (!ctype_digit($a) && !ctype_digit($b)) {
	 		if ($this->_ascending) {
	 			return strcasecmp($b, $a);
	 		} else {
	 			return strcasecmp($a, $b);
	 		}
	 	} else {
	 		if (intval($a) == intval($b)) {
	 			return 0;	
	 		}
	 		if ($this->_ascending) {
	 			return (intval($a) > intval($b)) ? 1 : -1;
	 		} else {
	 			return (intval($a) > intval($b)) ? -1 : 1;
	 		}
	 	}
	}
}
