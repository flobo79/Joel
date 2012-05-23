

var utils = {
	input:{
		 /**
		 * finds a radio button set and detemines which one is currently
		 * checked
		 * 
		 * @param {} name of radioGroup
		 * @return {} value of checked radio button
		 */
		radioValue:function(radioGroup, getElement) {
			var bind = { 'element':false, 'value':false };	
			var radios = $$('input[name='+radioGroup+']').each(function(el) {
				if(el.checked) {
					this.element = el;
					this.value = Element.value;
					return;
				}
			},bind);
			return typeof getElement != "undefined" ? bind.element : bind.value;
		},
		
 		insertAtCursor:function(input, str) {
			input.focus();
			  /* für Internet Explorer */
			  if(typeof document.selection != 'undefined') {
			    /* Einfügen des Formatierungscodes */
			    var range = document.selection.createRange();
			    //var insText = range.text;
			    range.text = str;
				
			    /* Anpassen der Cursorposition */
			    range = document.selection.createRange();
			    range.moveStart('character', str.length);      
			    range.select();
			  }
			  
			  /* für neuere auf Gecko basierende Browser */
			  else if(typeof input.selectionStart != 'undefined')
			  {
			    /* Einfügen des Formatierungscodes */
			    var start = input.selectionStart;
			    var end = input.selectionEnd;
			    input.value = input.value.substr(0, start)  + str + input.value.substr(end);
			    /* Anpassen der Cursorposition */
			    var pos = start + str.length;
			    input.selectionStart = pos;
			    input.selectionEnd = pos;
			  }
		}
	},
	
	String:{
		isEmail:function (email) {
		  var usr    = "([a-zA-ZäöüÄÖÜ0-9][a-zA-ZäöüÄÖÜ0-9_.-]*|\"([^\\\\\x80-\xff\015\012\"]|\\\\[^\x80-\xff])+\")";
		  var domain = "([a-zA-ZäöüÄÖÜ0-9][a-zA-ZäöüÄÖÜ0-9._-]*\\.)*[a-zA-ZäöüÄÖÜ0-9.][a-zA-ZäöüÄÖÜ0-9._-]*\\.[a-zA-Z]{2,5}";
		  var regex  = "^" + usr + "\@" + domain + "$";
		  var rgx    = new RegExp(regex);
		  return rgx.exec(email) ? true : false;
		},
	
		toNumber:function(str) {
			return Number(str.toString().split(',').join('.'));
		}
	},
	
	/**
	 * checks if the keyCode is either an integer or 8 or 9
	 * 
	 * @param {} keyCode
	 * @param {} key
	 * @return {}
	 */
	checkKey:function (keyCode, key){
		// allowed keys are numbers backspace and tab
		if ('8,9,'.indexOf(keyCode + ",") != -1) {
			return { cancelKey: false };
		}
	  	return { cancelKey: "0123456789".indexOf(key) == -1 };
	},

	/**
	 *  taken from Cyanide_7 
	 *  this function formats a number to a dollar currency string
	 *  
	 */
	formatCurrency:function(num) {
		 prefix = prefix || '';
		   num += '';
		   var splitStr = num.split('.');
		   var splitLeft = splitStr[0];
		   var splitRight = splitStr.length > 1 ? '.' + splitStr[1] : '';
		   var regx = /(\d+)(\d{3})/;
		   while (regx.test(splitLeft)) {
		      splitLeft = splitLeft.replace(regx, '$1' + ',' + '$2');
		   }
		   return splitLeft + splitRight;
	},
	
	/**
	 * Finds and returns the x,y scroll Postition of the window, crossbrowser compliant
	 * 
	 * @return {x,y} x and y postition in pixel
	 * @example:<code>var xpos = getScrollXY().x;<br>var scroll = getScrollXY(); alert(scroll.y); // alerts y scroll position in pixel </code>
	 */
	getScrollXY:function() {
	  var scrOfX = 0, scrOfY = 0;
	  if( typeof( window.pageYOffset ) == 'number' ) {
	    //Netscape compliant
	    scrOfY = window.pageYOffset;
	    scrOfX = window.pageXOffset;
	  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
	    //DOM compliant
	    scrOfY = document.body.scrollTop;
	    scrOfX = document.body.scrollLeft;
	  } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
	    //IE6 standards compliant mode
	    scrOfY = document.documentElement.scrollTop;
	    scrOfX = document.documentElement.scrollLeft;
	  }
	  return {'x':scrOfX, 'y':scrOfY};
	},
	
	
	/**
	 * decodes a html entity string into its real text
	 * @param {} str
	 * @return {}
	 */
	html_entity_decode:function(str) {
	  var ta=document.createElement("textarea");
	  ta.innerHTML=str.replace(/</g,"&lt;").replace(/>/g,"&gt;");
	  return ta.value;
	},
	
	iecompattest:function () {
		return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body;
	},
	
	utf8_encode:function(rohtext) {
	     // dient der Normalisierung des Zeilenumbruchs
	     rohtext = rohtext.replace(/\r\n/g,"\n");
	     var utftext = "";
	     for(var n=0; n<rohtext.length; n++) {
	         // ermitteln des Unicodes des  aktuellen Zeichens
	         var c = rohtext.charCodeAt(n);
	         // alle Zeichen von 0-127 => 1byte
	         if (c < 128) {
			 	utftext += String.fromCharCode(c);
			 // alle Zeichen von 127 bis 2047 => 2byte
				}
				else 
					if ((c > 127) && (c < 2048)) {
						utftext += String.fromCharCode((c >> 6) | 192);
						utftext += String.fromCharCode((c & 63) | 128);
					}
					// alle Zeichen von 2048 bis 66536 => 3byte
					else {
						utftext += String.fromCharCode((c >> 12) | 224);
						utftext += String.fromCharCode(((c >> 6) & 63) | 128);
						utftext += String.fromCharCode((c & 63) | 128);
					}
	         }
	     return utftext;
	},
	
	
	utf8_decode:function (utftext) {
	     var plaintext = ""; var i=0; var c=0; var c1= 0; var c2=0;
	     // while-Schleife, weil einige Zeichen uebersprungen werden
	     while(i<utftext.length)
	         {
	         c = utftext.charCodeAt(i);
	         if (c<128) {
	             plaintext += String.fromCharCode(c);
	             i++;
			} else if((c>191) && (c<224)) {
	             c2 = utftext.charCodeAt(i+1);
	             plaintext += String.fromCharCode(((c&31)<<6) | (c2&63));
	             i+=2;
			} else {
	             c2 = utftext.charCodeAt(i+1); c3 = utftext.charCodeAt(i+2);
	             plaintext += String.fromCharCode(((c&15)<<12) | ((c2&63)<<6) | (c3&63));
	             i+=3;
			}
	     }
	     return plaintext;
	 },

	/**
	 * Assign actions defined in Object myActions to specified Elements
	 * 
	 * Expecting an Object named "myActions" containing identifiers and actions to 
	 * apply to the identifier.
	 * 
	 * Define myActions like this:<br>
	 * <code>
	 * myActions = {<br>
	 *   '#bu_newproject': function (e) { <br>
	 *	 	e.onclick= function () { newProject(); }<br>
	 *	 },<br>
	 *   '.hover': function (e) { <br>
	 *	 	e.onmouseover= function () { e.addClass('hover'); }<br>
	 *	 }<br>
	 * </code>
	 */
	
	applyBehaviour:function (setrules) {
		rules = [];
		rules.push(setrules);
		rules.each(function(rule) {
			for (selector in rule) {
				$$(selector).each(function(element) {
					rule[selector](element);
				});
			}
		});
	}
};

/**
*
*  Javascript sprintf
*  http://www.webtoolkit.info/
*
*
**/

sprintfWrapper = {

	init : function () {

		if (typeof arguments == "undefined") { return null; }
		if (arguments.length < 1) { return null; }
		if (typeof arguments[0] != "string") { return null; }
		if (typeof RegExp == "undefined") { return null; }

		var string = arguments[0];
		var exp = new RegExp(/(%([%]|(\-)?(\+|\x20)?(0)?(\d+)?(\.(\d)?)?([bcdfosxX])))/g);
		var matches = new Array();
		var strings = new Array();
		var convCount = 0;
		var stringPosStart = 0;
		var stringPosEnd = 0;
		var matchPosEnd = 0;
		var newString = '';
		var match = null;

		while (match = exp.exec(string)) {
			if (match[9]) { convCount += 1; }

			stringPosStart = matchPosEnd;
			stringPosEnd = exp.lastIndex - match[0].length;
			strings[strings.length] = string.substring(stringPosStart, stringPosEnd);

			matchPosEnd = exp.lastIndex;
			matches[matches.length] = {
				match: match[0],
				left: match[3] ? true : false,
				sign: match[4] || '',
				pad: match[5] || ' ',
				min: match[6] || 0,
				precision: match[8],
				code: match[9] || '%',
				negative: parseInt(arguments[convCount]) < 0 ? true : false,
				argument: String(arguments[convCount])
			};
		}
		strings[strings.length] = string.substring(matchPosEnd);

		if (matches.length == 0) { return string; }
		if ((arguments.length - 1) < convCount) { return null; }

		var code = null;
		var match = null;
		var i = null;

		for (i=0; i<matches.length; i++) {

			if (matches[i].code == '%') { substitution = '%' }
			else if (matches[i].code == 'b') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(2));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'c') {
				matches[i].argument = String(String.fromCharCode(parseInt(Math.abs(parseInt(matches[i].argument)))));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'd') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'f') {
				matches[i].argument = String(Math.abs(parseFloat(matches[i].argument)).toFixed(matches[i].precision ? matches[i].precision : 6));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'o') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(8));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 's') {
				matches[i].argument = matches[i].argument.substring(0, matches[i].precision ? matches[i].precision : matches[i].argument.length)
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'x') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'X') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]).toUpperCase();
			}
			else {
				substitution = matches[i].match;
			}

			newString += strings[i];
			newString += substitution;

		}
		newString += strings[i];

		return newString;

	},

	convert : function(match, nosign){
		if (nosign) {
			match.sign = '';
		} else {
			match.sign = match.negative ? '-' : match.sign;
		}
		var l = match.min - match.argument.length + 1 - match.sign.length;
		var pad = new Array(l < 0 ? 0 : l).join(match.pad);
		if (!match.left) {
			if (match.pad == "0" || nosign) {
				return match.sign + pad + match.argument;
			} else {
				return pad + match.sign + match.argument;
			}
		} else {
			if (match.pad == "0" || nosign) {
				return match.sign + match.argument + pad.replace(/0/g, ' ');
			} else {
				return match.sign + match.argument + pad;
			}
		}
	}
}

sprintf = sprintfWrapper.init;


/** php style file include for javascript files */
function include(script_filename) {
    var html_doc = document.getElementsByTagName('head').item(0);
    var js = document.createElement('script');
    js.setAttribute('language', 'javascript');
    js.setAttribute('type', 'text/javascript');
    js.setAttribute('src', script_filename);
    html_doc.appendChild(js);
    return false;
}

var included_files = [];
function include_once(script_filename) {
    if (!in_array(script_filename, included_files)) {
        included_files[included_files.length] = script_filename;
        include(script_filename);
    }
}


/** 
 * Searches for a Get parameter in the URL
 * 
 * @param {String} key
 * @param {String} url
 */
function $get(key, url){  
    if (arguments.length < 2) { url = location.href; }
    if(arguments.length > 0 && key !== "") {  
        if(key == "#"){  
            var regex = new RegExp("[#]([^$]*)");  
        } else if(key === "?"){  
            var regex = new RegExp("[?]([^#$]*)");  
        } else {  
            var regex = new RegExp("[?&]"+key+"=([^&#]*)");  
        }  
        var results = regex.exec(url);  
        return (results === null )? "" : results[1];  
    } else {  
        url = url.split("?");  
        var results = {};  
            if(url.length > 1){  
                url = url[1].split("#");  
                if (url.length > 1) {
					results.hash = url[1];
				} 
                url[0].split('&').each(function(item,index){  
                    item = item.split('=');  
                    results[item[0]] = item[1];  
                });  
            }  
        return results;  
    }  
}  


function htmlentities(string){
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: nobbler
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // %          note: table from http://www.the-art-of-web.com/html/character-codes/
    // *     example 1: htmlentities('Kevin & van Zonneveld');
    // *     returns 1: 'Kevin &amp; van Zonneveld'
 
    var histogram = {}, code = 0, tmp_arr = [], i = 0;
    var stringl = 0;
 
    histogram['34'] = 'quot';
    histogram['38'] = 'amp';
    histogram['60'] = 'lt';
    histogram['62'] = 'gt';
    histogram['160'] = 'nbsp';
    histogram['161'] = 'iexcl';
    histogram['162'] = 'cent';
    histogram['163'] = 'pound';
    histogram['164'] = 'curren';
    histogram['165'] = 'yen';
    histogram['166'] = 'brvbar';
    histogram['167'] = 'sect';
    histogram['168'] = 'uml';
    histogram['169'] = 'copy';
    histogram['170'] = 'ordf';
    histogram['171'] = 'laquo';
    histogram['172'] = 'not';
    histogram['173'] = 'shy';
    histogram['174'] = 'reg';
    histogram['175'] = 'macr';
    histogram['176'] = 'deg';
    histogram['177'] = 'plusmn';
    histogram['178'] = 'sup2';
    histogram['179'] = 'sup3';
    histogram['180'] = 'acute';
    histogram['181'] = 'micro';
    histogram['182'] = 'para';
    histogram['183'] = 'middot';
    histogram['184'] = 'cedil';
    histogram['185'] = 'sup1';
    histogram['186'] = 'ordm';
    histogram['187'] = 'raquo';
    histogram['188'] = 'frac14';
    histogram['189'] = 'frac12';
    histogram['190'] = 'frac34';
    histogram['191'] = 'iquest';
    histogram['192'] = 'Agrave';
    histogram['193'] = 'Aacute';
    histogram['194'] = 'Acirc';
    histogram['195'] = 'Atilde';
    histogram['196'] = 'Auml';
    histogram['197'] = 'Aring';
    histogram['198'] = 'AElig';
    histogram['199'] = 'Ccedil';
    histogram['200'] = 'Egrave';
    histogram['201'] = 'Eacute';
    histogram['202'] = 'Ecirc';
    histogram['203'] = 'Euml';
    histogram['204'] = 'Igrave';
    histogram['205'] = 'Iacute';
    histogram['206'] = 'Icirc';
    histogram['207'] = 'Iuml';
    histogram['208'] = 'ETH';
    histogram['209'] = 'Ntilde';
    histogram['210'] = 'Ograve';
    histogram['211'] = 'Oacute';
    histogram['212'] = 'Ocirc';
    histogram['213'] = 'Otilde';
    histogram['214'] = 'Ouml';
    histogram['215'] = 'times';
    histogram['216'] = 'Oslash';
    histogram['217'] = 'Ugrave';
    histogram['218'] = 'Uacute';
    histogram['219'] = 'Ucirc';
    histogram['220'] = 'Uuml';
    histogram['221'] = 'Yacute';
    histogram['222'] = 'THORN';
    histogram['223'] = 'szlig';
    histogram['224'] = 'agrave';
    histogram['225'] = 'aacute';
    histogram['226'] = 'acirc';
    histogram['227'] = 'atilde';
    histogram['228'] = 'auml';
    histogram['229'] = 'aring';
    histogram['230'] = 'aelig';
    histogram['231'] = 'ccedil';
    histogram['232'] = 'egrave';
    histogram['233'] = 'eacute';
    histogram['234'] = 'ecirc';
    histogram['235'] = 'euml';
    histogram['236'] = 'igrave';
    histogram['237'] = 'iacute';
    histogram['238'] = 'icirc';
    histogram['239'] = 'iuml';
    histogram['240'] = 'eth';
    histogram['241'] = 'ntilde';
    histogram['242'] = 'ograve';
    histogram['243'] = 'oacute';
    histogram['244'] = 'ocirc';
    histogram['245'] = 'otilde';
    histogram['246'] = 'ouml';
    histogram['247'] = 'divide';
    histogram['248'] = 'oslash';
    histogram['249'] = 'ugrave';
    histogram['250'] = 'uacute';
    histogram['251'] = 'ucirc';
    histogram['252'] = 'uuml';
    histogram['253'] = 'yacute';
    histogram['254'] = 'thorn';
    histogram['255'] = 'yuml';
 
    string += '';
    stringl = string.length;
    for (i = 0; i < stringl; ++i) {
        code = string.charCodeAt(i);
        if (code in histogram) {
            tmp_arr[i] = '&'+histogram[code]+';';
        } else {
            tmp_arr[i] = string.charAt(i);
        }
    }
 
    return tmp_arr.join('');
}
    