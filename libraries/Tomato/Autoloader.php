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
 * @version 	$Id: Autoloader.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Autoloader extends Zend_Loader_Autoloader_Resource
{
    public function __construct($options)
    {
        parent::__construct($options);
    }
    
	public function autoload($class)
    {
    	$prefix = TOMATO_APP_DIR . DS;
    	$paths  = explode('_', $class);
    	switch (strtolower($paths[0])) {
    		case 'plugins':
    		case 'hooks':
    			$prefix .= '';
    			break;
    		default:
    			$prefix .= 'modules' . DS;
    			break;
    	}
    	    	
		$className = $paths[count($paths) - 1];
		$classFile = substr($class, 0, -strlen($className));
		$classFile = $prefix . strtolower(str_replace('_', DS, $classFile)) . $className . '.php';
		
		if (file_exists($classFile)) {
			return require_once $classFile;
		}
    	
        return false;
    }
}
