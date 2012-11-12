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
 * @version 	$Id: index.php 3905 2010-07-24 15:23:09Z huuphuoc $
 */

/**
 * Check PHP version
 * @since 2.0.1
 */
if (version_compare(phpversion(), '5.2.0', '<') === true) {
    die('ERROR: Your PHP version is ' . phpversion() . '. TomatoCMS requires PHP 5.2.0 or newer.');
}

error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

define('TOMATO_ROOT_DIR', dirname(__FILE__));
define('TOMATO_APP_DIR',  TOMATO_ROOT_DIR . DS . 'application');
define('TOMATO_LIB_DIR',  TOMATO_ROOT_DIR . DS . 'libraries');
define('TOMATO_TEMP_DIR', TOMATO_ROOT_DIR . DS . 'temp');

set_include_path(PS . TOMATO_LIB_DIR . PS . get_include_path());

/**
 * Run the application
 * Use Zend_Application
 * @since 2.0.3
 */
require_once 'Zend/Application.php';
$application = new Zend_Application(
    'production',
    TOMATO_APP_DIR . DS . 'config'. DS . 'application.ini'
);

/**
 * Don't store following options to application.ini, because when user try to install,
 * the installer can not save these options to application.ini
 * (it replaces TOMATO_APP_DIR with real path)
 */
$options = array(
	'bootstrap' => array(
    	'path' 	=> TOMATO_APP_DIR . DS . 'Bootstrap.php',
		'class' => 'Bootstrap',
    ),
    'autoloadernamespaces' => array(
    	'tomato' => 'Tomato_',
    ),
	'resources' => array(
		'frontController' => array(
			'controllerDirectory' => TOMATO_APP_DIR . DS . 'controllers',
			'moduleDirectory' 	  => TOMATO_APP_DIR . DS . 'modules',
		),
	),
);
$options = $application->mergeOptions($application->getOptions(), $options);
$application->setOptions($options)
			->bootstrap()
			->run();
