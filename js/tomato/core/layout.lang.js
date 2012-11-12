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
 * @version 	$Id: layout.lang.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Core.Layout.Lang'.namespace();

/**
 * This static class store language data for layout package
 * 
 * @constructor
 */
Tomato.Core.Layout.Lang = function() {};

/** 
 * Language data 
 */
Tomato.Core.Layout.Lang.DATA = {
	CONTAINER_COLS: 		  '%s cols',
	CONTAINER_REMOVE_CONFIRM: 'Do you really want to remove this container and child container/widgets?',
	WIDGET_PREVIEW: 		  'Preview',
	WIDGET_BACK: 			  'Back',
	WIDGET_REMOVE_CONFIRM: 	  'Do you really want to remove this widget?',
	WIDGET_CACHE: 			  'Cache (in seconds)',
	WIDGET_LOAD_AJAX: 		  'Load by Ajax',
	DEFAULT_OUTPUT: 		  'Default Output'
};

/**
 * Set language data
 * 
 * @param array data
 * @return void
 */
Tomato.Core.Layout.Lang.setLang = function(data) {
	for (var p in data) {
		Tomato.Core.Layout.Lang.DATA[p] = data[p];
	}
};

/**
 * Get language data
 * 
 * @param string key
 * @return string
 */
Tomato.Core.Layout.Lang.getLang = function(key) {
	return Tomato.Core.Layout.Lang.DATA[key];
};
