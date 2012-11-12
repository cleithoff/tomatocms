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
 * @version 	$Id: MysqlParser.php 5063 2010-08-28 18:54:58Z huuphuoc $
 * @since		2.0.5
 */

/**
 * Import sample data from SQL file
 * Based on the algorithm provied by BigDump (http://www.ozerov.de/bigdump.php)
 */
class Core_Import_Adapter_MysqlParser
{
	/**
	 * Array of comment characters
	 */
	private static $_COMMENTS = array(
		'#',
		'--',
		'/*!'
	);	
	
	/**
	 * Parse MySQL *.sql file
	 * 
	 * @param string $file File name
	 * @return array Array of queries
	 */
	public static function parse($file, $prefix = null)
	{
		if (!file_exists($file)) {
			return null;
		}
		
		$queries = array();
		
		@ini_set('auto_detect_line_endings', true);
		@set_time_limit(0);

		$f = fopen($file, 'r');
		
		/**
		 * Fize position
		 */
		$offset = 0;
		
		/**
		 * Get the file size
		 */
		$filesize = filesize($file);
		
		/**
		 * Query
		 */
		$query = '';
		
		while ($offset <= $filesize || $query != '') {
			$inParents = false;
			
			/**
			 * Jump to position
			 */
			fseek($f, $offset);
			
			$dumpline = '';
			$temp = '';
			while (!feof($f) && substr($temp, -1) != "\n" && substr($temp, -1) != "\r") {
				$temp = fgets($f, 4096);
				$dumpline .= $temp;
			}
			if ($dumpline == '') {
				break;
			}
			$dumpline = str_replace("\r\n", "\n", $dumpline);
			$dumpline = str_replace("\r", "\n", $dumpline);
			
			if (!$inParents) {
				$skipLine = false;
				foreach (self::$_COMMENTS as $comment) {
					$pos = strpos($dumpline, $comment);
					if (!$inParents && (trim($dumpline) == '' || (is_int($pos) && $pos == 0))) {
						$skipLine = true;
						break;
					}
				}
				if ($skipLine) {
					$dumpline = '';
				}
			}

			$sqlDeslashed = str_replace("\\\\", "", $dumpline);
      		$parents = substr_count($sqlDeslashed, "'") - substr_count($sqlDeslashed, "\\'");
      		if ($parents % 2 != 0) {
        		$inParents = !$inParents;
      		}
      		$query .= $dumpline;
      		
			if (preg_match("/;$/", trim($query)) && !$inParents) {
				if ($prefix) {
					$query = self::_addPrefix($query, $prefix);
				}
				$queries[] = $query;
				
				/**
				 * Reset query
				 */
				$query = '';
			}
			
      		$offset = ftell($f);
		}
		fclose($f);
		
		return $queries;
	}
	
	/**
	 * Try to find a table name in SQL and replace it with the one including prefix
	 * 
	 * @param string $sql
	 * @return string 
	 */
	private static function _addPrefix($sql, $prefix)
	{
		$queries = array(
			'/DROP(\s+)TABLE(\s+)IF(\s+)EXISTS(\s+)`([\w_]+)`;/' => 'DROP TABLE IF EXISTS `' . $prefix . '$5`;',
			'/CREATE(\s+)TABLE(\s+)`([\w_]+)`/' 				 => 'CREATE TABLE `' . $prefix . '$3`',
			'/LOCK(\s+)TABLES(\s+)`([\w_]+)`(\s+)WRITE;/' 		 => 'LOCK TABLES `' . $prefix . '$3` WRITE;',
			'/INSERT(\s+)INTO(\s+)`([\w_]+)`(\s+)VALUES/'		 => 'INSERT INTO `' . $prefix . '$3` VALUES',
		);
		foreach ($queries as $search => $replace) {
			$sql = preg_replace($search, $replace, $sql);
		}
		return $sql;
	}	
}
