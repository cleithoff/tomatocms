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
 * @version 	$Id: Importer.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.5
 */

class Core_Import_Adapter_Mysql_Importer
	implements Core_Import_Importer	
{
	
	private function _getReplacements() 
	{
		$replacements = array();
		$config  = Tomato_Config::getConfig();
		
		foreach ($config->import->replacements as $replacement) {
			if (strpos($replacement->value, 'config://') === false) {
				$replacements[$replacement->key] = $replacement->value;
			} else {
				$replacements[$replacement->key] = $config->getOption(substr($replacement->value,8));
			}			
		}		
		return count($replacements) == 0 ? null : $replacements;
	}
	
	public function import($file)
	{
		$config  = Tomato_Config::getConfig();
		
		/**
		 * Get random master server
		 */
		$servers = $config->db->master;
		$servers = $servers->toArray();
		$random  = array_rand($servers);
		$server  = $servers[$random];
		
		/**
		 * Connect
		 */
		$conn = mysql_connect($server['host'] . ':' . $server['port'], $server['username'], $server['password']);
		mysql_select_db($server['dbname'], $conn);
		mysql_query(sprintf("SET CHARACTER SET '%s'", 
							mysql_real_escape_string($server['charset'])));
		
		$prefix  = Tomato_Db_Connection::factory()->getDbPrefix();
		$replacements = $this->_getReplacements();
		$queries = Core_Import_Adapter_MysqlParser::parse($file, $prefix, $replacements);
		if ($queries) {
			foreach ($queries as $query) {
				mysql_query($query, $conn);
				/**
			 	 * FIXME: Use PDO instead of normal MySQL connection
			 	 * <code>
			 	 * 	try {
			 	 * 		$conn->beginTransaction();
			 	 * 		$conn->query($query);
			 	 * 		$conn->commit();
			 	 * 	} catch (Exception $ex) {
			 	 * 		$conn->rollBack()	
			 	 * 	}
			 	 * </code>
			     */
			}
		}
		mysql_close($conn);
	}
}
