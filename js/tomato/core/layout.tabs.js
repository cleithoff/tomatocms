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
 * @version 	$Id: layout.tabs.js 5154 2010-08-30 07:31:57Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Core.Layout.Tabs'.namespace();

/**
 * This class represent a tab container
 * 
 * @constructor
 */
Tomato.Core.Layout.Tabs = function(id, container) {
	this._id = id;
	this._container = container;
	this._defaultTitle = "Tab";
	/**
	 * Array of tabs
	 */
	this._tabs = new Array();
};

/**
 * Getters/Setters
 */
Tomato.Core.Layout.Tabs.prototype.getId = function() { return this._id; };
Tomato.Core.Layout.Tabs.prototype.setDefaultTitle = function(title) { this._defaultTitle = title; };
Tomato.Core.Layout.Tabs.prototype.addTab = function(name) {};
Tomato.Core.Layout.Tabs.prototype.removeTab = function(name) {};

/**
 * Render a tabs container
 * 
 * @return void
 */
Tomato.Core.Layout.Tabs.prototype.render = function() {
	$('<div id="' + this._id + '" class="t_tab_container"><ul><li><a href="#"><span>' + this._defaultTitle + '</span></a></li><li><a href="#tTab2"><span>' + this._defaultTitle + '</span></a></li></ul><div id="tTab1"></div><div id="tTab2"></div></div>')
		.appendTo($('#' + this._container.getId()));
	$('#' + this._id).tabs();
};
