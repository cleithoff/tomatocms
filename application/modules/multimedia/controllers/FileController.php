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
 * @version 	$Id: FileController.php 5498 2010-09-22 01:44:46Z huuphuoc $
 * @since		2.0.0
 */

class Multimedia_FileController extends Zend_Controller_Action 
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
		Tomato_Hook_Registry::getInstance()->register('Multimedia_File_Add_ShowSidebar', array(
			new Tag_Hooks_Tagger_Hook(), 
			'show',
			array('file_id', 'multimedia_file_details', 'multimedia_tag_file')
		));
		Tomato_Hook_Registry::getInstance()->register('Multimedia_File_Edit_ShowSidebar', array(
			new Tag_Hooks_Tagger_Hook(),
			'show',
			array('file_id', 'multimedia_file_details', 'multimedia_tag_file')
		));
		Tomato_Hook_Registry::getInstance()->register(
			'Multimedia_File_Add_Success',
			'Tag_Hooks_Tagger_Hook::add'
		);
		Tomato_Hook_Registry::getInstance()->register(
			'Multimedia_File_Edit_Success',
			'Tag_Hooks_Tagger_Hook::add'
		);
	}
	
	/* ========== Frontend actions ========================================== */
	
	/**
	 * View file details
	 * 
	 * @since 2.0.2
	 * @return void
	 */
	public function detailsAction() 
	{
		$request = $this->getRequest();
		$fileId  = $request->getParam('file_id');
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$noteDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getNoteDao();
		$fileDao->setDbConnection($conn);
		$noteDao->setDbConnection($conn);
		
		$file = $fileDao->getById($fileId);
		if (null == $file) {
			throw new Tomato_Exception_NotFound();
		}
		
		/**
		 * Show all notes
		 * @since 2.0.4
		 */
		$notes = $noteDao->find(null, null, array('file_id' => $fileId, 'is_active' => 1));
		
		$this->view->assign('file', $file);
		$this->view->assign('notes', $notes);
	}	
	
	/* ========== Backend actions =========================================== */

	/**
	 * Activate file
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
			$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
			$fileDao->setDbConnection($conn);
			$fileDao->toggleStatus($id);
			
			$this->getResponse()->setBody(1 - $status);
		}
	}
	
	/**
	 * Add new file
	 * 
	 * @return void
	 */
	public function addAction() 
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			$user 		 = Zend_Auth::getInstance()->getIdentity();
			$title 		 = $request->getPost('title');
			$description = $request->getPost('description');
			$image 		 = $request->getPost('image');
			$fileType 	 = $request->getPost('file_type');
			$imageUrls 	 = Zend_Json::decode(stripslashes($image));
			$url 		 = $request->getPost('url');
			$htmlCode 	 = $request->getPost('html_code');
			
			$file = new Multimedia_Models_File(array(
				'title'	 			=> $title,
				'slug' 				=> Tomato_Utility_String::removeSign($title, '-', true),
				'description' 		=> $description,
				'created_date' 		=> date('Y-m-d H:i:s'),
				'created_user' 		=> $user->user_id,
				'created_user_name' => $user->user_name,
				'url' 				=> $url,
				'html_code' 		=> $htmlCode,
				'file_type' 		=> $fileType,		
				'is_active' 		=> true,
			));
			if (null != $imageUrls) {
				$file->image_square    = $imageUrls['square'];
				$file->image_thumbnail = $imageUrls['thumbnail'];
				$file->image_small 	   = $imageUrls['small'];
				$file->image_crop 	   = $imageUrls['crop'];
				$file->image_medium    = $imageUrls['medium'];
				$file->image_large 	   = $imageUrls['large'];
				$file->image_original  = $imageUrls['original'];
			}
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
			$fileDao->setDbConnection($conn);
			$fileId = $fileDao->add($file);
			if ($fileId > 0) {
				/**
				 * Execute hooks
				 * @since 2.0.2
				 */
				Tomato_Hook_Registry::getInstance()->executeAction('Multimedia_File_Add_Success', $fileId);
				
				$this->_helper->getHelper('FlashMessenger')->addMessage(
					$this->view->translator('file_add_success')
				);
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'multimedia_file_add'));
			}
		}
	}
	
	/**
	 * Delete file
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
			$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
			$fileDao->setDbConnection($conn);
			$fileDao->delete($id);
		}
		$this->getResponse()->setBody('RESULT_OK');
	}
	
	/**
	 * Edit file
	 * 
	 * @return void
	 */
	public function editAction() 
	{
		$request = $this->getRequest();
		$fileId  = $request->getParam('file_id');
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$fileDao->setDbConnection($conn);
		
		$file = $fileDao->getById($fileId);
		$imageData = array(
			'image_square' 	  =>  $file->image_square,
			'image_thumbnail' => $file->image_thumbnail,
			'image_small' 	  => $file->image_small,
			'image_crop' 	  => $file->image_crop,
			'image_medium' 	  => $file->image_medium,
			'image_large' 	  => $file->image_large,
		);
					
		$this->view->assign('file', $file);
		$this->view->assign('imageData', rawurlencode(base64_encode(Zend_Json::encode($imageData))));
		
		if ($request->isPost()) {
			$keySection = $request->getPost('keySection');
			$newValue 	= $request->getPost('value');
			if ($keySection && $newValue) {
				$this->_helper->getHelper('viewRenderer')->setNoRender();
				$this->_helper->getHelper('layout')->disableLayout();
				
				$arr = explode('_', $keySection);			
				switch ($arr[0]) {
					case 'title':
						$fileDao->updateDescription($arr[1], $newValue);
						break;
					case 'description':
						$fileDao->updateDescription($arr[1], null, $newValue);
						break;
				}
				$this->getResponse()->setBody('RESULT_OK');
			} else {
				$user 		 = Zend_Auth::getInstance()->getIdentity();
				$title 		 = $request->getPost('title');
				$description = $request->getPost('description');
				$image 		 = $request->getPost('image');
				$fileType 	 = $request->getPost('file_type');
				$imageUrls 	 = Zend_Json::decode($image);
				$url 		 = $request->getPost('url');
				$htmlCode 	 = $request->getPost('html_code');
				
				$file = new Multimedia_Models_File(array(
					'file_id' 	  => $fileId,
					'title' 	  => $title,
					'slug' 		  => Tomato_Utility_String::removeSign($title, '-', true),
					'description' => $description,
					'url' 		  => $url,
					'html_code'   => $htmlCode,
					'file_type'   => $fileType,
				));
				if (null != $imageUrls) {
					$file->image_square    = $imageUrls['square'];
					$file->image_thumbnail = $imageUrls['thumbnail'];
					$file->image_small 	   = $imageUrls['small'];
					$file->image_crop 	   = $imageUrls['crop'];
					$file->image_medium    = $imageUrls['medium'];
					$file->image_large 	   = $imageUrls['large'];
				}
				$result = $fileDao->update($file);
				
				/**
				 * Execute hooks
			 	 * @since 2.0.2
			 	 */
				Tomato_Hook_Registry::getInstance()->executeAction('Multimedia_File_Edit_Success', $fileId);
			
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('file_edit_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array('file_id' => $fileId), 'multimedia_file_edit'));
			}
		}
	}
	
	/**
	 * Image Editor
	 * 
	 * @since 2.0.4
	 * @return void
	 */
	public function editorAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();

		$request = $this->getRequest();
		$load 	 = $request->getPost('load');
		if ($request->isPost() && $load == 'edit') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$response = 'RESULT_ERROR';
			$action   = $request->getPost('action');
			$source   = $request->getPost('source');
			$source   = explode('?', $source);
			$source   = $source[0];
			$type 	  = null;
			$desData  = $request->getPost('des');
			
			if ($desData != null) {
				$desData = explode('|', $desData);
				$des  = $desData[0];
				$type = $desData[1];
			} else {
				$des = $source;
			}
			$fileId   = $request->getPost('file_id');
			$maxWidth = $request->getPost('max_width');
			
			/**
			 * Remove script filename from base URL
			 */
			$baseUrl = $this->view->baseUrl();
			if (isset($_SERVER['SCRIPT_NAME']) && ($pos = strripos($baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false) {
	            $baseUrl = substr($baseUrl, 0, $pos);
	        }
			if (strpos($des, $baseUrl) === false) {
				return;
			}
			
			$ret 	= $des;
			$des 	= TOMATO_ROOT_DIR . DS .str_replace($baseUrl, '', $des);
			$source = TOMATO_ROOT_DIR . DS .str_replace($baseUrl, '', $source);
			
			/**
			 * Get config
			 */
			$config  = Tomato_Module_Config::getConfig('upload');
			$tool 	 = $config->thumbnail->tool;
			$service = null;
			switch (strtolower($tool)) {
				case 'imagemagick':
					$service = new Tomato_Image_ImageMagick();
					break;
				case 'gd':
					$service = new Tomato_Image_GD();
					break;
			}
			
			$service->setFile($source);
			switch ($action) {
				case 'rotate':
					$degrees = $request->getPost('degrees');
					$service->rotate($des, $degrees);
					break;
				case 'flip':
					$mode = $request->getPost('mode');
					$service->flip($des, $mode);
					break;
				case 'crop':
					$cropX 	   = $request->getPost('x1');
					$cropY 	   = $request->getPost('y1');
					$newWidth  = $request->getPost('width');
					$newHeight = $request->getPost('height');
					$info 	   = getimagesize($source);
					$width 	   = $info[0];
					if ($width > $maxWidth) {
						$ratio = $width / $maxWidth;
						$cropX = floor($cropX * $ratio);
						$cropY = floor($cropY * $ratio);
					}
					
					$service->crop($des, $newWidth, $newHeight, false, $cropX, $cropY);
					break;
			}
						
			$response = array(
				'type' 		=> $type,
				'image_url' => $ret,
			);
			$response = Zend_Json::encode($response);
			$this->getResponse()->setBody($response);
			return;
		}
		
		$fileId = $request->getParam('file_id');
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$fileDao->setDbConnection($conn);
		$file = $fileDao->getById($fileId);
		$data = array(
			'image_square' 	  => $file->image_square,
			'image_thumbnail' => $file->image_thumbnail,
			'image_small' 	  => $file->image_small,
			'image_crop' 	  => $file->image_crop,
			'image_medium' 	  => $file->image_medium,
			'image_large' 	  => $file->image_large,
		);
		$dataString = rawurlencode(base64_encode(Zend_Json::encode($data)));
		
		/**
		 * Remove script filename from base URL
		 */
		$baseUrl = $this->view->baseUrl();
		if (isset($_SERVER['SCRIPT_NAME']) && ($pos = strripos($baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false) {
            $baseUrl = substr($baseUrl, 0, $pos);
        }
        
        $this->view->assign('file', $file);
		$this->view->assign('data', $data);
		$this->view->assign('dataString', $dataString);
        $this->view->assign('baseUrl', $baseUrl);
	}
	
	/**
	 * List files
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$request   = $this->getRequest();
		$pageIndex = $request->getParam('pageIndex', 1);
		$perPage   = 20;
		$offset    = ($pageIndex - 1) * $perPage;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$fileDao->setDbConnection($conn);
		
		/**
		 * Build file search expression
		 */
		$user 	= Zend_Auth::getInstance()->getIdentity();
		$params = null;
		$exp 	= array(
			'created_user' => $user->user_id,
		);
		
		if ($request->isPost()) {
			$id 		 = $request->getPost('fileId');
			$keyword 	 = $request->getPost('keyword');
			$findMyFiles = $request->getPost('findMyFiles');
			$findPhotos  = $request->getPost('findPhotos');
			$findClips 	 = $request->getPost('findClips');
			
			if ($keyword) {
				$exp['keyword'] = strip_tags($keyword);
			}
			if ($id) {
				$exp['file_id'] = $id;
			}
			if (null == $findMyFiles) {
				$exp['created_user'] = null;
			}
			if ($findPhotos) {
				$exp['photo'] = $findPhotos;
			}
			if ($findClips) {
				$exp['clip'] = $findClips;
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
		
		$files 	  = $fileDao->find($offset, $perPage, $exp);
		$numFiles = $fileDao->count($exp);
		
		/**
		 * Paginator
		 */
		$paginator = new Zend_Paginator(new Tomato_Utility_PaginatorAdapter($files, $numFiles));
		$paginator->setCurrentPageNumber($pageIndex);
		$paginator->setItemCountPerPage($perPage);
		$paginatorOptions = array(
			'path' 	   => $this->view->url(array(), 'multimedia_file_list'),
			'itemLink' => (null == $params) ? 'page-%d' : 'page-%d?q=' . $params,
		);
		
		$this->view->assign('pageIndex', $pageIndex);
		$this->view->assign('paginator', $paginator);
		$this->view->assign('paginatorOptions', $paginatorOptions);
		
		/**
		 * Support searching from other page
		 * For example, search files at adding set page
		 * @since 2.0.2
		 */
		if (isset($exp['format']) && $exp['format'] == 'JSON') {
			$this->_helper->getHelper('viewRenderer')->setNoRender();
			$this->_helper->getHelper('layout')->disableLayout();

			$res = array(
				'files' 	=> array(),
				'paginator' => $this->view->paginator()->slide($paginator, $paginatorOptions),
			);
			foreach ($files as $f) {
				$res['files'][] = array(
					'file_id' 	=> $f->file_id,
					'original' 	=> $f->image_original,
					'square' 	=> $f->image_square,
					'thumbnail' => $f->image_thumbnail,
					'small' 	=> $f->image_small,
					'crop' 		=> $f->image_crop,
					'medium' 	=> $f->image_medium,
					'large' 	=> $f->image_large,
					'url' 		=> $f->url,
					'html_code' => $f->html_code,
					'file_type' => $f->file_type,
				);
			}
			$this->getResponse()->setBody(Zend_Json::encode($res));
		} else {
			$this->view->assign('numFiles', $numFiles);
			$this->view->assign('files', $files);
			$this->view->assign('exp', $exp);
		}
	}	
}
