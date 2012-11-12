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
 * @version 	$Id: BannerController.php 4534 2010-08-12 09:50:52Z huuphuoc $
 * @since		2.0.0
 */

class Ad_BannerController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * Activate a banner
	 * 
	 * @return void
	 */
	public function activateAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$id 	= $request->getPost('id');
			$status = $request->getPost('status');
			$status = ($status == 'inactive') ? 'active' : 'inactive';
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$bannerDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerDao();
			$bannerDao->setDbConnection($conn);
			$bannerDao->updateStatus($id, $status);
			
			$this->getResponse()->setBody($status);			
		}
	}
	
	/**
	 * Add new banner
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$zoneDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
		$clientDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getClientDao();
		$bannerDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerDao();
		$bannerPageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerPageAssocDao();
		$pageDao->setDbConnection($conn);
		$zoneDao->setDbConnection($conn);
		$clientDao->setDbConnection($conn);
		$bannerDao->setDbConnection($conn);
		$bannerPageDao->setDbConnection($conn);
		
		$pages 	 = $pageDao->getOrdered();
		$zones 	 = $zoneDao->getZones();
		$clients = $clientDao->getClients();
		
		$this->view->assign('pages', $pages);
		$this->view->assign('zones', $zones);
		$this->view->assign('clients', $clients);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$text 		 = $request->getPost('text');
			$startDate 	 = $request->getPost('startDate');
			$expiredDate = $request->getPost('expiredDate');
			$code 		 = $request->getPost('code');
			$clickUrl 	 = $request->getPost('clickUrl');
			$target 	 = $request->getPost('target');
			$format 	 = $request->getPost('format');
			$mode 		 = $request->getPost('mode');
			$timeout 	 = $request->getPost('timeout');
			$status 	 = $request->getPost('status');
			$imageUrl 	 = $request->getPost('imageUrl');
			$clientId 	 = $request->getPost('client');
			$linkItems 	 = $request->getPost('linkItems');			
			
			$banner = new Ad_Models_Banner(array(
				'name' 		   => $name,
				'text' 		   => $text,
				'created_date' => date('Y-m-d H:i:s'),
				'code' 		   => $code,
				'click_url'    => $clickUrl,
				'format' 	   => $format,
				'image_url'    => $imageUrl,
				'mode' 		   => $mode,
				'timeout' 	   => 0,
				'status' 	   => 'inactive',
			));
			if ($timeout) {
				$banner->timeout = $timeout;
			}
			if ($target) {
				$banner->target = $target;
			}
			if ($status) {
				$banner->status = $status;
			}
			if ($clientId) {
				$banner->client_id = $clientId;
			}
			if ($startDate) {
				$banner->start_date = date('Y-m-d', strtotime($startDate));
			}
			if ($expiredDate) {
				$banner->expired_date = date('Y-m-d', strtotime($expiredDate));
			}
			
			$id = $bannerDao->add($banner);
			if ($id > 0 && $linkItems) {
				$linkItems = Zend_Json::decode($linkItems);
				foreach ($linkItems as $link) {
					$bannerPageDao->add(new Ad_Models_BannerPageAssoc(array(
						'route' 	 => $link['route'],
						'page_url' 	 => $link['page_url'],
						'page_title' => $link['page_title'],
						'zone_id' 	 => $link['zone_id'],
						'banner_id'  => $id,
					)));
				}
				
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('banner_add_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'ad_banner_add'));
			}
		}
	}

	/**
	 * Delete banner
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
			$bannerDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerDao();
			$bannerDao->setDbConnection($conn);
			$bannerDao->delete($id);
		}
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Edit banner
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$bannerDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerDao();
		$clientDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getClientDao();
		$pageDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$bannerPageDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerPageAssocDao();
		$zoneDao 	   = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getZoneDao();
		$bannerDao->setDbConnection($conn);
		$clientDao->setDbConnection($conn);
		$pageDao->setDbConnection($conn);
		$bannerPageDao->setDbConnection($conn);
		$zoneDao->setDbConnection($conn);

		$request  = $this->getRequest();
		$bannerId = $request->getParam('banner_id');
		$banner   = $bannerDao->getById($bannerId);
		if (null == $banner) {
			throw new Exception('Not found banner with id of ' . $bannerId);
		}
		
		$clients  = $clientDao->getClients();
		$pages 	  = $pageDao->getOrdered();
		$rsZones  = $zoneDao->getZones();

		$linkItems = $bannerPageDao->getBannerPageAssoc($bannerId);
		$zones = array();
		if ($rsZones) {
			foreach ($rsZones as $row) {
				$zones[$row->zone_id] = $row->name;
			}
		}
		$this->view->assign('banner', $banner);
		$this->view->assign('clients', $clients);
		$this->view->assign('pages', $pages);
		$this->view->assign('zones', $zones);
		$this->view->assign('linkItems', $linkItems);
		
		if ($request->isPost()) {
			$name 		 = $request->getPost('name');
			$text 		 = $request->getPost('text');
			$startDate 	 = $request->getPost('startDate');
			$expiredDate = $request->getPost('expiredDate');
			$code 		 = $request->getPost('code');
			$clickUrl 	 = $request->getPost('clickUrl');
			$target 	 = $request->getPost('target');
			$format 	 = $request->getPost('format');
			$mode 		 = $request->getPost('mode');
			$timeout 	 = $request->getPost('timeout');	
			$status 	 = $request->getPost('status');		
			$imageUrl 	 = $request->getPost('imageUrl');
			$clientId 	 = $request->getPost('client');
			$linkItems 	 = $request->getPost('linkItems');
			
			$banner = new Ad_Models_Banner(array(
				'banner_id' => $bannerId,
				'name' 		=> $name,
				'text' 		=> $text,
				'code' 		=> $code,
				'click_url' => $clickUrl,
				'format' 	=> $format,
				'image_url' => $imageUrl,
				'mode' 		=> $mode,
				'timeout' 	=> 0,
			));
			if ($timeout) {
				$banner->timeout = $timeout;
			}
			if ($target) {
				$banner->target = $target;
			}
			if ($status) {
				$banner->status = $status;
			}
			if ($clientId) {
				$banner->client_id = $clientId;
			}
			if ($startDate) {
				$banner->start_date = date('Y-m-d', strtotime($startDate));
			}
			if ($expiredDate) {
				$banner->expired_date = date('Y-m-d', strtotime($expiredDate));
			}
			$result = $bannerDao->update($banner);
			
			
			$bannerPageDao->removeByBanner($bannerId);
			if ($linkItems) {
				$linkItems = Zend_Json::decode($linkItems);
				foreach ($linkItems as $link) {
					$bannerPageDao->add(new Ad_Models_BannerPageAssoc(array(
						'route' 	 => $link['route'],
						'page_url' 	 => $link['page_url'],
						'page_title' => $link['page_title'],
						'zone_id' 	 => $link['zone_id'],
						'banner_id'  => $bannerId,
					)));
				}
			}
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage($this->view->translator('banner_edit_success'));
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('banner_id' => $bannerId), 'ad_banner_edit'));
		}
	}
	
	/**
	 * List banners
	 * 
	 * @return void
	 */
	public function listAction()
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$pageDao   = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getPageDao();
		$bannerDao = Tomato_Model_Dao_Factory::getInstance()->setModule('ad')->getBannerDao();
		$pageDao->setDbConnection($conn);
		$bannerDao->setDbConnection($conn);
			
		$pages 	   = $pageDao->getOrdered();
		
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage   = 20;
		$offset    = ($pageIndex - 1) * $perPage;
		
		$user   = Zend_Auth::getInstance()->getIdentity();
		$params = null;
		$exp 	= array(
			'keyword'	=> null,
			'banner_id'	=> null,
			'route'	=> null,
			'status'	=> null,
		);
		
		if ($request->isPost()) {
			$id 	  = $request->getPost('bannerId');
			$keyword  = $request->getPost('keyword');
			$route    = $request->getPost('page');
			$status   = $request->getPost('status');
			$purifier = new HTMLPurifier();
			 
			if ($keyword) {
				$exp['keyword'] = $purifier->purify($keyword); 
			}
			if ($id) {
				$exp['banner_id'] = $purifier->purify($id);
			}
			if ($route) {
				$exp['route'] = $route;
			}
			if ($status) {
				$exp['status'] = $status;
			}
			$params = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		} else {
			$params = $request->getParam('q');
			if (null != $params) {
				$exp = rawurldecode(base64_decode($params));
				$exp = Zend_Json::decode($exp); 
			} else {
				$params = rawurlencode(base64_encode(Zend_Json::encode($exp)));
			}
		}
		
		$banners 	= $bannerDao->find($offset, $perPage, $exp);
		$numBanners = $bannerDao->count($exp);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($banners, $numBanners));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);

		$this->view->assign('pages', $pages);
		$this->view->assign('pageIndex', $pageIndex);
		
		$this->view->assign('numBanners', $numBanners);
		$this->view->assign('banners', $banners);
		$this->view->assign('exp', $exp);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'ad_banner_list'),
			'itemLink' => (null == $params) ? 'page-%d' : 'page-%d?q=' . $params,
		));
	}
}
