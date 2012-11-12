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
 * @version 	$Id: layout.widget.js 5352 2010-09-09 03:21:56Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Core.Layout.Widget'.namespace();

/**
 * This class represent a widget. Each widget belong to a container.
 */

/**
 * Create new widget
 * 
 * @param string id Id of the DIV element that contain widget
 * @param Tomato.Core.Layout.Container container Widget container
 * @constructor
 */
Tomato.Core.Layout.Widget = function(id, container) {
	/**
	 * Id of widget
	 */
	this._id = id;
	
	/**
	 * The container that widget belongs to
	 */
	this._container = container;
	
	/**
	 * Widget's module
	 */
	this._module = null;
	
	/**
	 * Widget's name
	 */
	this._name = null;
	
	/**
	 * Widget's title
	 */
	this._title = null;
	
	/**
	 * Widget' resources
	 */
	this._resources = {};
	
	/**
	 * Widget's class name
	 */
	this._class = 'Tomato.Core.Layout.Widget';
	
	/**
	 * Mode of widget. It can take one of two values: 
	 * CONFIG if user are performing config widget (default mode)
	 * or PREVIEW if user are previewing widget
	 */
	this._mode = 'CONFIG';
};

/**
 * DO NOT CHANGE THESE VALUES
 * They define values for some special parameters
 */
Tomato.Core.Layout.Widget.CACHE_LIFETIME_PARAM = '___cacheLifetime';
Tomato.Core.Layout.Widget.LOAD_AJAX_PARAM 	   = '___loadAjax';
Tomato.Core.Layout.Widget.PREVIEW_MODE_PARAM   = '___widgetPreviewMode';

/**
 * Getters/Setters
 */
Tomato.Core.Layout.Widget.prototype.getId = function() { return this._id; };
Tomato.Core.Layout.Widget.prototype.setModule = function(module) { this._module = module; };
Tomato.Core.Layout.Widget.prototype.setName = function(name) { this._name = name; };
Tomato.Core.Layout.Widget.prototype.setTitle = function(title) { this._title = title; };
Tomato.Core.Layout.Widget.prototype.getContainer = function() { return this._container; };
Tomato.Core.Layout.Widget.prototype.setContainer = function(container) { this._container = container; };

/**
 * Render widget
 * 
 * @param object params The object contain config data. 
 * Will be used when loading a widget has been configured
 * @param boolean showOnlyContent Shows only content of widget 
 * without head section (which shows widget title) and bottom section (which shows Preview/Config button)
 * The default value is FALSE.
 * This parameter was added from 2.0.7 
 * @return void
 */
Tomato.Core.Layout.Widget.prototype.render = function(params, showOnlyContent) {
	var div = $('<div/>');
	$(div).attr('id', this._id).addClass('t_a_ui_layout_editor_widget').addClass('clearfix');
	
	/**
	 * Append the container
	 */
	$('#' + this._container.getId()).append(div);
	
	var self = this;
	
	/**
	 * Load widget config
	 */
	$(div).html('').addClass('t_a_ui_helper_loading');//.fadeOut('slow');
	
	var data = { mod: this._module, name: this._name, act: 'config' };
	if (params != null) {
		data.params = $.toJSON(params);
	}
	
	var baseUrl = this._container.getBaseUrl();
	baseUrl = baseUrl.replace(/\/+$/, '');
	
	$.ajaxq('core_layout', {
		url: baseUrl + '/core/widget/ajax/',
		type: 'POST',
		data: data,
		success: function(response) {
			response = $.evalJSON(response);
			
			$('<div class="t_a_ui_layout_editor_widget_head"><h3>' + self._title + '</h3></div>'
				+ 	'<div class="t_a_ui_layout_editor_widget_content">'
				+ 		'<div class="t_a_ui_layout_editor_widget_config">' + response.content 
				+ 			Tomato.Core.Layout.Lang.getLang('WIDGET_CACHE') + ':<br /><input type="text" style="width: 100px" name="' + Tomato.Core.Layout.Widget.CACHE_LIFETIME_PARAM + '" class="t_widget_input" />'
				+ 			'<hr /><input type="checkbox" name="' + Tomato.Core.Layout.Widget.LOAD_AJAX_PARAM + '" class="t_widget_input" /> ' + Tomato.Core.Layout.Lang.getLang('WIDGET_LOAD_AJAX')
				+ 		'</div>'
				+ 		'<div class="t_a_ui_layout_editor_widget_preview" id ="' + $(div).attr('id') + '_preview" style="display: none"></div>'
				+ 	'</div>'
				+	'<div class="t_a_ui_layout_editor_widget_bottom"><a href="javascript: void(0);">' + Tomato.Core.Layout.Lang.getLang('WIDGET_PREVIEW') + '</a></div>'
				).appendTo($(div));
			$(div).removeClass('t_a_ui_helper_loading');//.fadeIn('slow');
			
			self._resources.css = response.css;
			for (var i in response.css) {
				if ($('head').find('link[href="' + response.css[i] + '"]').length == 0) {
					$('<link rel="stylesheet" type="text/css" href="' + response.css[i] + '" />').appendTo('head');
				}
			}
			self._resources.javascript = response.javascript;
			for (i in response.javascript) {
				if (response.javascript[i].file != null && $('body').find('script[src="' + response.javascript[i].file + '"]').length == 0) {
					$('<script type="text/javascript" src="' + response.javascript[i].file + '"></script>').prependTo('body');
				}
				
				/**
				 * Add scripts to the end of page
				 * @since 2.0.6
				 */
				if (response.javascript[i].script != null) {
					$('<script type="text/javascript">' + response.javascript[i].script + '</script>').prependTo('body');
				}
			}
			
			/**
			 * Remove button
			 */
			$('<a href="javascript: void(0)" class="t_a_ui_layout_editor_widget_remove">CLOSE</a>').mousedown(function(e) {
                e.stopPropagation();
            }).click(function() {
            	if (confirm(Tomato.Core.Layout.Lang.getLang('WIDGET_REMOVE_CONFIRM'))) {
            		$(this).parents('.t_a_ui_layout_editor_widget').animate({
            			opacity: 0
            		}, function() {
            			$(this).wrap('<div/>').parent().slideUp(function() {
            				$(this).remove();
            				self._container.removeWidget(self);
            			});
            		});
            	}
            }).appendTo($(div).find('.t_a_ui_layout_editor_widget_head'));
			
			/**
			 * Clone button
			 */
			$('<a href="javascript: void(0)" class="t_a_ui_layout_editor_widget_clone">CLONE</a>').mousedown(function(e) {
                e.stopPropagation();
            }).click(function() {
            	var widgetId = self._container.generateWidgetId();
            	
            	/**
            	 * TODO: Use clone() method from jQuery, so we have not load data for widget again
            	 * var clone = $(div).clone(true).attr('id', widgetId);
            	 * console.log('=== Cloning widget: srcId=' + self._id + '==> desId=' + widgetId);
            	 * $(clone).find('div.t_a_ui_layout_editor_widget_preview:first').attr('id', widgetId + '_preview');
            	 * $(clone).appendTo($(div).parent()); 
            	 */
            	var widget = new Tomato.Core.Layout.Widget(widgetId, self._container);
				widget.setModule(self._module);
				widget.setName(self._name);
				widget.setTitle(self._title);
				self._container.addWidget(widget);
				
				widget.render();
            }).appendTo($(div).find('.t_a_ui_layout_editor_widget_head'));
			
			/**
			 * Collapse button
			 */
			$('<a href="javascript: void(0)" class="t_a_ui_layout_editor_widget_collapse">COLLAPSE</a>').mousedown(function (e) {
                e.stopPropagation();  
            }).toggle(function() {
            	$(this).css({backgroundPosition: '-32px 0'}).parents('.t_a_ui_layout_editor_widget').find('.t_a_ui_layout_editor_widget_content, .t_a_ui_layout_editor_widget_bottom, .t_a_ui_layout_editor_widget_config').show();
            	return false;
            }, function() {
            	$(this).css({backgroundPosition: ''}).parents('.t_a_ui_layout_editor_widget').find('.t_a_ui_layout_editor_widget_content, .t_a_ui_layout_editor_widget_bottom, .t_a_ui_layout_editor_widget_config').hide();
            	return false;
            }).prependTo($(div).find('.t_a_ui_layout_editor_widget_head'));
			
			$(div).find('div.t_a_ui_layout_editor_widget_bottom a').toggle(function() {
				self.toggleMode('PREVIEW');
			}, function() {
				self.toggleMode('CONFIG');
			});
			
			/**
			 * Increase container height
			 */
			//self._container.incHeight($(div).height());
			
			/**
			 * Init data for widget if any
			 */
			if (params != null) {
				var data = {};
				for (var paramName in params) {
					data = params[paramName];
					$('#' + self._id).find('.t_widget_input[name="' + paramName + '"]').each(function() {
						if (($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') && data.value != '') {
							$(this).attr('checked', true);
						} else {
							$(this).val(data.value);
						}
					});
					if (data.type != undefined && data.type == 'global') {
						$('#' + self._id).find('.t_widget_input_global[type="checkbox"][name="global_' + paramName + '"]').attr('checked', 'checked');
					}
				}
			}
			
			if (showOnlyContent) {
				self.preview(showOnlyContent);
			}
		}
	});
};

/**
 * Preview widget
 * 
 * @param boolean showOnlyContent Shows only content of widget 
 * without head section (which shows widget title) and bottom section (which shows Preview/Config button)
 * The default value is FALSE.
 * This parameter was added from 2.0.7 
 * @return void
 */
Tomato.Core.Layout.Widget.prototype.preview = function(showOnlyContent) {
	this._mode = 'PREVIEW';	
	var params = {};
	
	/**
	 * Add a param named '__widgetPreviewMode' to indicate that
	 * we are previewing widget in backend, not frontend
	 */
	params[Tomato.Core.Layout.Widget.PREVIEW_MODE_PARAM] = true;
	
	params.container = $('#' + this._id).find('.t_a_ui_layout_editor_widget_preview:first').attr('id');
	
	$('#' + this._id).find('.t_widget_input, .t_widget_input_for_preview').each(function() {
		if ($(this).attr('checked') == true || 
				($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio')) { 
			params[$(this).attr('name')] = $(this).attr('value');
		}
	});
	params = $.toJSON(params);
	
	if (showOnlyContent) {
		$('#' + this._id).find('.t_a_ui_layout_editor_widget_head, .t_a_ui_layout_editor_widget_bottom').hide();
	}
	
	$('#' + this._id).find('div.t_a_ui_layout_editor_widget_content').show().addClass('t_a_ui_helper_loading');
	$('#' + this._id).find('div.t_a_ui_layout_editor_widget_config:first').hide();
	$('#' + this._id).find('div.t_a_ui_layout_editor_widget_preview:first').show().html('');
	var self = this;
	var baseUrl = this._container.getBaseUrl();
	baseUrl = baseUrl.replace(/\/+$/, '');
	$.ajaxq('core_layout', {
		url: baseUrl + '/core/widget/ajax/',
		type: 'POST',
		data: { mod: this._module, name: this._name, params: params },
		success: function(response) {
			response = $.evalJSON(response);
			for (var i in response.css) {
				if ($('head').find('link[href="' + response.css[i] + '"]').length == 0) {
					$('<link rel="stylesheet" type="text/css" href="' + response.css[i] + '" />').appendTo('head');
				}
			}
			
			for (i in response.javascript) {
				if (response.javascript[i].file != null && $('body').find('script[src="' + response.javascript[i].file + '"]').length == 0) {
					$('<script type="text/javascript" src="' + response.javascript[i].file + '"></script>').prependTo('body');
				}
				
				/**
				 * Add scripts to the end of page
				 * @since 2.0.6
				 */
				if (response.javascript[i].script != null) {
					$('<script type="text/javascript">' + response.javascript[i].script + '</script>').prependTo('body');
				}				
			}
			
			$('#' + self._id).find('div.t_a_ui_layout_editor_widget_content').removeClass('t_a_ui_helper_loading'); //.css('background-color', '#272727');
			$('#' + self._id).find('div.t_a_ui_layout_editor_widget_preview:first').show().html(response.content);
		}
	});
};

/**
 * Save handler
 * 
 * @return object An object with following properties:
 * - cls: Widget's class name
 * - module: Widget's module
 * - name: Widget's title
 * - resources: Widget' resources
 * - params: Widget' parameters
 * 
 * Each parameter is an object with two properties:
 * - value: Value of parameter
 * - type: Empty ('') or global
 */
Tomato.Core.Layout.Widget.prototype.save = function() {
	var out = {
		cls: this._class,
		module: this._module,
		name: this._name,
		title: this._title,
		resources: this._resources
	};
	var params = {};
	var self = this, v;
	$('#' + self._id).find('.t_widget_input').each(function() {
		var name = $(this).attr('name');
		
		if (name == Tomato.Core.Layout.Widget.LOAD_AJAX_PARAM) {
			v = ($(this).attr('checked') == true) ? 1 : '';
		} else {
			v = $(this).attr('value');
		}
		params[name] = {
			value: v,
			type: ''
		};
		/**
		 * Allow user to set param value will be taken from request
		 */
		if ($('#' + self._id).find('.t_widget_input_global[type="checkbox"][checked][name="global_' + name + '"]').length > 0) {
			params[name].type = 'global';
		}
	});
	out.params = params;
	return out;
};

/**
 * Toggle preview/config mode for widget
 * 
 * @param string mode New mode, can be PREVIEW or CONFIG
 * @param boolean showOnlyContent
 * @return void
 */
Tomato.Core.Layout.Widget.prototype.toggleMode = function(mode, showOnlyContent) {
	switch (mode) {
		case 'PREVIEW':
			if (this._mode == 'PREVIEW') {
				/**
				 * The widget is currently in the preview mode, do nothing
				 */
			} else {
				/**
				 * Because of using clone() method above, we have to get id as follow
				 * The method this.preview() will not work
				 * var widgetId = $(this).parents('.t_a_ui_layout_editor_widget').attr('id');
				 * console.log("preview widget id=" + widgetId);
				 * this._container.getWidget(widgetId).preview(); 
				 */
				this.preview(showOnlyContent);
				$('#' + this._id).find('div.t_a_ui_layout_editor_widget_bottom a').html(Tomato.Core.Layout.Lang.getLang('WIDGET_BACK'));
			}
			break;
		case 'CONFIG':
			$('#' + this._id).find('.t_a_ui_layout_editor_widget_head, .t_a_ui_layout_editor_widget_bottom, .t_a_ui_layout_editor_widget_config').show();
			$('#' + this._id).find('div.t_a_ui_layout_editor_widget_bottom a').html(Tomato.Core.Layout.Lang.getLang('WIDGET_PREVIEW'));
			$('#' + this._id).find('div.t_a_ui_layout_editor_widget_preview:first').hide();
			$('#' + this._id).find('div.t_a_ui_layout_editor_widget_content').removeClass('t_a_ui_helper_loading');
			
			this._mode = 'CONFIG';
			break;
	}
};
