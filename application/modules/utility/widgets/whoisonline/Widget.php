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
 * @version 	$Id: Widget.php 5394 2010-09-12 16:05:40Z huuphuoc $
 * @since		2.0.9
 */

class Utility_Widgets_WhoIsOnline_Widget extends Tomato_Widget 
{
	const COOKIE_GEO_DATA = 'Utility_Widgets_WhoIsOnline_Widget';
	
	protected function _prepareShow() 
	{
		$ip 	  = $this->_request->getClientIp();
		$userId   = Zend_Auth::getInstance()->hasIdentity() 
					? Zend_Auth::getInstance()->getIdentity()->user_id : null;
		$userName = Zend_Auth::getInstance()->hasIdentity() 
					? Zend_Auth::getInstance()->getIdentity()->user_name : null;
		$visit    = new Utility_Widgets_WhoIsOnline_Models_Visit(array(
							'ip' 		   => $ip,
							'access_time'  => null,
							'country' 	   => null,
							'country_code' => null,
							'user_id' 	   => $userId,
							'user_name'    => $userName,
						));
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$visitDao = Tomato_Model_Dao_Factory::getInstance()->setWidget($this)->getVisitDao();
		$visitDao->setDbConnection($conn);
		
		$isOnline = $visitDao->isOnline($ip);
		
		if (!$isOnline) {
			if (isset($_COOKIE[self::COOKIE_GEO_DATA])) {
				 list($countryName, $countryCode) = explode('|', strip_tags($_COOKIE[self::COOKIE_GEO_DATA]));				
			} else {
				$xml = simplexml_load_file('http://api.hostip.info/?ip=' . $ip);
				$xml->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
				
				$hostIp = $xml->xpath('/HostipLookupResultSet/gml:featureMember/Hostip');
				$countryName = (string) $hostIp[0]->countryName;
				$countryCode = (string) $hostIp[0]->countryAbbrev;
				
				/**
				 * Stores the country name and code in cookie for 30 days
				 */
				setcookie(self::COOKIE_GEO_DATA, implode('|', array($countryName, $countryCode)), time() + 30 * 24 * 60 * 60);
			} 
			
			$visit->access_time	 = date('Y-m-d H:i:s');
			$visit->country 	 = $countryName;
			$visit->country_code = $countryCode;
			
			$visitDao->add($visit);
		} else {
			$visitDao->update($visit);
		}
		
		$timeout = 10 * 60;
		$visitDao->deleteByTime($timeout);
		
		/**
		 * Count the number of visitors
		 */
		$numGuests = $visitDao->count(false);
		$numUsers  = $visitDao->count(true);
		$users     = $visitDao->getOnlineUsers();
		
		$this->_view->assign('numGuests', $numGuests);
		$this->_view->assign('numUsers', $numUsers);
		$this->_view->assign('total', $numGuests + $numUsers);
		$this->_view->assign('users', $users);
	}
}
