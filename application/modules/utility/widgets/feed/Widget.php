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
 * @version 	$Id: Widget.php 5072 2010-08-29 04:33:29Z huuphuoc $
 * @since		2.0.0
 */

class Utility_Widgets_Feed_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$url   = $this->_request->getParam('url');
		$title = $this->_request->getParam('title');
		
		$entries = Zend_Feed::import($url);
    	$limit   = $this->_request->getParam('limit', count($entries));
    	$index = 0;
    	$items = array();
    	foreach ($entries as $entry) {
    		$index++;
    		if ($index > $limit) {
    			break;
    		} else {
    			$items[] = $entry;
    		}
    	}
    	
		$title = ($title == null || $title == '') ? $entries->title() : $title;

		$this->_view->assign('entries', $items);
		$this->_view->assign('limit', $limit);
		$this->_view->assign('title', $title);
		
		/**
		 * @since 2.0.8
		 */
		$this->_view->assign('dateFormat', array(
			'DAY' 			=> $this->_view->translator()->widget('diff_day_format'),
			'DAY_HOUR'		=> $this->_view->translator()->widget('diff_day_hour_format'),
			'HOUR' 			=> $this->_view->translator()->widget('diff_hour_format'),
			'HOUR_MINUTE' 	=> $this->_view->translator()->widget('diff_hour_minute_format'),
			'MINUTE' 		=> $this->_view->translator()->widget('diff_minute_format'),
			'MINUTE_SECOND'	=> $this->_view->translator()->widget('diff_minute_second_format'),
			'SECOND'		=> $this->_view->translator()->widget('diff_second_format'),
		));
	}
}
