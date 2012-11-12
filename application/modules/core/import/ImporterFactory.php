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
 * @version 	$Id: ImporterFactory.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.5
 */

class Core_Import_ImporterFactory
{
	/**
	 * @return Core_Import_Importer
	 */
	public static function getInstance()
	{
		$config  = Tomato_Config::getConfig();
		$adapter = $config->db->adapter;
		$adapter = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));
		$class   = 'Core_Import_Adapter_' . $adapter . '_Importer';
		if (!class_exists($class)) {
			return null;
		}
		$instance = new $class($adapter);
		return $instance;
	}
}
