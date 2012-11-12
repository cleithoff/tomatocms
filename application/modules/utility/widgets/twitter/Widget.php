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
 * @version 	$Id: Widget.php 5073 2010-08-29 05:52:05Z huuphuoc $
 * @since		2.0.0
 */

class Utility_Widgets_Twitter_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$account 	= $this->_request->getParam('account');
    	$limit 		= $this->_request->getParam('limit', 5);
    	$dateFormat = array(
			'DAY' 			=> $this->_view->translator()->widget('diff_day_format'),
			'DAY_HOUR'		=> $this->_view->translator()->widget('diff_day_hour_format'),
			'HOUR' 			=> $this->_view->translator()->widget('diff_hour_format'),
			'HOUR_MINUTE' 	=> $this->_view->translator()->widget('diff_hour_minute_format'),
			'MINUTE' 		=> $this->_view->translator()->widget('diff_minute_format'),
			'MINUTE_SECOND'	=> $this->_view->translator()->widget('diff_minute_second_format'),
			'SECOND'		=> $this->_view->translator()->widget('diff_second_format'),
		);
    	
		if (empty($account)) {
			return;
		}
		
    	$url 	 = 'http://twitter.com/statuses/user_timeline/' . $account . '.json';
    	$updates = Tomato_Utility_HttpRequest::getResponse($url);
		$updates = Zend_Json::decode($updates);
    	$limit 	 = min(array($limit, count($updates)));
    	$items   = array();
    	for ($index = 0; $index < $limit; $index++) {
    		$items[] = $updates[$index];
    	}
    	
    	$this->_view->assign('account', $account);
    	$this->_view->assign('items', $items);
    	$this->_view->assign('dateFormat', $dateFormat);
	}
}
