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
 * @version 	$Id: PhotoController.php 4976 2010-08-26 03:28:23Z huuphuoc $
 * @since		2.0.0
 */

class Multimedia_PhotoController extends Zend_Controller_Action 
{
	/* ========== Backend actions =========================================== */
	
	/**
	 * @return void
	 */
	public function uploadAction() 
	{
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$setDao  = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getSetDao();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$setDao->setDbConnection($conn);
		$fileDao->setDbConnection($conn);
		
		$sets = $setDao->find();
	
		$config = Tomato_Module_Config::getConfig('upload');		
		$sizes 	= array();
		foreach ($config->size->toArray() as $key => $value) {
			list($method, $width, $height) = explode('_', $value);
			$sizes[$key] = array('method' => $method, 'width' => $width, 'height' => $height);
		}
		
		$text 		  = isset($config->watermark->text) ? $config->watermark->text : null;
		$color 		  = isset($config->watermark->color) ? $config->watermark->color : null;
		$image 		  = isset($config->watermark->image) ? $config->watermark->image : null;
		$position 	  = isset($config->watermark->position) ? $config->watermark->position : null;
		$sizesApplied = isset($config->watermark->sizes) ? $config->watermark->sizes : null;
		$sizesApplied = explode(',', $sizesApplied);

		$this->view->assign('sets', $sets);
		$this->view->assign('sizes', $sizes);
		$this->view->assign('text', $text);
		$this->view->assign('color', $color);
		$this->view->assign('image', $image);
		$this->view->assign('position', $position);
		$this->view->assign('sizesApplied', $sizesApplied);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			/**
			 * Save watermark option to module configuration file
			 * @since 2.0.4
			 */
			$watermark = $request->getPost('watermark');
			if ($watermark) {
				$typeMark = $request->getPost('watermarkType');
				$text 	  = $request->getPost('watermarkText');
				$color 	  = $request->getPost('watermarkColor');
				$image 	  = $request->getPost('watermarkImageUrl');
				$position = $request->getPost('watermarkPosition');
				$sizes 	  = $request->getPost('sizes');
				if ($sizes) {
					$sizes = implode(',', $sizes);
				}
							
				$file 	= TOMATO_APP_DIR . DS . 'modules' . DS . 'upload' . DS . 'config' . DS . 'config.ini';		
				$config = new Zend_Config_Ini($file, null, array('allowModifications' => true));
				$config = $config->toArray();
				
				unset($config['watermark']['text']);
				unset($config['watermark']['color']);
				unset($config['watermark']['image']);
				if ('text' == $typeMark && $text) {
					$config['watermark']['text']  = $text;
					$config['watermark']['color'] = $color;
				}
				if ('image' == $typeMark && $image) {
					$config['watermark']['image'] = $image;
				}
				$config['watermark']['position'] = $position;
				$config['watermark']['sizes'] 	 = $sizes;
				$writer = new Zend_Config_Writer_Ini();
				$writer->write($file, new Zend_Config($config));
			}
			
			$user = Zend_Auth::getInstance()->getIdentity();
			$title = $request->getPost('title');
			
			if ($title && $title != ',') {
				$square    = $request->getPost('square');	
				$thumbnail = $request->getPost('thumbnail');
				$small 	   = $request->getPost('small');
				$crop 	   = $request->getPost('crop');
				$medium    = $request->getPost('medium');
				$large 	   = $request->getPost('large');
				$original  = $request->getPost('original');
				
				$setId 	   = $request->getPost('set');
				$newSet    = $request->getPost('newSet');
				
				$arrTitle 	= explode(',', $title);
				$arrCrop 	= explode(',', $crop);
				$arrThumbnail = explode(',', $thumbnail);
				$arrLarge 	= explode(',', $large);
				$arrMedium 	= explode(',', $medium);
				$arrOrgin 	= explode(',', $original);
				$arrSmall 	= explode(',', $small);
				$arrSquare 	= explode(',', $square);
				$isAdd 		= false;
				
				for ($i = 1; $i < count($arrTitle); $i++) {
					$arrImageTitle = explode('.', $arrTitle[$i]);
					$imageName = substr($arrTitle[$i], 0, strlen($arrTitle[$i]) - strlen($arrImageTitle[count($arrImageTitle)-1]) - 1);
					$file = new Multimedia_Models_File(array(
						'title' 			=> $imageName,
						'slug' 				=> Tomato_Utility_String::removeSign($imageName, '-', true),
						'image_square' 		=> $arrSquare[$i],
						'image_thumbnail' 	=> $arrThumbnail[$i],
						'image_small' 		=> $arrSmall[$i],
						'image_crop' 		=> $arrCrop[$i],
						'image_medium' 		=> $arrMedium[$i],						
						'image_large' 		=> $arrLarge[$i],
						'image_original' 	=> $arrOrgin[$i],
						'created_date' 		=> date('Y-m-d H:i:s'),
						'created_user' 		=> $user->user_id,
						'created_user_name' => $user->user_name,
						'url' 				=> $arrOrgin[$i],
						'file_type' 		=> 'image',					
						'is_active' 		=> true,
					));
					$fileId = $fileDao->add($file);
					
					if ($newSet && !$isAdd) {
						$isAdd = true;
						$set = new Multimedia_Models_Set(array(
							'title' 			=> $newSet,
							'created_date' 		=> date('Y-m-d H:i:s'),
							'created_user_id' 	=> $user->user_id,
							'created_user_name' => $user->user_name,
							'is_active' 		=> true,
						));
						$setId = $setDao->add($set);
					}
					if ($setId) {
						$fileDao->addToSet($setId, $fileId);
					}
				}
				$this->_helper->getHelper('FlashMessenger')
					->addMessage($this->view->translator('photo_upload_success'));
				$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'multimedia_photo_upload'));
			}			
		}
	}
}
