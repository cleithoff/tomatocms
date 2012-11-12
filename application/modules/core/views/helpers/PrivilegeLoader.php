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
 * @version 	$Id: PrivilegeLoader.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

class Core_View_Helper_PrivilegeLoader extends Zend_View_Helper_Abstract 
{
	public function privilegeLoader() 
	{
		return $this;
	}
	
	public function getPrivileges($module) 
	{
		$file = TOMATO_APP_DIR . DS . 'modules' . DS . $module . DS . 'config' . DS . 'permissions.xml';
		if (!file_exists($file)) {
			return null;
		}
		$ret = array();
		$xml = simplexml_load_file($file);
		$resources = $xml->resource;
		foreach ($resources as $res) {
			$attr 	 = $res->attributes();
			$langKey = (string) $attr['langKey'];
			
			$description = $this->view->translator($langKey, $module);
			$description = ($description == $langKey) ? (string) $attr['description'] : $description;
			$resource = new Core_Models_Resource(array(
				'description' 	  => $description,
				'module_name' 	  => $module,
				'controller_name' => $attr['name'],
			));
			$privileges = array();
			if ($res->privilege) {
				foreach ($res->privilege as $pri) {
					$attr2 	 = $pri->attributes();
					$langKey = (string) $attr2['langKey'];
					
					$description = $this->view->translator($langKey, $module);
					$description = ($description == $langKey) ? (string) $attr2['description'] : $description;
					
					$privileges[] = new Core_Models_Privilege(array(
						'name' 			  => $attr2['name'],
						'description' 	  => $description,
						'module_name'	  => $module,
						'controller_name' => $attr['name'],
					));
				}
			}
			$ret[] = array('resource' => $resource, 'privileges' => $privileges);
		}
		return $ret;
	}
}
