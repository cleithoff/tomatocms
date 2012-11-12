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
 * @version 	$Id: note.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.4
 */

'Tomato.Multimedia.Note'.namespace();
'Tomato.Multimedia.Note.Container'.namespace();

/**
 * Create new note
 * 
 * @param int id Id of note
 * @param int photoId Id of photo
 * @param int top
 * @param int left
 * @param int width
 * @param int height
 * @param string content Content of note
 */
Tomato.Multimedia.Note = function(id, top, left, width, height, content, creatable, editable, removable) {
	this._top 		= top;
	this._left 		= left;
	this._width 	= width;
	this._height 	= height;
	this._content 	= content;
	this._options 	= null;
	this._creatable = creatable;
	this._editable 	= editable;
	this._removable = removable;
	
	this._container = null;
	this._image 	= null;
	this._id 		= id;
	this._index 	= 0;
};

Tomato.Multimedia.Note.prototype = {
	/**
	 * Getters/Setters
	 */
	setEditable: function(editable) { this._editable = editable; },
	setRemovable: function(removable) { this._removable = removable; },
	setIndex: function(index) { this._index = index; },
	setContainer: function(container) {
		this._container = container;
		this._photoId 	= container.getPhotoId();
		this._options 	= container.getOptions();
		this._image 	= container.getImage();
	},
	
	/**
	 * Show note
	 */
	show: function() {
		var parent = $(this._image).parent();
		var self = this;
	
		/** Create container */
		var container = $('<div/>');
		$(container).attr('id', 'note_container_' + this._index)
			.css('position', 'absolute')
			.css('top', this._top).css('left', this._left);		
	
		/** Note rectangle */
		var rec = $('<div/>');
		$(rec).attr('id', 'note_' + this._index).addClass('t_multimedia_note')
			.width(this._width).height(this._height);	
		
		/** Note content */
		var text = $('<div/>');
		$(text).attr('id', 'note_content_' + this._index).addClass('t_multimedia_note_content')
			.html(this._content);	
		var content = $('<div/>');
		$(content).append(text).hide();
		
		/** Edit/delete buttons */
		var buttons = $('<div/>');
		if (this._creatable || this._editable) {
			var url = (this._id == null  && this._creatable) ? this._options.addUrl : this._options.editUrl;
			
			$(text).editInPlace({
				url: url,
				bg_out: '#f3f3f3',
				field_type: 'text',
				textarea_cols: 17,
				textarea_rows: 2,
				saving_text: self._options.savingText,
				on_blur: null,
				callback: function(original_element, html, original) {
					self._content = html;
//					self._handleUpdate();
					return html;
			    }
			});
			$('<a/>').attr('id', 'note_save_' + this._index).attr('href', 'javascript: void(0)').addClass('t_multimedia_note_action')
				.click(function() {
					self._handleUpdate();
				})
				.html(self._options.saveButton).appendTo(buttons);
		}
		if (this._removable) {
			$('<a/>').attr('href', 'javascript: void(0)').addClass('t_multimedia_note_action')
				.click(function() {
					self._handleDelete();
				})
				.html(self._options.deleteButton).appendTo(buttons);
		}
		if (this._creatable || this._editable || this._removable) {
			$(content).append(buttons);
		}
		
		$(container).append(rec).append(content).prependTo(parent);
		
		$(rec).resizable({
				containment: parent,
				resize: function(event, ui) {
					$(content).css('position', 'absolute')
						.css('top', ui.position.top + ui.size.height);
//						.width($(content).width());
				},
				stop: function(event, ui) {
					self._top = self._top + ui.position.top;
					self._left = self._left + ui.position.left;
					self._height = ui.size.height;
					self._width = ui.size.width;
					
					$(rec).css('top', 0).css('left', 0);
					$(content).css('top', self._height);
					$(container).css('top', self._top).css('left', self._left);
				}
			})
			.draggable({
				containment: parent,
				drag: function(event, ui) {
					$(content).css('position', 'absolute')
						.css('top', ui.position.top + self._height)
						.css('left', ui.position.left);
//						.width($(content).width());
				},
				stop: function(event, ui) {
					self._top = self._top + ui.position.top;
					self._left = self._left + ui.position.left;
					
					// Update position
					$(rec).css('top', 0).css('left', 0);
					$(content).css('left', 0).css('top', self._height);
					$(container).css('top', self._top).css('left', self._left);
				}
			});
		
		$(container).mouseover(function() {
				$(content).show();
			})
			.mouseout(function() {
				$(content).hide();	
			});
	},
	
	_handleUpdate: function() {
		if (this._id != null && !this._editable) {
			return;
		}
		
		var data = { id: this._id, fileId: this._photoId, top: this._top, left: this._left, width: this._width, height: this._height, content: this._content };	
		var url = (this._id == null && this._creatable) ? this._options.addUrl : this._options.editUrl;
	
		var self = this;
		$.ajaxq('multimedia_note_update', {		
			url: url,
			cache: false,
			type: 'POST',
			data: data,
			success: function(response) {
				if (self._id == null) {
					self._id = response;
					
					if (!self._editable) {
						// Disable EditInPlace editor
						$('#note_content_' + self._index).unbind('mouseover').unbind('mouseout').unbind('click');
						// Disable Save button
						$('#note_save_' + self._index).unbind('click').hide();
					}
				}
			}
		});	
	},
	
	_handleDelete: function() {
		var self = this;
		if (self._id == null) {
			return;
		}
		if (confirm(self._options.deleteConfirmText)) {
			$.ajaxq('multimedia_note_update', {
				url: self._options.deleteUrl,
				type: 'POST',
				data: { id: self._id },
				success: function(response) {
					$('#note_container_' + self._index).remove();
				}
			});
		}
	}
};

/**
 * Create new note container
 * 
 * @param element image
 * @param int photoId
 * @param array options Note options includes properties:
 * - addUrl: URL to add note
 * - editUrl: URL to update note
 * - deleteUrl: URL to delete note
 * - defaultContent: Default note content when user create note
 * - savingText: Text show when user update note content
 * - saveButton: Label of update button
 * - deleteButton: Label of delete button
 * - deleteConfirmText: Text that requires confirmation when user are going to delete note
 * @param bool creatable Allows user to add note or not
 * @param bool editable Allows user to edit note or not
 * @param bool removable Allows user to remove note or not
 */
Tomato.Multimedia.Note.Container = function(image, photoId, options, creatable, editable, removable) {
	/** Image selector */
	this._image 	= image;
	this._photoId 	= photoId;
	
	this._options 	= options;
	
	/** Array of notes */
	this._notes 	= [];
	
	if (creatable) {
		var self = this;
		$(image).imgAreaSelect({
			handles: true,
			autoHide: true,
	        onSelectEnd: function(img, selection) {
				var note = new Tomato.Multimedia.Note(null, selection.y1, selection.x1, selection.width, selection.height, options.defaultContent, true, editable, removable);
				self.add(note);
				note.show();
			}
	    });
	}
};

Tomato.Multimedia.Note.Container.prototype = {
	/**
	 * Getters
	 */
	getImage: function() { return this._image; },
	getPhotoId: function() { return this._photoId; },
	getOptions: function() { return this._options; },
	
	/**
	 * Add note to container
	 * @param Tomato.Multimedia.Note note
	 */
	add: function(note) {
		note.setContainer(this);
		note.setIndex(this._notes.length);
		this._notes[this._notes.length] = note;
	},
	
	/**
	 * Show all notes belonging to container
	 */
	show: function() {
		for (var i in this._notes) {
			this._notes[i].show();
		}
	},
	
	/**
	 * Hide all notes
	 */
	hide: function() {
		$(this._image).parent().find('.t_multimedia_note').hide();
	}
};
