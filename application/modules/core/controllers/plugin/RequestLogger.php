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
 * @version 	$Id: RequestLogger.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Log HTTP request sent to web server.
 * This plugin still make log if the user are browsing the cache version 
 * which procedured by Tomato_Controller_Plugin_UrlCache plugin. 
 * If you don't want to log the request, set the flag <code>Tomato_GlobalKey::LOG_REQUEST</code> 
 * to <code>false</code> in your action:
 * <code>
 * Zend_Registry::set(Tomato_GlobalKey::LOG_REQUEST, false); 
 * </code>
 */
class Core_Controllers_Plugin_RequestLogger extends Zend_Controller_Plugin_Abstract 
{
	/** 
	 * Some popular bots
	 * <Bot agent pattern> => <Bot name>
	 * @var array
	 */
	private static $_BOTS = array(
		'/googlebot/i' 	   => 'google',
		'/msnbot/i' 	   => 'bing',
		'/slurp/i' 		   => 'yahoo',
		'/baidu/i' 		   => 'baidu',
		'/twiceler/i' 	   => 'cuil',
		'/teoma/i' 		   => 'ask',
		'/facebook/i' 	   => 'facebook',
		'/technoratibot/i' => 'technorati',
	);
	
	/**
	 * Most popular web browsers
	 * 
	 * @var array
	 */
	private static $_BROWSERS = array(
		'firefox', 'msie', 'opera', 
		'chrome', 'safari', 
		'mozilla', 'seamonkey', 'konqueror', 'netscape', 
		'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 
		'omniweb', 'avant', 'camino', 'flock', 'aol',
	);
	
	public function postDispatch(Zend_Controller_Request_Abstract $request) 
	{
		if (Zend_Registry::isRegistered(Tomato_GlobalKey::LOG_REQUEST) 
			&& Zend_Registry::get(Tomato_GlobalKey::LOG_REQUEST) == false
		) {
			return;		
		}
		
		$uri 	 = $request->getRequestUri();
		$agent 	 = $request->getServer('HTTP_USER_AGENT');
		$browser = self::_getBrowserInfo($agent);
		$log 	 = new Core_Models_RequestLog(array(
			'ip' 		  => $request->getClientIp(),
			'agent' 	  => $agent,
			'browser' 	  => $browser['browser'],
			'version' 	  => $browser['version'],
			'platform'	  => $browser['platform'],
			'bot' 		  => self::_getBot($agent),
			'uri' 		  => $uri,
			'full_url' 	  => $request->getScheme().'://'.$request->getHttpHost().'/'.ltrim($uri, '/'),
			'refer_url'   => $request->getServer('HTTP_REFERER'),
			'access_time' => date('Y-m-d H:i:s'),
		));
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$requestLogDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getRequestLogDao();
		$requestLogDao->setDbConnection($conn);
		$requestLogDao->create($log);
	}
	
	private static function _getBrowserInfo($agent) 
	{
		$agent = strtolower($agent);
		$info  = array('browser' => null, 'version' => null, 'platform' => null);
		foreach(self::$_BROWSERS as $browser) { 
            if (preg_match('#(' . $browser . ')[/ ]?([0-9.]*)#', $agent, $match)) { 
                $info['browser'] = $match[1] ; 
                $info['version'] = $match[2] ; 
                break;
            }
        }
		if (preg_match('/linux/', $agent)) { 
            $info['platform'] = 'linux'; 
        } elseif (preg_match('/macintosh|mac os x/', $agent)) { 
            $info['platform'] = 'mac'; 
        } elseif (preg_match('/windows|win32/', $agent)) { 
            $info['platform'] = 'windows';
        }
		return $info; 
	}
	
	private static function _getBot($agent) 
	{
		foreach (self::$_BOTS as $pattern => $name) {
			if (preg_match($pattern, $agent) == 1) {
				return $name;
			}
		}
		return null;
	}
}
