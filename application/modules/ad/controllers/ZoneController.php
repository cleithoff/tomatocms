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
 * @version 	$Id: ZoneController.php 4514 2010-08-12 09:32:15Z huuphuoc $
 * @since		2.0.0
 */

class Ad_ZoneController extends Zend_Controller_Action 
{
	const KEY = 'AD_ZONE_INIT';
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Load all zones/banners from database
	 * 
	 * @return void
	 */
	public function loadAction() 
	{
		Zend_Registry::set(Tomato_GlobalKey::LOG_REQUEST, false);
		$this->_helper->getHelper('layout')->disableLayout();
		
		$this->getResponse()->setHeader('Content-type', 'application/x-javascript');
//		header('Content-type', 'application/x-javascript');
		
		if (!Zend_Registry::isRegistered(self::KEY) || null == Zend_Registry::get(self::KEY)) {
			/**
			 * Load global banners and zones for the once time
			 */
			Zend_Registry::set(self::KEY, true);
			
			$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
			$zoneDao   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
			$bannerDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerDao();
			$zoneDao->setDbConnection($conn);
			$bannerDao->setDbConnection($conn);
			
			/**
			 * Get the list of zones
			 */
			$zones = $zoneDao->getZones();
			
			/**
			 * Get the list of banners
			 */
			$banners = $bannerDao->loadBanners();
			
			$this->view->assign('zones', $zones);
			$this->view->assign('banners', $banners);
		}
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Add new zone
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$description = $request->getPost('description');
			$width 		 = $request->getPost('width');
			$height 	 = $request->getPost('height');
			
			$zone = new Ad_Models_Zone(array(
				'name' 		  => $name,
				'description' => $description,
				'width' 	  => $width,
				'height' 	  => $height,
			));
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$zoneDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
			$zoneDao->setDbConnection($conn);
			
			$id = $zoneDao->add($zone);
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('zone_add_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'ad_zone_add'));
		}
	}
	
	/**
	 * Check if the zone name has been existed or not
	 * 
	 * @return void
	 */
	public function checkAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request  = $this->getRequest();
		$original = $request->getParam('original');
		$name 	  = $request->getParam('name');
		$result   = false;
		if ($original == null || ($original != null && $name != $original)) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$zoneDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
			$zoneDao->setDbConnection($conn);
			$result = $zoneDao->exist($name);
		}
		($result == true) ? $this->getResponse()->setBody('false') 
						  : $this->getResponse()->setBody('true');
	}
	
	/**
	 * Delete zone
	 * 
	 * @return void
	 */
	public function deleteAction() 
	{
		$this->_helper->getHelper('layout')->disableLayout();
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id = $request->getPost('id');
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$zoneDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
			$zoneDao->setDbConnection($conn);
			$zoneDao->delete($id);
		}
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Edit zone
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request = $this->getRequest();
		$zoneId  = $request->getParam('zone_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$zoneDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
		$zoneDao->setDbConnection($conn);
		$zone = $zoneDao->getById($zoneId);
		
		if (null == $zone) {
			throw new Exception('Not found zone with id of ' . $zoneId);
		}
		
		$this->view->assign('zone', $zone);
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$description = $request->getPost('description');
			$width 		 = $request->getPost('width');
			$height 	 = $request->getPost('height');
			
			$zone = new Ad_Models_Zone(array(
				'zone_id' 	  => $zoneId,
				'name' 		  => $name,
				'description' => $description,
				'width' 	  => $width,
				'height' 	  => $height,
			));
			$result = $zoneDao->update($zone);
			if ($result > 0) {
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('zone_edit_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array('zone_id' => $zoneId), 'ad_zone_edit'));
			}
		}
	}
	
	/**
	 * List zones
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$zoneDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
		$zoneDao->setDbConnection($conn);
		$zones = $zoneDao->getZones();
		$this->view->assign('zones', $zones);
	}
}
