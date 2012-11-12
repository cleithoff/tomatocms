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
 * @version 	$Id: SetController.php 5099 2010-08-29 20:09:17Z huuphuoc $
 * @since		2.0.0
 */

class Multimedia_SetController extends Zend_Controller_Action 
{
	/**
     * Init controller
     * 
     * @return void
	 */
	public function init() 
	{
		/**
		 * Register hooks
		 * @since 2.0.2
		 */
		Tomato_Hook_Registry::getInstance()->register('Multimedia_Set_Add_ShowSidebar', array(
			new Tag_Hooks_Tagger_Hook(), 
			'show',
			array('set_id', 'multimedia_set_details', 'multimedia_tag_set')
		));
		Tomato_Hook_Registry::getInstance()->register('Multimedia_Set_Edit_ShowSidebar', array(
			new Tag_Hooks_Tagger_Hook(),
			'show',
			array('set_id', 'multimedia_set_details', 'multimedia_tag_set')
		));
		Tomato_Hook_Registry::getInstance()->register(
			'Multimedia_Set_Add_Success',
			'Tag_Hooks_Tagger_Hook::add'
		);
		Tomato_Hook_Registry::getInstance()->register(
			'Multimedia_Set_Edit_Success',
			'Tag_Hooks_Tagger_Hook::add'
		);
	}	
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * View set details
	 * 
	 * @since 2.0.2
	 * @return void
	 */
	public function detailsAction() 
	{
		$request   = $this->getRequest();
		$setId 	   = $request->getParam('set_id');
		$pageIndex = $request->getParam('page_index', 1);
		$perPage = 15;
		$offset	 = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$setDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$setDao->setDbConnection($conn);
		$fileDao->setDbConnection($conn);
		
		$set = $setDao->getById($setId);
		if (null == $set) {
			throw new Tomato_Exception_NotFound();
		}
		
		/**
		 * Get the list of file that belongs to this set
		 */
		$files 	  = $fileDao->getFilesInSet($setId, $offset, $perPage, true);
		$numFiles = $fileDao->countFilesInSet($setId, true);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($files, $numFiles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('set', $set);
		$this->view->assign('files', $files);
		
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url($set->getProperties(), 'multimedia_set_details'),
			'itemLink' => 'page-%d',
		));
	}
	
	/* ========== Backend actions =========================================== */

	/**
	 * Activate set
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
			
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$setDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
			$setDao->setDbConnection($conn);
			$setDao->toggleStatus($id);
			
			$this->getResponse()->setBody(1 - $status);
		}
	}
	
	/**
	 * Add new set
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$request = $this->getRequest();
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$setDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$setDao->setDbConnection($conn);
		$fileDao->setDbConnection($conn);
		
		if ($request->isPost()) {
			$user 		 = Zend_Auth::getInstance()->getIdentity();			
			$title 		 = $request->getPost('title');
			$description = $request->getPost('description');
			$setImage 	 = $request->getPost('setImage');
			$imageUrls 	 = Zend_Json::decode($setImage);
			$photos 	 = $request->getPost('photos');
			
			$set = new Multimedia_Models_Set(array(
				'title' 			=> $title,
				'slug' 				=> Tomato_Utility_String::removeSign($title, '-', true),
				'description' 		=> $description,
				'created_date' 		=> date('Y-m-d H:i:s'),
				'created_user_id' 	=> $user->user_id,
				'created_user_name' => $user->user_name,
				'is_active' 		=> true,
			));
			if (null != $imageUrls) {
				$set->image_square 	  = $imageUrls['square'];
				$set->image_thumbnail = $imageUrls['thumbnail'];
				$set->image_small 	  = $imageUrls['small'];
				$set->image_crop 	  = $imageUrls['crop'];
				$set->image_medium 	  = $imageUrls['medium'];
				$set->image_large 	  = $imageUrls['large'];
			}
			$setId = $setDao->add($set);
			
			if ($setId > 0) {
				if ($photos != null && is_array($photos)) {
					$fileIds = array();	
					foreach ($photos as $photo) {	
						$photo = stripslashes($photo);						
						$images = Zend_Json::decode($photo);
						if ($images['file_id']) {
							$fileId = $images['file_id'];
						} else {
							$file = new Multimedia_Models_File(array(
								'image_square' 		=> $images['square'],
								'image_thumbnail' 	=> $images['thumbnail'],
								'image_small' 		=> $images['small'],
								'image_crop' 		=> $images['crop'],
								'image_medium' 		=> $images['medium'],								
								'image_large' 		=> $images['large'],
								'image_original' 	=> $images['original'],
								'created_date' 		=> date('Y-m-d H:i:s'),
								'created_user' 		=> $user->user_id,
								'created_user_name' => $user->user_name,
								'url' 				=> $images['original'],
								'file_type' 		=> 'image',				
								'is_active' 		=> true,
							));				
							$fileId = $fileDao->add($file);
						}
						if (!in_array($fileId, $fileIds)) {
							$fileIds[] = $fileId;
							$fileDao->addToSet($setId, $fileId);
						}
					}
				}
				
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Hook_Registry::getInstance()->executeAction('Multimedia_Set_Add_Success', $setId);
				
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('set_add_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'multimedia_set_add'));
			}
		}
	}
	
	/**
	 * Delete set
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
			$setDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
			$setDao->setDbConnection($conn);			
			$setDao->delete($id);
		}
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Edit set
	 * 
	 * @return void
	 */
	public function editAction() 
	{			
		$request = $this->getRequest();
		$setId 	 = $request->getParam('set_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$setDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$setDao->setDbConnection($conn);
		$fileDao->setDbConnection($conn);	
		
		$set 	   = $setDao->getById($setId);
		$oldPhotos = $fileDao->getFilesInSet($setId);
		
		$this->view->assign('set', $set);
		$this->view->assign('oldPhotos', $oldPhotos);	
			
		if ($request->isPost()) {
			$keySection = $request->getPost('keySection');
			$newValue 	= $request->getPost('value');
			if ($keySection && $newValue) {
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				
				$arr = explode('_', $keySection);			
				switch ($arr[0]) {
					case 'title':
						$setDao->updateDescription($arr[1], $newValue);
						break;
					case 'description':
						$setDao->updateDescription($arr[1], null, $newValue);
						break;
				}
				$this->getResponse()->setBody('RESULT_OK');
			} else {
				$user 		 = Zend_Auth::getInstance()->getIdentity();
				$title 		 = $request->getPost('title');
				$description = $request->getPost('description');
				$setImage 	 = $request->getPost('setImage');
				$imageUrls 	 = Zend_Json::decode($setImage);
				$photos 	 = $request->getPost('photos');
				
				$set = new Multimedia_Models_Set(array(
					'set_id' 			=> $setId,	
					'title' 			=> $title,
					'slug' 				=> Tomato_Utility_String::removeSign($title, '-', true),
					'description' 		=> $description,
					'created_date' 		=> date('Y-m-d H:i:s'),
					'created_user_id' 	=> $user->user_id,
					'created_user_name' => $user->user_name,
					'is_active' 		=> true,
				));
				if (null != $imageUrls) {
					$set->image_square 	  = $imageUrls['square'];
					$set->image_thumbnail = $imageUrls['thumbnail'];
					$set->image_small 	  = $imageUrls['small'];
					$set->image_crop 	  = $imageUrls['crop'];
					$set->image_medium 	  = $imageUrls['medium'];
					$set->image_large 	  = $imageUrls['large'];
				}
				$setDao->update($set);
				if ($setId > 0) {
					$fileDao->removeFromSet($setId);
									
					if ($photos != null && is_array($photos)) {
						$fileIds = array();					
						foreach ($photos as $photo) {
							$images = Zend_Json::decode($photo);
							if ($images['file_id']) {
								$fileId = $images['file_id'];
							} else {
								$file = new Multimedia_Models_File(array(
									'image_square' 		=> $images['square'],
									'image_thumbnail' 	=> $images['thumbnail'],
									'image_small' 		=> $images['small'],
									'image_crop' 		=> $images['crop'],
									'image_medium' 		=> $images['medium'],									
									'image_large' 		=> $images['large'],
									'image_original' 	=> $images['original'],
									'created_date' 		=> date('Y-m-d H:i:s'),
									'created_user' 		=> $user->user_id,
									'created_user_name' => $user->user_name,
									'url' 				=> $images['original'],
									'file_type' 		=> 'image',				
									'is_active' 		=> true,
								));
								$fileId = $fileDao->add($file);
							}
							if (!in_array($fileId, $fileIds)) {
								$fileIds[] = $fileId;
								$fileDao->addToSet($setId, $fileId);
							}
						}
					}
					
					/**
					 * Execute hooks
					 * @since 2.0.2
					 */
					Tomato_Hook_Registry::getInstance()->executeAction('Multimedia_Set_Edit_Success', $setId);
					
					$this->_helper->getHelper('FlashMessenger')
						->addMessage($this->view->translator('set_edit_success'));
					$this->_redirect($this->view->serverUrl() . $this->view->url(array('set_id' => $setId), 'multimedia_set_edit'));
				}
			}
		}
	}
	
	/**
	 * List sets
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$setDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
		$setDao->setDbConnection($conn);

		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage = 30;
		$offset	 = ($pageIndex - 1) * $perPage;
		
		/**
		 * Build photo search expression
		 */
		$user 	= Zend_Auth::getInstance()->getIdentity();
		$params = null;
		$exp	= array(
			'created_user' => $user->user_id
		);
		
		if ($request->isPost()) {
			$keyword 	= $request->getPost('keyword');
			$findMySets = $request->getPost('findMySets');
			if ($keyword) {
				$exp['keyword'] = strip_tags($keyword);
			}
			if (null == $findMySets) {
				$exp['created_user'] = null;
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
		
		$sets 	 = $setDao->find($offset, $perPage, $exp);
		$numSets = $setDao->count($exp);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($sets, $numSets));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', array(
			'path' 	   => $this->view->url(array(), 'multimedia_set_list'),
			'itemLink' => (null == $params) ? 'page-%d' : 'page-%d?q=' . $params,
		));
		
		$this->view->assign('numSets', $numSets);
		$this->view->assign('sets', $sets);
		$this->view->assign('exp', $exp);
	}
}
