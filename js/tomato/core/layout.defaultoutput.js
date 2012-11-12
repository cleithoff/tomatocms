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
 * @version 	$Id: layout.defaultoutput.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Core.Layout.DefaultOutput'.namespace();

/**
 * This class represents default output of page
 * Default output means the ouput of page without any widgets
 * 
 * @param string id
 * @param Tomato.Core.Layout.Container container
 * @constructor
 */
Tomato.Core.Layout.DefaultOutput = function(id, container) {
	/**
	 * Id of DIV container
	 */
	this._id = id;
	
	/**
	 * Parent container
	 */
	this._container = container;
	
	/**
	 * The class name
	 */
	this._class = 'Tomato.Core.Layout.DefaultOutput';
};

/**
 * The default output class extends from Tomato.Core.Layout.Widget class
 * 
 * @constructor
 */
Tomato.Core.Layout.DefaultOutput.prototype = new Tomato.Core.Layout.Widget();

/**
 * Render a default output
 * 
 * @return void
 */
Tomato.Core.Layout.DefaultOutput.prototype.render = function() {
	var div = $('<div/>');
	$(div).attr('id', this._id).addClass('t_a_ui_layout_editor_widget')
		.addClass('t_g_output').css('height', '140px')
		.addClass('clearfix');
	$('<div class="t_a_ui_layout_editor_widget_head"><h3>' + Tomato.Core.Layout.Lang.getLang('DEFAULT_OUTPUT') + '</h3></div>').css('cursor', 'move').appendTo($(div));
	$('#' + this._container.getId()).append(div);
};
