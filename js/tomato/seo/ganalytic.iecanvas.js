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
 * @version 	$Id: ganalytic.iecanvas.js 5082 2010-08-29 12:07:45Z huuphuoc $
 * @since		2.0.7
 */

'Tomato.Seo.Ganalytic.IECanvas.js'.namespace();

/**
 * Most of implemented here was taken from excanvas library:
 * http://code.google.com/p/explorercanvas/
 * But I rewrite it with more of comments and only use simple methods for
 * this module functions
 */

if (!document.createElement('canvas').getContext) {
(function() {
	/**
	 * getContext()
	 */
	function getContext() {
		//return this._context || (this._context = new Tomato.Seo.Ganalytic.IECanvas(this));
		return new Tomato.Seo.Ganalytic.IECanvas(this);
	};
	
	if (/MSIE/.test(navigator.userAgent) && !window.opera) {
		document.namespaces.add('v', 'urn:schemas-microsoft-com:vml', '#default#VML');
		document.namespaces.add('o', 'urn:schemas-microsoft-com:office:office');
		
		/**
		 * Add VML behavior styles to head
		 */
		var s = document.createElement('style');
		s.setAttribute('type', 'text/css');
		if (s.styleSheet) {
		    s.styleSheet.cssText = 'v\:* { behavior: url(#default#VML); display:inline-block}'
		    						+ 'o\:* { behavior: url(#default#VML); display:inline-block}';
		}
		var head = document.getElementsByTagName('head')[0];
		head.appendChild(s);
		
		//document.createElement('canvas');
		document.attachEvent('onreadystatechange', function() {
			/** 
			 * Add namespaces to document
			 * See http://msdn.microsoft.com/en-us/library/bb264142(v=VS.85).aspx
			 */
			var canvasElements = document.getElementsByTagName('canvas');
			for (var i = 0; i < canvasElements.length; i++) {
				if (!canvasElements[i].getContext) {
					canvasElements[i].getContext = getContext;
				}
			}
		});
	}
})();
}

/**
 * @see http://dev.w3.org/html5/canvas-api/canvas-2d-api.html
 */
Tomato.Seo.Ganalytic.IECanvas = function(element) {
	this._canvas = element;
	
//	var el = element.ownerDocument.createElement('div');
//    el.style.width  = element.clientWidth + 'px';
//    el.style.height = element.clientHeight + 'px';
//    el.style.overflow = 'hidden';
//    el.style.position = 'absolute';
//    element.appendChild(el);
//    this._element = el;
    
    this._paths = [];
    this._lineScale = 1;
    
    /**
     * Default: 1.0
     */
    this.globalAlpha = 1.0;
    
    /**
     * Default: black
     */
    this.fillStyle = '#000';
    
    /**
     * Default: black
     */
    this.strokeStyle = '#000';
    
    /**
     * Can take one of values: 'miter', 'round', 'bevel'
     * Default: 'miter'
     */
    this.lineJoin = 'miter';
    
    /**
     * Default: 1px
     */
    this.lineWidth = 1;
    
    /**
     * Default: 10
     */
    this.miterLimit = 10;
    
    /**
     * Get one of values: 'butt', 'round', 'square'
     * Default: butt
     */
    this.lineCap = 'butt';
};

Tomato.Seo.Ganalytic.IECanvas.prototype.clearRect = function() {
	//$(this._element).html('');
	this._canvas.innerHtml = '';
};

Tomato.Seo.Ganalytic.IECanvas.prototype.beginPath = function() {
	this._paths = [];
};

Tomato.Seo.Ganalytic.IECanvas.prototype.moveTo = function(x, y) {
	var p = this._getCoordinates(x, y);
	this._paths.push({type: 'moveTo', x: p.x, y: p.y});
};

Tomato.Seo.Ganalytic.IECanvas.prototype.lineTo = function(x, y) {
	var p = this._getCoordinates(x, y);
    this._paths.push({type: 'lineTo', x: p.x, y: p.y});
};

Tomato.Seo.Ganalytic.IECanvas.prototype.closePath = function() {
	this._paths.push({ type: 'closePath'});
};

Tomato.Seo.Ganalytic.IECanvas.prototype.fill = function() {
	this.stroke(true);
};

/**
 * Output VML
 * 
 * @return void
 */
Tomato.Seo.Ganalytic.IECanvas.prototype.stroke = function(fill) {
	var output = [];
	
	/**
	 * See the list of Shape element properties:
	 * http://msdn.microsoft.com/en-us/library/bb263897(v=VS.85).aspx
	 */
	output.push('<v:shape',
				' filled="', !!fill, '"',
				' stroked="', !fill, '"',
				' style="position: absolute; width: ', 10, 'px; height: ', 10, 'px;"',
				' coordorigin="0 0" coordsize="', 100, ' ', 100, '"',
				' path="');
	for (var i = 0; i < this._paths.length; i++) {
		var p = this._paths[i];
		switch (p.type) {
			case 'moveTo':
				output.push(' m ', Math.round(p.x), ',', Math.round(p.y));
				break;
			case 'lineTo':
				output.push(' l ', Math.round(p.x), ',', Math.round(p.y));
				break;
			case 'closePath':
				output.push(' x ');
				break;
		}
	}
	output.push(' ">');
	
	var style   = this._normalizeStyle(fill ? this.fillStyle : this.strokeStyle);
    var color   = style.color;
    var opacity = style.alpha * this.globalAlpha;
	
	if (!fill) {
		var lineWidth = this._lineScale * this.lineWidth;
		if (lineWidth < 1) {
			opacity *= lineWidth;
		}
		/**
		 * Stroke element properties:
		 * http://msdn.microsoft.com/en-us/library/bb264077(v=VS.85).aspx
		 */
		output.push(
	        '<v:stroke',
	        ' opacity="', opacity, '"',
	        ' joinstyle="', this.lineJoin, '"',
	        ' miterlimit="', this.miterLimit, '"',
	        ' endcap="', this._normalizeLineCap(this.lineCap), '"',
	        ' weight="', lineWidth, 'px"',
	        ' color="', color, '" />');		
	} else {
		output.push('<v:fill color="', color, 
					'" opacity="', opacity,
        			'" />');
	}
	output.push('</v:shape>');
    this._canvas.insertAdjacentHTML('beforeEnd', output.join(''));
};

Tomato.Seo.Ganalytic.IECanvas.prototype._getCoordinates = function(x, y) {
	var m = [
	            [1, 0, 0],
	            [0, 1, 0],
	            [0, 0, 1]
	          ];
    return {
      x: 10 * (x * m[0][0] + y * m[1][0] + m[2][0]) - 5,
      y: 10 * (x * m[0][1] + y * m[1][1] + m[2][1]) - 5
    };
};

/**
 * Convert fill style or stroke style in Canvas to VML format
 * 
 * @param string color The color in various formats:
 * - Hexa: #xxxxxx
 * - RGB: rgba(R, G, B, alpha)
 * @return object An object with two properties:
 * - color in Hexa code
 * - alpha
 */
Tomato.Seo.Ganalytic.IECanvas.prototype._normalizeStyle = function(color) {
	var str, alpha = 1;
	
	/**
	 * Ensure that the input is string
	 */
	color = String(color);
	
	/**
	 * The input has format of rgb
	 */
	if (color.substring(0, 3) == 'rgb') {
		var start = color.indexOf('(', 3);
		var end   = color.indexOf(')', start + 1);
		var temp  = color.substring(start + 1, end).split(',');
		
		str = '#';
		var hex;
		for (var i = 0; i < 3; i++) {
			/**
			 * Convert int to Hexa
			 */
			hex = parseInt(temp[i]).toString(16);
			str += (hex.length < 2) ? '0' + hex : hex;
		}
		if (temp.length == 4 && color.substr(3, 1) == 'a') {
			alpha = temp[3];
		}
	} 
	/**
	 * The input has format of Hex
	 */
	else {
		str = color;
	}
	return { color: str, alpha: alpha };
};

/**
 * Convert lineCap in Canvas to VML format
 * 
 * @see EndCap Attribute: http://msdn.microsoft.com/en-us/library/bb229428(v=VS.85).aspx
 * @param string lineCap
 * @return string
 */
Tomato.Seo.Ganalytic.IECanvas.prototype._normalizeLineCap = function(lineCap) {
	switch (lineCap) {
    	case 'butt':
    		return 'flat';
    	case 'round':
    		return 'round';
    	case 'square':
    	default:
    		return 'square';
	}
};
