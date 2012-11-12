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
 * @version 	$Id: RequestLog.php 5030 2010-08-28 17:16:02Z huuphuoc $
 * @since		2.0.5
 */

class Core_Models_Dao_Pgsql_RequestLog extends Tomato_Model_Dao
	implements Core_Models_Interface_RequestLog
{
	public function convert($entity) 
	{
		return new Core_Models_RequestLog($entity); 
	}
	
	public function create($log) 
	{
		$sql = sprintf("INSERT INTO " . $this->_prefix . "core_request_log (ip, agent, browser, version, platform, bot, uri, full_url, refer_url, access_time)
						VALUES ('%s', '%s', '%s', '%s', '%s', '%s, '%s', '%s', '%s', '%s')",
						pg_escape_string($log->ip),
						pg_escape_string($log->agent),
						pg_escape_string($log->browser),
						pg_escape_string($log->version),
						pg_escape_string($log->platform),
						pg_escape_string($log->bot),
						pg_escape_string($log->uri),
						pg_escape_string($log->full_url),
						pg_escape_string($log->refer_url),
						pg_escape_string($log->access_time));
		pg_query($sql);
	}
}
