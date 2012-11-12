/**
 * Mega menu implemented by Soh Tanaka
 * http://www.sohtanaka.com/web-design/mega-drop-downs-w-css-jquery/
 */

function megaHoverOver() {
	$(this).find(".sub").stop().fadeTo('fast', 1).show();

	// Calculate width of all ul's
	(function($) {
		jQuery.fn.calcSubWidth = function() {
			rowWidth = 0;
			// Calculate row
			$(this).find("ul").each(function() {
				rowWidth += $(this).width();
			});
		};
	})(jQuery);

	if ($(this).find(".row").length > 0) { // If row exists...
		var biggestRow = 0;
		// Calculate each row
		$(this).find(".row").each(function() {
			$(this).calcSubWidth();
			// Find biggest row
				if (rowWidth > biggestRow) {
					biggestRow = rowWidth;
				}
			});
		// Set width
		$(this).find(".sub").css( {
			'width' : biggestRow
		});
		$(this).find(".row:last").css( {
			'margin' : '0'
		});

	} else { // If row does not exist...

		$(this).calcSubWidth();
		// Set Width
		$(this).find(".sub").css( {
			'width' : rowWidth
		});

	}
};

function megaHoverOut() {
	$(this).find(".sub").stop().fadeTo('fast', 0, function() {
		$(this).hide();
	});
};
