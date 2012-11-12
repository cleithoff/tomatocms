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
 * @version 	$Id: RssController.php 4573 2010-08-12 18:34:21Z huuphuoc $
 * @since		2.0.0
 */

class News_RssController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * View RSS output
	 * 
	 * @return void
	 */
	public function indexAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request    = $this->getRequest();
		$categoryId = $request->getParam('category_id');
		/**
		 * @since 2.0.8
		 */
		$lang       = $request->getParam('lang');
		$output 	= News_Services_Rss::feed($categoryId, $lang);
		
		header('Content-Type: application/rss+xml; charset=utf-8');
		$this->getResponse()->setBody($output);
	}
}
