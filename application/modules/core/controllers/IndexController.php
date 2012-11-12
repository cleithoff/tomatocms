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
 * @version 	$Id: IndexController.php 4822 2010-08-24 06:56:22Z huuphuoc $
 * @since		2.0.7
 */

class Core_IndexController extends Zend_Controller_Action
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Default action which will be dispatched when user browse to /
	 * From 2.0.7, we move the index controller from default controller directory
	 * (/application/controllers/) to core module.
	 * The "/" URL will be mapped to core_index_index route, therefore user can 
	 * configure the layout of index route in back-end section.
	 * 
	 * @return void
	 */
	public function indexAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		
		/**
		 * Add meta keyword tag
		 */
		$config = Tomato_Config::getConfig();
		if ($keyword = $config->web->meta->keyword) {
			$keyword = strip_tags($keyword);
			$this->view->headMeta()->setName('keyword', $keyword);
		}
		
		/**
		 * Add meta description tag
		 */
		if ($description = $config->web->meta->description) {
			$description = strip_tags($description);
			$this->view->headMeta()->setName('description', $description);
		}
		$this->view->headTitle($config->web->title);
	}
}
