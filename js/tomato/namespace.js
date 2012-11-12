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
 * @version 	$Id: namespace.js 5496 2010-09-21 17:02:25Z huuphuoc $
 * @since		2.0.0
 */

/**
 * Allow using namespace.
 * http://blogger.ziesemer.com/2008/05/javascript-namespace-function.html
 */
String.prototype.namespace = function(separator) {
	var ns = this.split(separator || '.'),
    	o = window,
    	i,
    	len;
	for (i = 0, len = ns.length; i < len; i++) {
		o = o[ns[i]] = o[ns[i]] || {};
	}
	return o;
};

/**
 * Check if the class exists or not
 * Usage:
 * 
 * 	'x.y.z'.namespace();
 * 	if (!'x.y.z'.isClassExist()) {
 * 		x.y.z = function(...) { ... };
 * 		
 * 		// Declare more prototypes of class here ...
 * 	}
 * 
 * @since 2.0.9
 */
String.prototype.isClassExist = function() {
	return eval('typeof ' + this) == 'function';
};
