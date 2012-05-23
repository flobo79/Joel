/**
 * 
 * The DOM.CSS Class provides useful methods to manipulate 
 * style sheet definitions right in the DOM Collection. Rather than applying
 * different/additional CSS Rules to DOM elements, this class changes the CSS
 * Rules in the dom-collection, that means as soon as a Rule changes, it changes globally 
 * and applies to all usages.
 * 
 * @Example: This changes the style definition of all 'a' tags to color #FFF and marginTop:3px
 * var css = new CSS();
 * if(css.exists('a')) {
 *   css.set('a', {color:'#FFF', marginTop:'3px'});
 * }
 * 
 * @Package DOM
 * @author flobo <bosselmann@gmail.com>
 */
var CSS = Class({
	CSSStyles:{},
	
	initialize:function() {
		this.ident = document.styleSheets[0].cssRules ? 'cssRules' : 'rules';
		for(c=0;c<2;c++) {
			var collection = document.styleSheets[c][this.ident];
			for (i = 0; i < collection.length; i++ ) {
				var title = collection[i].selectorText.split(",");
				title.each(function(e) {
					if(e) { this[e] = {c:c,i:i}; }
				},this.CSSStyles);
			}
		}
	},
	
	/**
	 * checks whether a selector name exists or not
	 * 
	 * @param {String} name of Selector
	 * @return {Boolean}
	 */
	exists:function(name) {
		return typeof this.CSSStyles[name] == 'object' ? this.CSSStyles[name] : false;
	},
	
	/**
	 * Modify a style definition by adding one or more pairs of 
	 * stylename and value
	 * 
	 * @param {String} name of Selector
	 * @param {Object} multiple style definitions 
	 * @return {Boolean} true if style has been updated, false if not found
	 */
	set:function(n,p) {
		if(!(el = this.exists(n))) { return false; }
		if(this.j.debug) { console.log('df'); }
		for(e in p) { document.styleSheets[el.c][this.ident][el.i].style[e] = p[e]; }
		return true;
	},
	
	/**
	 * Returns a Style Definition value
	 * 
	 * @param {String}  name of Selector
	 * @param {String} name of Value to be returned
	 * @return {String} value of Selector
	 */
	get:function(name,param) {
		if(!this.CSSStyles[name]) {return false;}
		return this.CSSStyles[name][param];
	}
});

var DOM = {};
DOM.CSS = new CSS();