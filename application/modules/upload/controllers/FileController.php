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
 * @version 	$Id: FileController.php 4761 2010-08-21 18:52:33Z huuphuoc $
 * @since		2.0.0
 */

class Upload_FileController extends Zend_Controller_Action 
{
	/**
	 * Watermark font 
	 * @const string
	 */
	const WATERMARK_FONT  = 'watermark.ttf';
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Browse uploaded files
	 * 
	 * @since 2.0.4
	 * @return void
	 */
	public function browseAction()
	{
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		$path = $request->getParam('path', '');
		$ext  = $request->getParam('ext', '');
		$path = ltrim($path, '/');
		$path = rtrim($path, '/');
		
		$imageSelectCallback = $request->getParam('imageSelectCallback', '');
		
		/**
		 * Breadcump
		 */
		$breadcump = '/';
		
		$fullPath = TOMATO_ROOT_DIR . DS . $path;
		if (!file_exists($fullPath)) {
			$fullPath = TOMATO_ROOT_DIR . DS . 'upload';
			$path     = 'upload';
		}

		$breadcump .= $path;
		
		/**
		 * Get config
		 */
		$config = Tomato_Module_Config::getConfig('upload');
		$sizes  = array();
		foreach ($config->size->toArray() as $key => $value) {
			$sizes[] = $key;
		}
		
		$dirIterator = new DirectoryIterator($fullPath);
		$dirs  = array();
		$files = array();
		foreach ($dirIterator as $file) {
			 if ($file->isDot()) {
				continue;
			}
			$name = $file->getFilename();
            if (('CVS' == $name) || ('.svn' == strtolower($name))) {
                continue;
            }
			if ($file->isDir()) {
				$dirs[] = $name;
			} else {
				if (null == $ext) {
					$files[] = $name;
				} else {
					$fileExt = substr($name, -3);
					if (strpos($ext, $fileExt) !== false) {
						$files[] = $name;
					}
				}
			}
		}
		
		$this->view->assign('imageSelectCallback', $imageSelectCallback);
		$this->view->assign('path', $path);
		$this->view->assign('breadcump', explode('/', $breadcump));
		$this->view->assign('prefixUrl', $this->view->APP_STATIC_SERVER . $breadcump);
		$this->view->assign('sizes', $sizes);
		$this->view->assign('dirs', $dirs);
		$this->view->assign('files', $files);
	}
	
	/**
	 * Upload file
	 * 
	 * @return void
	 */
	public function uploadAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();

		$request = $this->getRequest();
		if (!$request->isPost()) {
			return;
		}
		
		/**
		 * Get config
		 */
		$config = Tomato_Module_Config::getConfig('upload');
		$tool 	= $config->thumbnail->tool;
		$sizes 	= array();
		foreach ($config->size->toArray() as $key => $value) {
			list($method, $width, $height) = explode('_', $value);
			$sizes[$key] = array('method' => $method, 'width' => $width, 'height' => $height);
		}
		
		$user 	  = Zend_Auth::getInstance()->getIdentity();		
		$userName = $user->user_name;
		$module   = $request->getParam('mod');
		$thumbnailSizes = $request->getPost('thumbnails', null);
		
		/**
		 * Prepare folders
		 */
		$dir  = TOMATO_ROOT_DIR . DS . 'upload';
		$path = implode(DS, array($module, $userName, date('Y'), date('m')));
		Tomato_Utility_File::createDirs($dir, $path);
		
		/**
		 * Upload file
		 */
		$ext 	   = explode('.', $_FILES['Filedata']['name']);
		$extension = $ext[count($ext) - 1];
		//$fileName  = basename($_FILES['Filedata']['name'], '.'.$extension);
		$fileName  = uniqid();
		$file 	   = $dir . DS . $path . DS . $fileName . '.' . $extension;
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $file);
		
		/**
		 * Water mark
		 * @since 2.0.4
		 */
		$watermark   = $request->getParam('watermark');
		$overlayText = $color = $overlayImage = $position = $sizesApplied = null;

		if ($watermark) {
			$overlayText  = $request->getParam('text');
			$color 		  = $request->getParam('color');
			$overlayImage = $request->getParam('image');
			$position 	  = $request->getParam('position');
			$sizesApplied = $request->getParam('sizes');		
			$sizesApplied = explode(',', $sizesApplied);
		}
		
		/**
		 * Generate thumbnails if requested
		 */
		if (!isset($thumbnailSizes) || $thumbnailSizes == null) {
			$thumbnailSizes = array_keys($sizes);
		} else if ($thumbnailSizes != 'none') {
			$thumbnailSizes = explode(',', $thumbnailSizes);
		}
		$service = null;
		switch (strtolower($tool)) {
			case 'imagemagick':
				$service = new Tomato_Image_ImageMagick();
				break;
			case 'gd':
				$service = new Tomato_Image_GD();
				break;
		}
		
		$ret = array();
		
		/**
		 * Remove script filename from base URL
		 */
		$baseUrl = $this->view->baseUrl();
		if (isset($_SERVER['SCRIPT_NAME']) && ($pos = strripos($baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false) {
            $baseUrl = substr($baseUrl, 0, $pos);
        }
		$prefixUrl 		 = rtrim($baseUrl, '/') . '/upload/' . $module . '/' . $userName . '/' . date('Y') . '/' . date('m');
		$ret['original'] = array(
			'url'  => $prefixUrl . '/' . $fileName . '.' . $extension,
			'size' => null,
		);
		
		if ($thumbnailSizes != 'none') {
			$service->setFile($file);
			$ret['original']['size'] = $service->getWidth() . ' x ' . $service->getHeight();
			
			foreach ($thumbnailSizes as $s) {
				$service->setFile($file);
				$method = $sizes[$s]['method'];
				$width 	= $sizes[$s]['width'];
				$height = $sizes[$s]['height'];
				
				$f 		 = $fileName . '_' . $s . '.' . $extension;
				$newFile = $dir . DS . $path . DS . $f;
				
				/**
				 * Create thumbnail
				 */
				switch ($method) {
					case 'resize':
						$service->resizeLimit($newFile, $width, $height);						
						break;
					case 'crop':
						$service->crop($newFile, $width, $height);						
						break;
				}
				
				/**
				 * Create watermark if requested
				 */
				if ($watermark) {
					$service->setWatermarkFont(TOMATO_ROOT_DIR . '/data/upload/' . self::WATERMARK_FONT);
					$service->setFile($newFile);
					
					if ($overlayText && in_array($s, $sizesApplied)) {
						$service->watermarkText($overlayText, $position, array('color' => $color, 'rotation' => 0, 'opacity' => 50, 'size' => null));
					}
					if ($overlayImage && in_array($s, $sizesApplied)) {
						$overlay = explode('/', $overlayImage);
						$n 		 = count($overlay);	
						$overlay = implode(DS, array($dir, 'multimedia', $overlay[$n - 4], $overlay[$n - 3], $overlay[$n - 2], $overlay[$n - 1]));
						$service->watermarkImage($overlay, $position);
					}
				}
				
				$ret[$s] = array(
					'url'  => $prefixUrl . '/' . $f,
					'size' => $width . ' x ' . $height,
				);
			}
		}
		
		/**
		 * Return the reponse
		 */
		$ret = Zend_Json::encode($ret);
		$this->getResponse()->setBody($ret);
	}
}
