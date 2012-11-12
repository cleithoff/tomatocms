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
 * @version 	$Id: ganalytic.reporter.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.7
 */

'Tomato.Seo.Ganalytic.Reporter.js'.namespace();

/**
 * @param string id Id of canvas
 * @param object data Object with two properties:
 * - total
 * - entry: Array of report item
 * 
 * Each item is an object with two properties:
 * - dimension
 * - metric
 * @constructor
 */
Tomato.Seo.Ganalytic.Reporter = function(id, data) {
	/**
	 * Id of canvas container
	 */
	this._id = id;
	
	/**
	 * Report data
	 */
	this._data = data;
	
	/**
	 * Show tooltip or not
	 */
	this._enableTooltip = true;
	
	/**
	 * The tooltip string pattern used to show the tooltip
	 */
	this._tooltipPattern = '%s %s';
	
	/**
	 * Callback to format dimension
	 * 
	 * @param string dimension
	 * @return string
	 */
	this._formatDimensionCallback = function(dimension) {
		return dimension;
	};
	
	/**
	 * Callback to format metric
	 * 
	 * @param string metric
	 * @return string
	 */
	this._formatMetricCallback = function(metric) {
		return metric;
	};
};

/**
 * Setters
 */
Tomato.Seo.Ganalytic.Reporter.prototype.enableTooltip = function(enable) { this._enableTooltip = enable; };
Tomato.Seo.Ganalytic.Reporter.prototype.setTooltipPattern = function(pattern) { this._tooltipPattern = pattern; };
Tomato.Seo.Ganalytic.Reporter.prototype.setFormatDimensionCallback = function(callback) { this._formatDimensionCallback = callback; };
Tomato.Seo.Ganalytic.Reporter.prototype.setFormatMetricCallback = function(callback) { this._formatMetricCallback = callback; };

/**
 * Show the report
 * 
 * @return void
 */
Tomato.Seo.Ganalytic.Reporter.prototype.show = function() {
	var canvas  = document.getElementById(this._id);
	var context = canvas.getContext('2d');
	
	var width  = parseInt($(canvas).attr('width'));
	var height = parseInt($(canvas).attr('height'));
	
	context.clearRect(0, 0, width, height);
	context.strokeStyle = '#fff';
	context.fillStyle   = '#fff';
	
	var xPos, yPos;
	
	/**
	 * Draw y-axis
	 */
	yPos = (height) - ((height));
	context.beginPath();
	context.lineWidth = 1;
	context.moveTo(0, yPos);
	context.lineTo(width, yPos);
	context.closePath();
	context.stroke();
	
	yPos = (height) - ((height) / 2);
	context.beginPath();
	context.lineWidth = 1;
	context.moveTo(0, yPos);
	context.lineTo(width, yPos);
	context.closePath();
	context.stroke();
	
	yPos = (height);
	context.beginPath();
	context.lineWidth = 1;
	context.moveTo(0, yPos);
	context.lineTo(width, yPos);
	context.closePath();
	context.stroke();
	
	/**
	 * Draw lines
	 */
	context.fillStyle   = 'red';// 'rgba(255, 255, 255, 0.25)';
	context.strokeStyle = 'yellow';// 'rgb(255, 255, 255)';
	context.beginPath();
	context.lineWidth = 1;
	context.lineCap = 'round';
	
	var numItems  = this._data.entry.length;
	var maxMetric = this._getMaxMetric();
	
	var points = [];
	for (var i = 0; i < numItems; i++) {
		xPos = (i * width) / (numItems - 1);
		yPos = (height) - ((parseFloat(this._data.entry[i].metric) / maxMetric) * (height));
		
		points.push({ x: xPos, y: yPos });
		
		if (i == 0) {
			context.moveTo(xPos, height);
			context.lineTo(xPos, yPos);
		} else {
			context.lineTo(xPos, yPos);
		}
	}
	context.lineTo(xPos, height);
	context.closePath();
	context.stroke();

	/**
	 * Create tooltip container
	 */
	var parentDiv = canvas.parentNode;
	$(parentDiv).find('div').remove();
	
	if (this._enableTooltip) {
		var tooltip = '', dimension = '', metric = '';
		for (var i = 0; i < numItems; i++) {
			/**
			 * Allow user to format dimension and metric using callback
			 */
			dimension = this._formatDimensionCallback(this._data.entry[i].dimension);
			metric    = this._formatMetricCallback(this._data.entry[i].metric);
			tooltip   = sprintf(this._tooltipPattern, dimension, metric);
			
			var div = $('<div/>');
			xPos = points[i].x - 3;
			yPos = points[i].y - 3;
			$(div).css('left', xPos + 'px').css('top', yPos + 'px')
				.css('width', '6px').css('height', '6px')
				.css('position', 'absolute').css('z-index', 9999)
				.css('background', 'yellow')
				.html('&nbsp;')
				.attr('title', tooltip)
				.appendTo(parentDiv);
			
			$(div).tooltip({
				extraClass: 't_a_ui_tooltip',
				bodyHandler: function() {
					/**
					 * We couldn't return tooltip here, because it will take 
					 * the last tooltip.
					 * The trick is get the value of tooltipText attribute
					 */
					return $(this).attr('tooltipText');
				}
			});
		}
	}
};

/**
 * Get maximum matric
 * 
 * @return float
 */
Tomato.Seo.Ganalytic.Reporter.prototype._getMaxMetric = function() {
	var max      = parseFloat(this._data.entry[0].metric);
	var numItems = this._data.entry.length;
	var curr;
	for (var i = 0; i < numItems; i++) {
		curr = parseFloat(this._data.entry[i].metric);
		if (max < curr) {
			max = curr;
		}
	}
	return max;
};
