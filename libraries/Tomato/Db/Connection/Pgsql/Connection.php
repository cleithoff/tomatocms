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
 * @version 	$Id: Connection.php 4834 2010-08-24 08:29:37Z huuphuoc $
 * @since		2.0.5
 */

class Tomato_Db_Connection_Pgsql_Connection extends Tomato_Db_Connection_Abstract
{
	protected function _connect($config)
	{
		$connString = sprintf('host=%s port=%s dbname=%s user=%s password=%s',
							$config['host'], $config['port'], $config['dbname'], $config['username'], $config['password']);
		$db = pg_connect($connString);
		pg_query(sprintf("SET CLIENT_ENCODING TO '%s'", $config['charset']));
		return $db;
	}
	
	public function getVersion()
	{
		$conn 	 = $this->getSlaveConnection();
		$version = pg_version();
		return 'PostgreSQL v' . $version['client'];
	}
	
	public function query($sql)
	{
		$conn = $this->getMasterConnection();
		pg_query($conn, $sql);
	}
}
