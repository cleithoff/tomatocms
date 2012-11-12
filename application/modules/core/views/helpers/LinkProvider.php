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
 * @version 	$Id: LinkProvider.php 5309 2010-09-03 18:11:02Z huuphuoc $
 * @since		2.0.7
 */

class Core_View_Helper_LinkProvider extends Zend_View_Helper_Abstract
{
	/**
	 * The counter. 
	 * It is used to distinguish link provider section 
	 * if you want to use this helper many times in the same page 
	 * @var int
	 */
	private $_counter = 0;
	
	/**
	 * @param array $attributes The array contains the following key:
	 * jsCallback: The Javascript callback function used to handle event
	 * when user click on the link.
	 * The callback have to three parameters in the following orders:
	 * - route: The name of route
	 * - href: The href attribute of link
	 * - title: The title of link
	 * 
	 * @param string $lang (since 2.0.8)
	 */
	public function linkProvider($attributes = array('jsCallback' => null), $lang = null)
	{
		//$this->_counter++;
		$this->_counter = uniqid();
		
		/**
		 * @since 2.0.8
		 */
		if (null == $lang) {
			$lang = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
		}
		$links = Core_Services_LinkProvider::getLinks($lang);
		
		$this->view->assign('counter', $this->_counter);
		$this->view->assign('links', $links);
		$this->view->assign('attributes', $attributes);
		
		return $this->view->render('_partial/_linkProvider.phtml');
	}
} 
