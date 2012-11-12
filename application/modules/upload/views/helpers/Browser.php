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
 * @version 	$Id: Browser.php 3979 2010-07-25 11:37:38Z huuphuoc $
 * @since		2.0.4
 */

class Upload_View_Helper_Browser extends Zend_View_Helper_Abstract
{
	/**
	 * @var int
	 */
	private $_counter = 0;
	
	/**
	 * @param string $path Current upload path
	 * @param string $ext File extensions will be used to filter the list of files
	 * @param string $imageSelectCallback JavaScript callback function which will be executed when select the file
	 * @return string
	 */
	public function browser($path = '/', $ext = null, $imageSelectCallback = null)
	{
		$this->_counter++;
		
		$this->view->assign('counter', $this->_counter);
		$this->view->assign('path', $path);
		$this->view->assign('imageSelectCallback', $imageSelectCallback);
		$this->view->assign('ext', $ext);
		$this->view->addScriptPath(TOMATO_APP_DIR . DS . 'modules' . DS . 'upload' . DS . 'views' . DS . 'scripts');
		
		return $this->view->render('file/_browser.phtml');
	}
}
