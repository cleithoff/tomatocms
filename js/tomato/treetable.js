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
 * @version 	$Id: treetable.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.7
 */

'Tomato.TreeTable'.namespace();

/**
 * @constructor
 */
Tomato.TreeTable = function(id, rowPrefix) {
	/**
	 * Id of table container
	 */
	this._id = id;
	
	/**
	 * Prefix of row's class
	 */
	this._rowPrefix = rowPrefix;
	
	this._init();
	
	this._maxRowId = 0;
};

Tomato.TreeTable.CHILD_PADDING = 40;

Tomato.TreeTable.prototype = {
	/**
	 * Initialize the rows
	 * 
	 * @return void
	 */
	_init: function() {
		var self = this;
		$('#' + this._id).find('tbody tr').each(function() {
			self._initRow($(this));
		});
	},
	
	_initRow: function(row) {
		var self = this;
		
		/**
		 * Add new attribute for each row named "parentId"
		 */
		var parentId = $(row).attr('class').split('_')[2];
		$(row).attr('parentid', parentId);
		
		/**
		 * Show the collapseable link for parent row
		 */
		var parent = $('#' + this._rowPrefix + '_id_' + parentId);
		if (parent.length > 0) {
			if ($(parent).find('td:first a.collapseable').length == 0) {
				$('<a/>').attr('href', 'javascript: void(0);')
					.html('-')
					.addClass('collapseable').addClass('expanded')
					.css('text-decoration', 'none')
					.click(function() {
						self._toggle($(this));
					})
					.prependTo($(parent).find('td:first'));
			}
		}
		
		/**
		 * Allows user to drag rows
		 */
		$(row).find('span.' + this._rowPrefix + '_draggable').draggable({
			helper: 'clone',
			opacity: .75,
			revert: 'invalid'
		});
		
		/**
		 * Allows user to drop rows
		 * TODO: Do NOT allow user to drop the row if:
		 * - Drop to itself
		 * - Drop to its parent and it is the last child of parent
		 * - Drop to its children
		 */
		$(row).droppable({
			accept: '.' + self._rowPrefix + '_draggable',
			hoverClass: 't_a_ui_active',
			drop: function(event, ui) {
				/**
				 * Get dropped row and target
				 */
				var source   = $(ui.draggable).parents('tr');
				var target   = this;
				var targetId = $(target).attr('id').split('_')[2];

				var sourceParentId = $(source).attr('parentid');
				var sourceParent   = $('#' + self._rowPrefix + '_id_' + sourceParentId);
				
				/**
				 * Get children rows of dropped row (including itself)
				 */
				var movingRows = new Array();
				movingRows.push(source);
				var childrenRows = self._getChildrenRows(source);
				for (var i in childrenRows) {
					movingRows.push(childrenRows[i]);
				}
				
				var targetPaddingLeft = parseInt($(target).find('td:first').css('padding-left'));
				var sourcePaddingLeft = parseInt($(source).find('td:first').css('padding-left'));
				
				/**
				 * Append dropped row and its children right after the most deep of target row
				 */
				var mostDeepChild = self._getMostDeepRow(target);
				
				$(source).attr('parentid', targetId);
				for (var i = movingRows.length - 1; i >= 0; i--) {
					/**
					 * Update row attributes
					 */
					var tr = movingRows[i];
					var delta = parseInt($(tr).find('td:first').css('padding-left')) - sourcePaddingLeft;
					
					$(tr).find('td:first')
						.css('padding-left', targetPaddingLeft + delta + Tomato.TreeTable.CHILD_PADDING + 'px');
					
					$(mostDeepChild).after(tr);
				}
				
				/**
				 * Update the collapse/expand status for target and parent's source
				 */
				var links = $(target).find('td:first a.collapseable');
				if (links.length > 0) {
					$(links[0]).removeClass('collapsed').addClass('expanded').html('-');
				} else {
					$('<a/>').attr('href', 'javascript: void(0);')
						.html('-')
						.addClass('collapseable').addClass('expanded')
						.css('text-decoration', 'none')
						.click(function() {
							self._toggle($(this));
						})
						.prependTo($(target).find('td:first'));
				}
				
				if (sourceParent) {
					/**
					 * If the parent's source has not any children
					 */
					if ($('#' + self._id).find('tbody tr[parentid="' + sourceParentId + '"]').length == 0) {
						$(sourceParent).find('td:first a.collapseable').remove();
					}
				}
			},
			over: function(event, ui) {
			}
		});
	},
	
	/**
	 * Toggle a row
	 * 
	 * @param link
	 * @return void
	 */
	_toggle: function(link) {
		if ($(link).hasClass('collapsed')) {
			$(link).html('-');
			this._expand(link);
		} else {
			$(link).html('+').removeClass('expanded');
			this._collapse(link);
		}
	},
	
	/**
	 * Expand all children rows
	 * 
	 * @param link
	 * @return void
	 */
	_expand: function(link) {
		$(link).removeClass('collapsed').addClass('expanded');
		var rowId = $(link).parents('tr').attr('id').split('_')[2];
		var self  = this;
		
		$('#' + this._id).find('tbody tr[parentid="' + rowId + '"]').each(function() {
			var collapseableLinks = $(this).find('td:first a.collapseable');
			if (collapseableLinks.length > 0) {
				var rowId = $(this).attr('id').split('_')[2];
				var hasChildren = ($('#' + self._id).find('tbody tr[parentid="' + rowId + '"]').length > 0);
				
				if ($(collapseableLinks[0]).hasClass('expanded') && hasChildren) {
					self._expand(collapseableLinks[0]);
				}
			}
			
			$(this).show();
		});
	},
	
	/**
	 * Collapse all children rows
	 * 
	 * @param link
	 * @return void
	 */
	_collapse: function(link) {
		$(link).addClass('collapsed');
		var rowId = $(link).parents('tr').attr('id').split('_')[2];
		var self = this;
		$('#' + this._id).find('tbody tr[parentid="' + rowId + '"]').each(function() {
			var collapseableLinks = $(this).find('td:first a.collapseable');
			if (collapseableLinks.length > 0) {
				if (!$(collapseableLinks[0]).hasClass('collapsed')) {
					self._collapse(collapseableLinks[0]);
				}
			}
			
			$(this).hide();
		});
	},
	
	/**
	 * Get children rows
	 * TODO: Improve the performance by caching the number of rows' chilren
	 * 
	 * @param row
	 * @return array
	 */
	_getChildrenRows: function(row) {
		var rows = new Array();
		
		var self = this;
		var id   = $(row).attr('id').split('_')[2];
		$('#' + this._id).find('tbody tr[parentid="' + id + '"]').each(function() {
			rows.push($(this));
			var childrenRows = self._getChildrenRows($(this));
			if (childrenRows.length > 0) {
				for (var i in childrenRows) {
					rows.push(childrenRows[i]);
				}
			}
		});
		
		return rows;
	},
	
	/**
	 * Get most deep "children" row
	 * 
	 * @param row
	 * @return TR element
	 */
	_getMostDeepRow: function(row) {
		var rowId    = $(row).attr('id').split('_')[2];
		var children = $('#' + this._id).find('tr[parentid="' + rowId + '"]:last');
		return (children.length == 0) ? row : this._getMostDeepRow(children[0]);
	},
	
	/**
	 * Get number of children (including the children of children)
	 * 
	 * @param rowId
	 * @return int
	 */
	_getNumChildren: function(rowId) {
		var total = 0;
		var self  = this;
		$('#' + this._id).find('tbody tr[parentid="' + rowId + '"]').each(function() {
			total++;
			var currentRowId = $(this).attr('id').split('_')[2];
			total += self._getNumChildren(currentRowId);
		});
		return total;
	},
	
	/**
	 * Returns the tree data. Array of item which each item has the 
	 * following attributes:
	 * - id
	 * - parent_id
	 * - left_id
	 * - right_id
	 * - depth
	 * 
	 * @return array
	 */
	getTreeData: function() {
		var self = this;
		var rows = $('#' + this._id).find('tbody tr');
		
		var numRows = rows.length;
		var rowData = new Array();
		
		var leftId  = -1;
		var rightId = 0;
		var rowId   = 0;
		var depth   = 0;
		var previousDepth = 0;
		var numChildren   = 0;
		
		for (var i = 0; i < numRows; i++) {
			rowId = $(rows[i]).attr('id').split('_')[2];
			depth = parseInt($(rows[i]).find('td:first').css('padding-left')) / Tomato.TreeTable.CHILD_PADDING;
			
			var delta = depth - previousDepth;
			if (delta == 0) {
				leftId = leftId + 2;
			} else if (delta > 0) {
				leftId = leftId + 1;
			} else if (delta < 0) {
				leftId = rightId - delta;
			}
			
			numChildren = this._getNumChildren(rowId);
			
			rowData[i] = {
				id: rowId,
				parent_id: $(rows[i]).attr('parentid'),
				left_id: leftId,
				right_id: 2 * numChildren + leftId + 1,
				depth: depth
			};
			
			rightId       = rowData[i].right_id + 1;
			previousDepth = rowData[i].depth;
		}
		
		return rowData;
	},
	
	/**
	 * Add row to table
	 * 
	 * @param row
	 * @return void
	 */
	addRow: function(row) {
		$('#' + this._id).find('tbody').append(row);
		
		var rowId = $(row).attr('id').split('_')[2];
		if (rowId == 0) {
			/**
			 * We need to calculate the maximum row Id
			 * then assign the Id for just added row
			 */
			if (this._maxRowId == 0) {
				this._maxRowId = this._getMaxRowId();
			}
			this._maxRowId++;
			$(row).attr('id', this._rowPrefix + '_id_' + this._maxRowId);
		}
		
		this._initRow(row);
	},
	
	_getMaxRowId: function() {
		var maxRowId = 0;
		$('#' + this._id).find('tbody tr').each(function() {
			var rowId = parseInt($(this).attr('id').split('_')[2]);
			if (maxRowId < rowId) {
				maxRowId = rowId;
			}
		});
		
		return maxRowId;
	},
	
	/**
	 * Remove row from table
	 * 
	 * @param row
	 * @return void
	 */
	removeRow: function(row) {
		var self = this;
		/**
		 * Get current row Id and parent id
		 */
		var rowId    = $(row).attr('id').split('_')[2];
		var parentId = $(row).attr('parentid');
		
		/**
		 * Update parent value for all row's children
		 */
		$(row).remove();
		$('#' + this._id).find('tbody tr[parentid="' + rowId + '"]').each(function() {
			$(this).attr('parentid', parentId);
			self._decreaseDepth($(this), -1);
		});
	},
	
	_decreaseDepth: function(row, step) {
		var self  = this;
		var rowId = $(row).attr('id').split('_')[2];
		var td    = $(row).find('td:first');
		var paddingLeft = parseInt($(td).css('padding-left'));
		$(td).css('padding-left', paddingLeft + step * Tomato.TreeTable.CHILD_PADDING + 'px');
		
		$('#' + this._id).find('tbody tr[parentid="' + rowId + '"]').each(function() {
			self._decreaseDepth($(this), step);
		});
	}
};
