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
 * @version 	$Id: HelperLoader.php 4653 2010-08-15 18:06:31Z huuphuoc $
 * @since		2.0.8
 */

class Core_View_Helper_HelperLoader extends Zend_View_Helper_Abstract
{
	/**
	 * Add helper path
	 * 
	 * @param string $module Module name
	 * @return Zend_View_Abstract The view instance
	 */
	public function helperLoader($module)
	{
		$module = strtolower($module);
		$this->view->addHelperPath(TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'views' . DS . 'helpers', ucfirst($module) . '_View_Helper_');
		$this->view->addScriptPath(TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'views' . DS . 'scripts');
		return $this->view;
	}
}
