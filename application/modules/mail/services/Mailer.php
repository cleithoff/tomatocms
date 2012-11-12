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
 * @version 	$Id: Mailer.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.6
 */

class Mail_Services_Mailer
{
	/**
	 * Mail transport instance
	 * 
	 * @var Zend_Mail_Transport
	 */
	private static $_transport = null;
	
	/**
	 * Get mail transport
	 * 
	 * @return Zend_Mail_Transport
	 */
	public static function getMailTransport()
	{
		if (self::$_transport == null) {
			$config = Tomato_Module_Config::getConfig('mail');
			$config = $config->toArray();
			
			switch ($config['protocol']['protocol']) {
				case 'mail':
					self::$_transport = new Zend_Mail_Transport_Sendmail();
					break;				
				case 'smtp':
					$options = array();
					/**
					 * Check port setting
					 */
					if (isset($config['smtp']['port'])) {
						$options['port'] = $config['smtp']['port'];
					}
					
					/**
					 * Check authentication settings
					 */
					if (isset($config['smtp']['username']) && isset($config['smtp']['password'])) {
						$options['auth'] 	 = 'login';
						$options['username'] = $config['smtp']['username'];
						$options['password'] = $config['smtp']['password'];
					}
					
					/**
					 * Check security setting
					 */
					if (isset($config['smtp']['security'])) {
						$options['ssl'] = $config['smtp']['security'];
					}
					
					self::$_transport = new Zend_Mail_Transport_Smtp($config['smtp']['host'], $options);					
					break;
			}
		}
		
		return self::$_transport;
	}	
}
