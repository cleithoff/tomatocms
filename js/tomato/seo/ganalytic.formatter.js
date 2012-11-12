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
 * @version 	$Id: ganalytic.formatter.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.7
 */

'Tomato.Seo.Ganalytic.Formatter.js'.namespace();

/**
 * This class provide some formatter used to convert from Google Analytic report data
 * to more understandable one
 */
Tomato.Seo.Ganalytic.Formatter = function() {};

/**
 * Convert the visit date
 * 
 * @param string date Date in format of YYYYMMDD
 * @return string Date in format of YYYY-MM-DD
 */
Tomato.Seo.Ganalytic.Formatter.formatVisitDate = function(date) {
	return new Array(date.substring(0, 4), date.substring(4, 6), date.substring(6, 8)).join('-');
};

/**
 * Convert time on site in seconds to format of hours:minutes:seconds
 * 
 * @param float seconds
 * @return string
 */
Tomato.Seo.Ganalytic.Formatter.formatTimeOnSite = function(seconds) {
	var a = [];
	a[0] = '00';
	a[1] = '00';
	a[2] = '00';
	
	if (seconds >= 31556926 ) {
		seconds = $time % 31556926;
	}
	if (seconds >= 86400 ) {
		seconds = seconds % 86400;
	}
	if (seconds >= 3600) {
		a[0]    = Tomato.Seo.Ganalytic.Formatter._formatTime(Math.round(seconds / 3600));
		seconds = seconds % 3600;
	}
	if (seconds >= 60) {
		a[1]    = Tomato.Seo.Ganalytic.Formatter._formatTime(Math.round(seconds / 60));
		seconds = seconds % 60;
	}
	a[2] = Tomato.Seo.Ganalytic.Formatter._formatTime(Math.round(seconds));
	return a.join(':');
};

/**
 * Helper function
 * 
 * @param int time
 * @return string
 */
Tomato.Seo.Ganalytic.Formatter._formatTime = function(time) {
	time = parseInt(time);
	return (time < 10) ? '0' + time + '' : time + '';
};
