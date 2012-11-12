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
 * @version 	$Id: TrackController.php 3968 2010-07-25 09:35:19Z huuphuoc $
 * @since		2.0.0
 */

class Ad_TrackController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Redirect to destination page after clicking on banner
	 * 
	 * @return void
	 */
	public function redirectAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request  = $this->getRequest();
		$bannerId = $request->getParam('bannerId');
		$zoneId   = $request->getParam('zoneId');
		$gotoUrl  = $request->getParam('clickUrl');
		$pageId   = $request->getParam('pageId');
		$ip 	  = $request->getClientIp();
		$fromUrl  = $request->getServer('HTTP_REFERER');
		
		$track = new Ad_Models_Track(array(
			'banner_id'    => $bannerId,
			'zone_id' 	   => $zoneId,
			'page_id' 	   => $pageId,
			'clicked_date' => date('Y-m-d H:i:s'),
			'ip' 		   => $ip,
			'from_url' 	   => $fromUrl,
		));
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$trackDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getTrackDao();
		$trackDao->setDbConnection($conn);
		$trackDao->add($track);
//		$this->_redirect($gotoUrl);

		/**
		 * Use javascript redirect to support link that have format mailto
		 */
		echo '<script type="text/javascript">window.location="' . addslashes($gotoUrl) . '"</script>';
	}
}
