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
 * @version 	$Id: colorconverter.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Core.ColorConverter'.namespace();

Tomato.Core.ColorConverter = function() {};

Tomato.Core.ColorConverter.hexToRgb = function(hex) {
	if (hex.substr(0, 1) == '#') {
		hex = hex.substr(1);
	}
	var r = parseInt(hex.substr(0, 2), 16),
    	g = parseInt(hex.substr(2, 2), 16),
    	b = parseInt(hex.substr(4, 2), 16);
	if (isNaN(r) || isNaN(g) || isNaN(b)) {
		r = g = b = 0;
	}
	return [r, g, b];
};

Tomato.Core.ColorConverter.rgbToHsv = function(r, g, b) {
	var r = (r / 255), g = (g / 255), b = (b / 255);	
	var min = Math.min(Math.min(r, g), b),
		max = Math.max(Math.max(r, g), b),
		delta = max - min;
	var v = max, s, h;
	if (max == min) {
		h = 0;
	} else if (max == r) {
		h = (60 * ((g-b) / (max-min))) % 360;
	} else if (max == g) {
		h = 60 * ((b-r) / (max-min)) + 120;
	} else if (max == b) {
		h = 60 * ((r-g) / (max-min)) + 240;
	}
	if (h < 0) {
		h += 360;
	}
	
	if (max == 0) {
		s = 0;
	} else {
		s = 1 - (min/max);
	}
	return [Math.round(h), Math.round(s * 100), Math.round(v * 100)];
};

Tomato.Core.ColorConverter.hsvToRgb = function(h, s, v) {
	var s = s / 100, v = v / 100;
	var hi = Math.floor((h/60) % 6);
	var f = (h / 60) - hi;
	var p = v * (1 - s);
	var q = v * (1 - f * s);
	var t = v * (1 - (1 - f) * s);

	var rgb = [];
	switch (hi) {
		case 0: rgb = [v, t, p]; break;
		case 1: rgb = [q, v, p]; break;
		case 2: rgb = [p, v, t]; break;
		case 3: rgb = [p, q, v]; break;
		case 4: rgb = [t, p, v]; break;
		case 5: rgb = [v, p, q]; break;
	}
	var r = Math.min(255, Math.round(rgb[0]*256)),
		g = Math.min(255, Math.round(rgb[1]*256)),
		b = Math.min(255, Math.round(rgb[2]*256));
	return [r, g, b];
};

Tomato.Core.ColorConverter.rgbToHex = function(r, g, b) {
	return ('#' + Tomato.Core.ColorConverter._intToHex(r) 
			+ Tomato.Core.ColorConverter._intToHex(g)
			+ Tomato.Core.ColorConverter._intToHex(b)).toUpperCase();
};

Tomato.Core.ColorConverter._intToHex = function(i) {
	var hex = parseInt(i).toString(16);  
	return (hex.length < 2) ? "0" + hex : hex;
};
