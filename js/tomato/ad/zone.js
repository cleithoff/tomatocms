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
 * @version 	$Id: zone.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Ad.Zone'.namespace();

/**
 * Represent a zone
 * 
 * @constructor
 */
Tomato.Ad.Zone = function(id, name, width, height) {
	this._id 				 = id;
	this._name 				 = name;
	this._width 			 = width;
	this._height 			 = height;
	this._banners 			 = new Array();
	this._bannerIds 		 = new Array();
	this._currentBannerIndex = 0;
	this._containerId 		 = this._id;
	this._mode 				 = Tomato.Ad.Banner.UNIQUE_MODE;
};

Tomato.Ad.Zone.prototype = {
	/**
	 * Getters/Setters
	 */
	getId: function() { return this._id; },
	getContainerId: function() { return this._containerId; },
	setContainerId: function(id) { this._containerId = id; },
	getName: function() { return this._name; },
	getWidth: function() { return this._width; },
	getHeight: function() { return this._height; },
	
	/**
	 * Add banner to zone
	 * 
	 * @param Tomato.Ad.Banner banner
	 * @return void
	 */
	addBanner: function(banner) {
		banner.setZone(this); 
		this._banners[this._banners.length] = banner;
		this._bannerIds[this._bannerIds.length] = banner.getId();
		this._mode = banner.getOption('mode');
	},
	
	/**
	 * Render zone
	 * 
	 * @return string
	 */
	render: function() {
		if (this._banners.length == 0) {
			return;
		}
		$('#' + this._containerId).addClass('t_ad_zone');
		switch (this._mode) {
			case Tomato.Ad.Banner.UNIQUE_MODE:
				var html = this._banners[0].render();
				$('#' + this._containerId).html(html);
				break;
			case Tomato.Ad.Banner.SHARE_MODE:
				this._renderShareImageBanner();
				break;
			case Tomato.Ad.Banner.ALTERNATE_MODE:
				var html = '';
				/**
				 * TODO: Add padding for image
				 */
				for (var i = 0; i < this._banners.length; i++) {
					html += this._banners[i].render();
				}
				$('#' + this._containerId).html(html);
				break;
		}
	},
	
	/**
	 * Render sharing image banners
	 * 
	 * @return void
	 */
	_renderShareImageBanner: function() {
		var self = this;
		var html = this._banners[this._currentBannerIndex].render();
		$('#' + this._containerId).html(html);
		
		setTimeout(function() { 
				self._renderShareImageBanner(); 
			}, this._banners[this._currentBannerIndex].getOption('timeout') * 1000
		);
		this._currentBannerIndex++;
		if (this._currentBannerIndex >= this._banners.length) {
			this._currentBannerIndex = 0;
		}
	},
	
	/**
	 * Check whether a banner has been added to zone or not
	 * 
	 * @param Tomato.Ad.Banner banner
	 * @return boolean
	 */
	contain: function(banner) {
		return ($.inArray(banner.getId(), this._bannerIds) > -1);
	}
};
