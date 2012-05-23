/** 
 * JS Prototype Collection
 * 
 * requires Mootools 1.2
 * 
 * @package Prototypes
 * @param {Object} obj
 */

/**
 * Element.hide
 * 
 * Hides an Element and optionally applies additional style definitions
 * 
 * @param {Object} obj optional additional Style definitions to be applied
 * @return Element
 * @example $('element_id').hide({fontWeight:'bold'});
 */
Element.prototype.hide = function(obj){ this.style.display = 'none'; if (nil(obj)) { this.setStyles(obj); } return this; };

/**
 * Element.show
 * 
 * Shows an Element and optionally applies additional style definitions
 * 
 * @param {Object} obj optional additional Style definitions to be applied
 * @return Element
 * @example $('element_id').show({fontWeight:'bold'});
 */
Element.prototype.show=function(obj) { this.style.display='block'; if(nil(obj)) { this.setStyles(obj); } return this; };

/**
 * Element.toggle
 * 
 * Toggles the display of an Element and optionally applies additional style definitions
 * 
 * @param {Object} obj optional additional Style definitions to be applied
 * @return Element
 * @example <!CDATA[$('element_id').toggle({fontWeight:'bold'}); ]]>
 */
Element.prototype.toggle=function(obj) { (this.style.display === 'block' || this.style.display === '') ? 'none' : 'block';  if (nil(obj)) { this.setStyles(obj); } return this; };

/**
 * String.in_object(obj)
 * 
 * Checks if a String exists as key in an object
 * 
 * @param {Object} obj to be searched
 * @return {Bool}
 * @example <![CDATA[  alert('apple'.in_object(fruits)); ]]>
 */
String.prototype.in_object=function(obj) { if(nil(obj[this])){return true;} return false; };
String.prototype.trim = function() {   return this.replace(/^\s+|\s+$/g,""); };
String.prototype.ltrim = function() { return this.replace(/^\s+/g,""); };
String.prototype.rtrim = function() {   return this.replace(/\s+$/g,""); };
String.prototype.addslashes = function () { if($type(str) != 'string') { return str; } return str.replace(/\'/g,'\\\'').replace(/\"/g,'\\"').replace(/\\/g,'\\\\').replace(/\0/g,'\\0'); };
String.prototype.stripslashes = function() { if ($type(str) != 'string')  { return str; } return str.replace(/\\'/g, '\'').replace(/\\"/g, '"').replace(/\\\\/g, '\\').replace(/\\0/g, '\0'); };
Window.prototype.getDimensions = function(dim) {
	var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
	if(dim == 'x') {
		return (myWidth);
	} else if (dim == 'y') {
		return (myHeight);
	} else {
		return [myWidth, myHeight];
	}
};

Window.prototype.getCenter = function (dim) {
	var winsize = getWindowSize();
	var scroll = utils.getScrollXY();
	
	if(dim == 'x') {
		return (((winsize[0])/2)+scroll[0]);
	} else if (dim == 'y') {
		return (((winsize[1])/2)+scroll[1]);
	} else {
		return [(((winsize[0])/2)+scroll[0]), (((winsize[1])/2)+scroll[1])];
	}
};
