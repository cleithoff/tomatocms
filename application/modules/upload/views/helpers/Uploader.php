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
 * @version 	$Id: Uploader.php 4656 2010-08-15 18:12:20Z huuphuoc $
 * @since		2.0.0
 */

/**
 * This view helper show the upload form
 */
class Upload_View_Helper_Uploader extends Zend_View_Helper_Abstract 
{
	/**
	 * @var int
	 */
	private $_counter = 0;
	
	/**
	 * Show the upload form
	 * 
	 * @param string $module The name of module which user are uploading from
	 * @param array $options Upload options.
	 * The available options are:
	 * - fileDesc: File description
	 * - extension: File extension. If you want to allow user to upload many file types,
	 * seperate them by ; (*.jpg;*.jpeg;*.png;*.gif, for example)
	 * - multi: TRUE if support upload multiple files
	 * - auto: TRUE if you want to the upload start automatically
	 * - simUploadLimit: Limit number of simulated upload files
	 * - sizeLimit: Limit of file size in Bytes
	 * - thumbnails: Array of thumbnail size configured in file config.ini of upload module
	 * If you don't want to create any thumbnails, set this to "none"
	 *  
	 * @param array $jsHandlers Javascript functions used to handle upload file
	 * The events which can be handled including:
	 * - onError: Call when the upload error
	 * - onCancel: Cancel upload
	 * - onClearQueue: Clear the upload queue
	 * - onComplete: Upload complete
	 * @param string $uploadElementId Id of upload element 
	 * @return string
	 */
	public function uploader($module,
		$options = array('multi' => true, 'auto' => true, 'simUploadLimit' => 5, 'sizeLimit' => 5242880, 'thumbnails' => null), 
		$jsHandlers = array('onError' => null, 'onCancel' => null, 'onClearQueue' => null, 'onProgress' => null, 'onComplete' => null, 'onAllComplete' => null),
		$uploadElementId = null) 
	{
		$this->_counter++;
		if (null == $uploadElementId) {
			$uploadElementId = 'uploadFile_'.$this->_counter;
		}
		
		$this->view->assign('uploadElementId', $uploadElementId);
		$this->view->assign('module', $module);
		$this->view->assign('options', $options);
		$this->view->assign('handlers', $jsHandlers);
		$this->view->assign('sessionId', Zend_Session::getId());
		
		return $this->view->render('_partial/_uploader.phtml');
	}
}
