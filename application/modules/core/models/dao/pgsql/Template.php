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
 * @version 	$Id: Template.php 5030 2010-08-28 17:16:02Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_Template extends Tomato_Model_Dao
	implements Core_Models_Interface_Template
{
	public function convert($entity)
	{
		return $entity; 
	}
	
	public function install($template)
	{
		$file = TOMATO_APP_DIR . DS . 'templates' . DS . $template . DS . 'about.xml';
		if (!file_exists($file)) {
			return;
		}
		$xml   = simplexml_load_file($file);
		
		$xpath = $xml->xpath('install/db[contains(@adapter, "pgsql")]/query');
		if (is_array($xpath) && count($xpath) > 0) {
			foreach ($xpath as $query) {
				try {
					$query = str_replace('###', $this->_prefix, (string)$query);
					pg_query($query);
				} catch (Exception $ex) {
					break;
				}
			}
		}
	}
}
