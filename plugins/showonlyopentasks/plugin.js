/**
 * Tasklist filter
 * 
 * adds a checkbox to the tasklist to filter hide finished tasklist
 * 
 * @requires Class CSS()
 * @author flobo <bosselmann@gmail.com>
 * 
 */
var plugin_showonlyopentasks = {
	
		box:new Element('div',{
		id:"showonlyopentasks",
		html:'hide finished tasks',
		styles:{ 'float': 'right', fontSize:'11px', padding:0, margin:0, marginTop:'5px'}
	}),
	
	checkbox:new Element('input',{
		type:"checkbox",
		styles:{'float':'left',padding:0, margin:0, marginRight:'2px',marginTop:'-2px' },
		id:"ch_showonlyopentasks",
	    'events': {
	        'click': function() {
	        	if(this.checked) {
					DOM.CSS.set('.t_row.ready',{display:'none'});
	        	} else {
	        		DOM.CSS.set('.t_row.ready',{display:'block'});
				}
	        }
	     }
	}),
	
	events:function(fnc, param) {
		if (fnc == 'tasklist.load') {
			if ($defined(DOM.CSS)) {
				if ($('tasklist')) {
					this.box.inject($('tasklist'), 'top');
					this.checkbox.inject(this.box, 'top');
				}
			}
		} else if (fnc == 'joel.init') {
			if(!$defined(DOM.CSS) && !$defined(CSS)) {
				alert("Plugin 'ShowOnlyOpenTasks' requires class DOM.CSS to manipulate the DOM CSS Collection");
			} else {
				DOM.CSS = new CSS();
			}
		}
	}
}