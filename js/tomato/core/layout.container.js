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
 * @version 	$Id: layout.container.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.0
 */

'Tomato.Core.Layout.Container'.namespace();

/**
 * This class represent a container.
 * 
 * @requires Require following plugins from jQuery:
 * - ajaxq
 * - jquery.json
 * - jquery UI (sortable/draggable/droppable)
 * @constructor
 */
Tomato.Core.Layout.Container = function(id, parent) {
	/** 
	 * Id of DIV container
	 */
	this._id = id;

	/** 
	 * Parent container
	 */
	this._parent = parent;
	
	/** 
	 * Child containers 
	 */
	this._child = [];
	
	/** 
	 * Number of child containers 
	 */
	this._childCount = 0;
	
	/** 
	 * Array of child containers 
	 */
	this._childContainers = {};
	
	/** 
	 * Array of widgets 
	 */
	this._widgets = {};
	
	/** 
	 * UUID of widget belong to container 
	 */
	this._widgetUuid = 0;
	
	/** 
	 * Number of grid columns 
	 */
	this._numColumns = 12;
	
	/** 
	 * Releative position of container in row with full of 12 columns.
	 * Can take one of two values: 'first' or 'last'
	 */
	this._position = null;
	
	/** 
	 * Array of tabs
	 * NOT used currently  
	 */
	this._tabs = [];
	
	/** 
	 * UUID of tab container 
	 */
	this._tabUuid = 0;
	
	/** 
	 * Background color
	 */
	this._bgColor = '#1a1a1a';
	
	/**
	 * The base URL used to load the Editor
	 */
	this._baseUrl = '/';
	
	this._droppable();
};

/**
 * Total columns in full-row container
 * TODO: Support 16 and 24 grid system
 */
Tomato.Core.Layout.Container.TOTAL_COLUMNS = 12;

/**
 * Initial height for each created container 
 */
Tomato.Core.Layout.Container.ROW_HEIGHT = 150;

/**
 * This static variable store the current dragged widget
 * TODO: Use jQuery data variable
 */
Tomato.Core.Layout.Container.currentDraggedWidget = null;

/**
 * Getters/setters
 */
Tomato.Core.Layout.Container.prototype.getId = function() { return this._id; };
Tomato.Core.Layout.Container.prototype.setParent = function(parent) { this._parent = parent; };
Tomato.Core.Layout.Container.prototype.getNumColumns = function() { return this._numColumns; };
Tomato.Core.Layout.Container.prototype.setNumColumns = function(numColumns) { this._numColumns = numColumns; };
Tomato.Core.Layout.Container.prototype.getPosition = function() { return this._position; };
Tomato.Core.Layout.Container.prototype.setPosition = function(position) { this._position = position; };
Tomato.Core.Layout.Container.prototype.setBgColor = function(color) { this._bgColor = color; };
Tomato.Core.Layout.Container.prototype.getBaseUrl = function() { return this._baseUrl; };
Tomato.Core.Layout.Container.prototype.setBaseUrl = function(url) { this._baseUrl = url; };

/**
 * Determine the container is root or not
 * 
 * @return boolean TRUE if the container is root
 */
Tomato.Core.Layout.Container.prototype.isRoot = function() {
	return (this._parent == null);
};

/**
 * Handle drop event
 * 
 * @return void 
 */
Tomato.Core.Layout.Container.prototype._droppable = function() {
	var self = this;
	$('#' + self._id).droppable({
		greedy: true,
		over: function(event, ui) {
			/**
			 * TODO: Disable drop in some case
			 */
		},
		drop: function(event, ui) {
			var item = ui.draggable;
			
			/**
			 * Drop container
			 */
			if ($(item).hasClass('t_column_draggable')) {
				var arr = $(item).attr('id').split('_');
				var numColumns = parseInt(arr[arr.length - 1]);
				if ((self.isRoot() && numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) 
					|| (!self.isRoot() && numColumns < Tomato.Core.Layout.Container.TOTAL_COLUMNS)) {
					
					if (numColumns != Tomato.Core.Layout.Container.TOTAL_COLUMNS && self.getChildTotalColumns() > Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
						/**
						 * Show disable drop cursor
						 */
						$(item).addClass('t_a_ui_layout_editor_container_undraggable').draggable('disable');
					} else {
						$(item).removeClass('t_a_ui_layout_editor_container_undraggable').draggable('enable');
						self.append(numColumns);
					}
				}
			}
			/**
			 * Drop tab container
			 */
			else if ($(item).hasClass('t_tab_draggable')) {
				var tabId = self.generateTabId();
				self._tabUuid++;
				var tab = new Tomato.Core.Layout.Tabs(tabId, self);
				tab.render();
				
				self.incHeight(140);
			}
			/**
			 * Drop wigdet
			 */
			else if ($(item).hasClass('t_widget_draggable')) {
				/**
				 * event.target.id => Id of target container
				 * ui.draggable.id => Id of widget
				 */
				var widgetId = self.generateWidgetId();
				var arr = $(item).attr('id').split('_');
				var widget = new Tomato.Core.Layout.Widget(widgetId, self);
				self.addWidget(widget);
				
				widget.setModule(arr[0]);
				widget.setName(arr[1]);
				widget.setTitle($(item).attr('title'));
				widget.render();
			}
			/**
			 * Drop default output
			 */
			else if ($(item).hasClass('t_a_ui_layout_editor_default_output')) {
				var widgetId = self.generateWidgetId();
				var output = new Tomato.Core.Layout.DefaultOutput(widgetId, self);
				self.addWidget(output);
				output.render();
			}
			return true;
		}
	});
};

/**
 * Make this container sortable.
 * User can drag and drop widget from container to other container
 * 
 * @return void
 */
Tomato.Core.Layout.Container.prototype.sortable = function() {
	var self = this;
	$('.t_a_ui_layout_editor_container').sortable({
		items: '.t_a_ui_layout_editor_widget',
        connectWith: '.t_a_ui_layout_editor_container',
        handle: '.t_a_ui_layout_editor_widget_head',
        placeholder: 't_a_ui_layout_editor_widget_placeholder',
        forcePlaceholderSize: true,
        revert: 300,
        opacity: 0.8,
        containment: 'document',
        start: function(e, ui) {
			/**
			 * ui.item.id => Id of widget
			 * self => source container
			 */
			var widgetId = $(ui.item).attr('id');
			Tomato.Core.Layout.Container.currentDraggedWidget = self.getWidget(widgetId);
			
            $(ui.helper).addClass('t_widget_dragging');
        },
        over: function(e, ui) {
        	/**
        	 * Make the widget is suitable in target container
        	 */
        	$(ui.item).css({width: $(e.target).width() + 'px'});
        },
        receive: function(e, ui) {
        	/**
        	 * e.target.id => Id of target container
        	 * ui.item.id => Id of widget
        	 * ui.sender.id => Id of source container
        	 * self => target container
        	 */
        	
        	/**
        	 * Add widget to target container
        	 */
        	if (Tomato.Core.Layout.Container.currentDraggedWidget != null) {
        		Tomato.Core.Layout.Container.currentDraggedWidget.setContainer(self);
	        	self.addWidget(Tomato.Core.Layout.Container.currentDraggedWidget);
        	}
        },
        stop: function(e, ui) {
        	/**
        	 * ui.item.id => Id of widget
        	 * self => source container
        	 */
        	
        	/**
        	 * Remove the widget from source container
        	 * if user dragged widget to other container
        	 */
        	if (Tomato.Core.Layout.Container.currentDraggedWidget != null
        			&& Tomato.Core.Layout.Container.currentDraggedWidget.getContainer().getId() != self._id) {
        		self.removeWidget(Tomato.Core.Layout.Container.currentDraggedWidget);
        	}
            //$(ui.item).css({width: ''}).removeClass('t_widget_dragging');
        }
    });
	
	/**
	 * Full-row containers are sortable
	 */
	if (this._numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
		$('#' + this._parent.getId()).sortable({
			items: '.t_a_ui_layout_editor_container.grid_12',
			handle: '.t_a_ui_layout_editor_container_head'
			//placeholder: 't_a_ui_layout_editor_container_placeholder',
			//forcePlaceholderSize: true
		});
	}
};

/**
 * Make container resizable
 * 
 * @return void
 */
Tomato.Core.Layout.Container.prototype.resizable = function() {
	if (this._numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
		return;
	}
	var self = this;
	$('#' + this._id).resizable({
		/**
		 * 60 is width of one-column container
		 */ 
		minWidth: 60,
		/**
		 * 940 is width of 12-columns container
		 */
		maxWidth: 940,
		handles: 'e',
		//maxHeight: Tomato.Core.Layout.Container.ROW_HEIGHT,
		ghost: false,
		grid: [80, 0],
		containment: 'parent',
		start: function(e, ui) {
		},
		resize: function(e, ui) {
			/**
			 * ui.helper.id, ui.element.id => Id of container
			 */
			$('#' + self._id).removeClass('grid_' + self._numColumns);
			var numColumns = Math.round((ui.size.width + 20) / 80);
			self._numColumns = numColumns;
			$('#' + self._id).addClass('grid_' + numColumns);
			
			/**
			 * Update number of columns for sibling containers
			 */
			self._parent.updatePositionType();
		},
		stop: function(e, ui) {
			/**
			 * Update number of columns for container
			 */
			var numColumns = Math.round((ui.size.width + 20) / 80);
			self._numColumns = numColumns;
		}
	});
};

/**
 * Update the position of all sibling containers when we resize/remove a container
 * 
 * @return void
 */
Tomato.Core.Layout.Container.prototype.updatePositionType = function() {
	var total = 0;
	var child = $('#' + this._id).children('.t_a_ui_layout_editor_container');
	
	$('#' + this._id).children('.clearfix').remove();
	var childColumns = 0;
	
	for (var i = 0; i < child.length; i++) {
		childColumns = Math.round(($(child[i]).width() + 20) / 80);
		$('#' + $(child[i]).attr('id')).find('.t_a_ui_layout_editor_container_head:first h3').html(sprintf(Tomato.Core.Layout.Lang.getLang('CONTAINER_COLS'), childColumns));
		
		total = total + childColumns;
		if (total == this._numColumns && childColumns < this._numColumns) {
			total = 0;
			$('#' + $(child[i]).attr('id')).removeClass('alpha').addClass('omega').after('<div class="clearfix"></div>');
		} else if (total == childColumns || childColumns == this._numColumns || total > this._numColumns) {
			if (total > this._numColumns) {
				total = total - this._numColumns;
			}
			$('#' + $(child[i]).attr('id')).removeClass('omega').addClass('alpha').before('<div class="clearfix"></div>');
		} else if (i > 0 && total > 0 && total < this._numColumns) {
			$('#' + $(child[i]).attr('id')).removeClass('omega').removeClass('alpha');
		}
	}
};

/**
 * Get total columns of child containers
 * 
 * @return int
 */
Tomato.Core.Layout.Container.prototype.getChildTotalColumns = function() {
	var total = 0, numColumns = 0;
	$('#' + this._id).children('.t_a_ui_layout_editor_container').each(function() {
		numColumns = Math.round(($(this).width() + 20) / 80);
		total += numColumns;
	});
	return total;
};

/**
 * Append the container
 * 
 * @param int numColumns Number of columns of new container which will be appended to current container
 * @return Tomato.Core.Layout.Container The child container has just been added
 */
Tomato.Core.Layout.Container.prototype.append = function(numColumns) {
	/**
	 * Ensure that the input is integer
	 */
	numColumns = parseInt(numColumns);
	
	var totalColumns = this.getChildTotalColumns();
	if (numColumns != Tomato.Core.Layout.Container.TOTAL_COLUMNS
			&& totalColumns > Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
		return null;
	}
	totalColumns += numColumns;
	
	/**
	 * Only allow user to drop the container which sum of
	 * its columns and container columns is not greater than 12
	 */ 
	var addableColumns = this._numColumns - totalColumns;
//	$('li.t_a_ui_layout_editor_container_draggable').each(function() {
//		var arr = $(this).attr('id').split('_');
//		if (parseInt(arr[1]) > addableColumns && addableColumns > 0) {
//			$(this).addClass('t_a_ui_layout_editor_container_undraggable').draggable('disable');
//		} else {
//			$(this).removeClass('t_a_ui_layout_editor_container_undraggable').draggable('enable');
//		}
//	});
	
	var id = this._id + '_' + this._childCount + '_' + numColumns;
	var div = $('<div/>');
	$(div).attr('id', id);
	
	$(div).addClass('grid_' + numColumns).addClass('t_a_ui_layout_editor_container').css('margin-bottom', '10px').css('background', this._bgColor);
	
	/**
	 * Have to append div container before creating container
	 * to make the child container is droppable
	 */
	$('#' + this._id).append(div);
	var childContainer = new Tomato.Core.Layout.Container(id, this);
	childContainer.setBaseUrl(this._baseUrl);
	childContainer.setNumColumns(numColumns);
	childContainer.setBgColor(this._bgColor);
	childContainer.sortable();
	childContainer.resizable();
	
	this._child[this._childCount] = childContainer;
	
	this.addChildContainer(childContainer);

	/**
	 * Add remove button
	 */
	var self = this;
	$('<div class="t_a_ui_layout_editor_container_head"><h3>' + sprintf(Tomato.Core.Layout.Lang.getLang('CONTAINER_COLS'), numColumns) + '</h3></div>').css('cursor', 'move').appendTo($(div));
	$('<a href="javascript: void(0)" class="t_a_ui_layout_editor_container_remove">REMOVE</a>').mousedown(function(e) {
        e.stopPropagation();
    }).click(function() {
    	if (confirm(Tomato.Core.Layout.Lang.getLang('CONTAINER_REMOVE_CONFIRM'))) {
    		$(div).remove();
    		self.updatePositionType();
    		self.removeChildContainer(childContainer);
    	}
    }).appendTo($(div).find('.t_a_ui_layout_editor_container_head'));
	
	/**
	 * Add clone button for container
	 */
	$('<a href="javascript: void(0)" class="t_a_ui_layout_editor_container_clone">CLONE</a>').mousedown(function(e) {
        e.stopPropagation();
    }).click(function() {
    	childContainer.append(numColumns);
    }).appendTo($(div).find('.t_a_ui_layout_editor_container_head'));
	
	var minHeight = Tomato.Core.Layout.Container.ROW_HEIGHT + 'px';
	$(div).css('min-height', minHeight).css('height', 'auto !important');//.css('height', minHeight);
	
	var position = null;
	if (/*totalColumns == numColumns || */ numColumns == this._numColumns || this._childCount == 0) {
		/**
		 * We are adding new row
		 */
		$(div).addClass('alpha');
		if (numColumns != this._numColumns) {
			position = 'first';
		}
		
		/**
		 * TODO: Increase container height, if not we still can add columns container
		 * (by clicking on the [+] button on the GUI), but can't drag it
		 */
		minHeight = parseInt($('#' + this._id).css('min-height'));
		if (numColumns == Tomato.Core.Layout.Container.TOTAL_COLUMNS) {
			minHeight += Tomato.Core.Layout.Container.ROW_HEIGHT + 10;
			$('#' + this._id).css('min-height', minHeight).css('height', 'auto !important');//.css('height', minHeight);
		} else {
			var p = null;
			$('#' + id).parents('.t_a_ui_layout_editor_container').each(function(i) {
				// 25 is height of container head section
				if (p == null || parseInt($(p).css('min-height')) + 25 > parseInt($(this).css('min-height'))) {
					minHeight = parseInt($(this).css('min-height')) + 25;
					$(this).css('min-height', minHeight).css('height', 'auto !important');//.css('height', minHeight);
				}
				p = this;
			});
		}
	}
	if (numColumns == this._numColumns || numColumns == totalColumns && this._childCount > 0) {
//		$('#' + this._id).append($('<div class="t_g_clear" style="height: 1px"></div>'));
//		$('#' + this._id).append($('<div class="t_container_row" style="clear: both; height: 140px"></div>'));
	}
	
	if (totalColumns == this._numColumns) {
		$(div).addClass('omega');
		if (numColumns != this._numColumns) {
			position = 'last';
		}
	}
	this._childCount++;
	
	childContainer.setPosition(position);
	
	return childContainer;
};

/**
 * Add child container
 * 
 * @param Tomato.Core.Layout.Container container
 * @return void
 */
Tomato.Core.Layout.Container.prototype.addChildContainer = function(container) {
	this._childContainers[container.getId() + ''] = container;
};

/**
 * Get child container by its Id
 * 
 * @param string id
 * @return Tomato.Core.Layout.Container
 */
Tomato.Core.Layout.Container.prototype.getChildContainer = function(id) {
	return this._childContainers[id + ''];
};

/**
 * Remove child container
 * 
 * @param Tomato.Core.Layout.Container container
 * @return void
 */
Tomato.Core.Layout.Container.prototype.removeChildContainer = function(container) {
	delete this._childContainers[container.getId() + ''];
};

/**
 * Add widget
 * 
 * @param Tomato.Core.Layout.Widget widget
 * @return void
 */
Tomato.Core.Layout.Container.prototype.addWidget = function(widget) {
	widget.setContainer(this);
	this._widgets[widget.getId() + ''] = widget;
};

/**
 * Get widget in container by its id
 * 
 * @param string id Id of widget
 * @return Tomato.Core.Layout.Widget
 */
Tomato.Core.Layout.Container.prototype.getWidget = function(id) {
	return this._widgets[id + ''];
};

/**
 * Remove a widget from container
 * 
 * @param Tomato.Core.Layout.Widget widget The widget will be removed
 * @return void
 */
Tomato.Core.Layout.Container.prototype.removeWidget = function(widget) {
	delete this._widgets[widget.getId() + ''];
};

/**
 * Increase height of container some pixel
 * 
 * @param int added
 * @return void
 */
Tomato.Core.Layout.Container.prototype.incHeight = function(added) {
	var height = parseInt($('#' + this._id).height()) + added;
	$('#' + this._id).css('height', height + 'px');
};

/**
 * Generate widget Id
 * 
 * @return string Id for new widget
 */
Tomato.Core.Layout.Container.prototype.generateWidgetId = function() {
	this._widgetUuid++;
	return this._id + '_widget_' + this._widgetUuid;
};

/**
 * Generate tab Id
 * NOT used currently
 * 
 * @return string
 */
Tomato.Core.Layout.Container.prototype.generateTabId = function() {
	return this._id + '_tabs_' + this._tabUuid;
};

/**
 * Save handler
 * 
 * @return object An object with following properties:
 * - isRoot: Indicate the container is root or not
 * - position: alpha or omega, if the container is first or last one 
 * among of parent' child containers
 * - cols: Number of columns
 * - containers: Array of child containers
 * - widgets: Array of child widgets
 */
Tomato.Core.Layout.Container.prototype.save = function() {
	var out = {
		isRoot: (this._parent == null) ? 1 : 0,
		cols: this._numColumns,
		containers: new Array(),
		widgets: new Array()
	};
	if (this._position != null) {
		out.position = this._position;
	}
	
	var self = this;
	
	/**
	 * Don't loop through the list of child containers as follow:
	 * 	for (var i in this._child) {
	 * 		out.containers[i] = this._child[i].save();
	 * 	}
	 * because we need to keep the order of child containers
	 * (in case user drag and drop container)
	 */
	$('#' + this._id).children('.t_a_ui_layout_editor_container').each(function(i) {
		var containerId = $(this).attr('id');
		var container = self.getChildContainer(containerId);
		if (container != null) {
			out.containers[i] = container.save();
		}
	});

	$('#' + this._id).children('.t_a_ui_layout_editor_widget').each(function(i) {
		var widgetId = $(this).attr('id');
		var widget = self.getWidget(widgetId);
		if (widget != null) {
			out.widgets[i] = widget.save();
		}
	});
	return out;
};

/**
 * Load child containers and widgets
 * 
 * @param object data Child containers and widgets serialized
 * @param boolean showOnlyContent Shows only content of widget/container 
 * without head section (which shows widget title) and bottom section (which shows Preview/Config button)
 * The default value is FALSE.
 * This parameter was added from 2.0.7
 * @return void
 */
Tomato.Core.Layout.Container.prototype.load = function(data, showOnlyContent) {
	for (var i in data.containers) {
		var numColumns = data.containers[i].cols;
		var childContainer = this.append(numColumns);
		
		if (childContainer != null) {
			childContainer.load(data.containers[i], showOnlyContent);
		}
	}
	
	if (showOnlyContent) {
		$('#' + this._id).children('.t_a_ui_layout_editor_container_head').hide();
		$('#' + this._id).css({backgroundColor: ''});
	}
	
	var widgetId, widget;
	for (i in data.widgets) {
		widgetId = this.generateWidgetId();
		/**
		 * TODO: Make a factory or reflection
		 */
		switch (data.widgets[i].cls) {
			case 'Tomato.Core.Layout.DefaultOutput':
				widget = new Tomato.Core.Layout.DefaultOutput(widgetId, this);	
				break;
			case 'Tomato.Core.Layout.Widget':
			default:
				widget = new Tomato.Core.Layout.Widget(widgetId, this);
				break;
		}
		this.addWidget(widget);

		widget.setModule(data.widgets[i].module);
		widget.setName(data.widgets[i].name);
		widget.setTitle(data.widgets[i].title);
		widget.render(data.widgets[i].params, showOnlyContent);
	}
};

/** 
 * Toggle preview/config mode for container.
 * In case, user want to preview a container without performing preview action for
 * each widgets belong to it.
 * 
 * @param string mode Can be PREVIEW or CONFIG
 * @param boolean showOnlyContent
 * @return void
 */
Tomato.Core.Layout.Container.prototype.toggleMode = function(mode, showOnlyContent) {
	switch (mode) {
		case 'PREVIEW':
			if (showOnlyContent) {
				$('#' + this._id).children('.t_a_ui_layout_editor_container_head').hide();
			} else {
				$('#' + this._id).children('.t_a_ui_layout_editor_container_head').show();
			}
			$('#' + this._id).css({backgroundColor: ''});
			break;
		case 'CONFIG':
			if (showOnlyContent) {
				$('#' + this._id).children('.t_a_ui_layout_editor_container_head, .t_a_ui_layout_editor_container_bottom').hide();
			} else {
				$('#' + this._id).children('.t_a_ui_layout_editor_container_head, .t_a_ui_layout_editor_container_bottom').show();
			}
			
			/**
			 * Restore background color
			 */
			$('#' + this._id).css({backgroundColor: this._bgColor});
			break;
	}
	for (var i in this._child) {
		this._child[i].toggleMode(mode, showOnlyContent);
	}
	var self = this;
	$('#' + this._id).children('.t_a_ui_layout_editor_widget').each(function(i) {
		var widgetId = $(this).attr('id');
		var widget = self.getWidget(widgetId);
		if (widget != null) {
			widget.toggleMode(mode, showOnlyContent);
		}
	});
};
