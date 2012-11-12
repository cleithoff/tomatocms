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
 * @version 	$Id: loader.js 5271 2010-09-01 07:01:44Z hoangninh $
 * @since		2.0.0
 */

'Tomato.Ad.Loader'.namespace();

Tomato.Ad.Loader = function(zoneId, containerId, route, url) {
	this._zoneId 	  = zoneId;
	this._containerId = containerId;
	this._url 		  = url;
	
	/**
	 * @since 2.0.8
	 */
	this._route 	  = route;
};

Tomato.Ad.Loader.prototype = {
	/**
	 * Load all banners and zones based on the current URL
	 * 
	 * @return void
	 */
	load: function() {
		var url = this._url;
		if (url == null) {
			url = parseUri(window.location).relative;
		}
		url = this._normalizeUrl(url);
		
		var gZone = G_AD_ZONES[this._zoneId + ''];
		if (gZone == null) {
			return;
		}
		/**
		 * Convert from G_AD_ZONES to Zone instance
		 */
		var zone = new Tomato.Ad.Zone(gZone.id, gZone.name, gZone.width, gZone.height);
		zone.setContainerId(this._containerId);
		
		var gBanner = null, match = false, pageUrl = null;
		for (var i = 0; i < G_AD_BANNERS.length; i++) {
			gBanner = G_AD_BANNERS[i];
			var banner = new Tomato.Ad.Banner(gBanner.id, new Tomato.Ad.Zone(gBanner.zone.id), gBanner.options, gBanner.baseUrl);
			
			pageUrl = banner.getOption('pageUrl');
			pageUrl = this._normalizeUrl(pageUrl);
			var regex = new RegExp(pageUrl, "g");
			
			match = (url == '' && pageUrl == '') 
					|| (pageUrl != '' && regex.exec(url) != null)
					|| (this._route == banner.getOption('route'));
			if (match && banner.getZone().getId() == zone.getId() && !zone.contain(banner)) {
				zone.addBanner(banner);
			}
		}
		zone.render();
	},
	
	/**
	 * Removes all / at the beginning and the end of URL
	 * 
	 * @param string url
	 * @return string
	 */
	_normalizeUrl: function(url) {
		/**
		 * Remove all "/" at the begining
		 */
		url = url.replace(/^(\/+)/, '');
		
		/**
		 * Remove all "/" at the end
		 */
		url = url.replace(/(\/+)$/, '');
		return url;
	}
};

/* ========== Libs ========================================================== */

/**
 * parseUri 1.2.1
 * (c) 2007 Steven Levithan <stevenlevithan.com> 
 * MIT License
 */
function parseUri(str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});
	return uri;
};

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};
