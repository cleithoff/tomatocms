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
 * @version 	$Id: LangLoader.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_View_Helper_LangLoader extends Zend_View_Helper_Abstract 
{
	public function langLoader($module) 
	{
		$langs = array('module' => array(), 'widget' => array());
		
		$dir = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'languages';
		if (is_dir($dir)) {
			/**
			 * Try to find the language file of module
			 */
			$langs['module'] = $this->_getLang($dir);
		} else {
			$langs['module'] = null;
		}
        
        /**
         * and of widget in this module
         */
        $widgetDir = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets';
        if (is_dir($widgetDir)) {
       		$dirIterator = new DirectoryIterator($widgetDir);
			foreach ($dirIterator as $subDir) {
	            if ($subDir->isDot() || !$subDir->isDir()) {
	                continue;
	            }
	            $dir = $subDir->getFilename();
	            if ($dir == '.svn') {
	            	continue;
	            }
	            $langs['widget'][$dir] = $this->_getLang($widgetDir . DS . $dir);
			}
        }
        
        return $langs;
	}
	
	private function _getLang($dir) 
	{
		$langs = array();
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $name = $file->getFilename();
            
            $pattern = '/' . str_replace('/', '\/', '^lang.([\w-]+).ini') . '/';
			if (preg_match($pattern, $name, $matches)) {
				$langs[] = $matches[1];
            }
        }
        return $langs;
	}
}
