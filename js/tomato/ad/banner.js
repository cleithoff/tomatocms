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
 * @version 	$Id: banner.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Ad.Banner'.namespace();

/**
 * Represent a banner.
 * If the banner have type of flash, the script require SwfObject to render the 
 * flash.
 * 
 * @constructor
 */
Tomato.Ad.Banner = function(id, zone, options, baseUrl) {
	this._trackUrl 	= baseUrl + '/ad/track/';
	this._id 		= id;
	this._zone 		= zone;
	this._options 	= {
		/** 
		 * Format of banner. It can take one of values: image, flash, html
		 * TODO: Support frame
		 */
		'format': 'image',
		
		/** 
		 * The text of banner
		 */
		'text': '',
		'code': '',
		'clickUrl': '',
		'target': 'new_tab',
		'imageUrl': '',
		'pageUrl': window.location,
		'route': '',
		'mode': 'unique',
		
		/**
		 * The time (in seconds) each banner will be displayed in sharing mode.
		 * Note that, this timeout can not work with flash or animated image.
		 */
		'timeout': 15
	};
	
	this._init(options);
	
	/**
	 * Banner's mode
	 * Do NOT change these values
	 */
	Tomato.Ad.Banner.UNIQUE_MODE 	= 'unique';
	Tomato.Ad.Banner.SHARE_MODE 	= 'share';
	Tomato.Ad.Banner.ALTERNATE_MODE = 'alternate';
};

Tomato.Ad.Banner.prototype = {
	/**
	 * Getters/Setters
	 */
	getId: function() { return this._id; },
	getZone: function() { return this._zone; },
	setZone: function(zone) { this._zone = zone; },
	getOption: function(name) { return this._options[name];	},
	
	/**
	 * Render banner
	 * 
	 * @return string
	 */
	render: function() {
		switch (this._options['format']) {
			case 'image':
				var ret = this._buildTrackUrl();
				/**
				 * If you want to show all banners in zone at the sametime
				 * don't set the width and height of image
				 */
				if (Tomato.Ad.Banner.ALTERNATE_MODE == this._options['mode']) {
					ret += '<img src="' + this._options['imageUrl'] + '" /></a>';
				} else {
					ret += '<img src="' + this._options['imageUrl'] + '" width="' + this._zone.getWidth() + '" height="' + this._zone.getHeight() + '" /></a>';
				}
				return ret;
				break;
			case 'flash':
				/**
				 * Require SWFObject to render flash file
				 */
				swfobject.embedSWF(this._options['imageUrl'], this._zone.getContainerId(), 
							this._zone.getWidth(), this._zone.getHeight(), "9.0.0", 
							"", {}, { allowscriptaccess: "always" }, {});
				break;
			case 'html':
				return this._options['code'];
				break;
		}
	},
	
	/**
	 * Initialize banner settings
	 * 
	 * @param array options
	 * @return void
	 */
	_init: function(options) {
		for (var name in options) {
			if (options[name] != null || options[name] != undefined) {
				this._options[name] = options[name];
			}
		}
	},
	
	/**
	 * Build the link's banner
	 * 
	 * @return string
	 */
	_buildTrackUrl: function() {
		/**
		 * Build target window
		 */
		var target = '_blank';
		switch (this._options['target']) {
			case 'new_tab':
				target = '_blank';
				break;
			case 'new_window':
				target = '_blank';
				break;
			case 'same_window':
				target = '';
				break;
		}
		
		switch (this._options['format']) {
			case 'image':
				return '<a target="' + target +'" alt="' + escape(this._options['text']) + '" title="' + escape(this._options['text']) 
					+ '" href="' + this._trackUrl + '?bannerId=' + this._id + '&zoneId=' + this._zone.getId() + '&route=' + this._options['route']
					+ '&clickUrl=' + escape(this._options['clickUrl']) + '">';
				//return '<a target="_blank" href="' + this._options['clickUrl'] + '">';
				break;
			default:
				break;
		}
	}
};
