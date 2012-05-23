/**
 * Tasklist filter
 * 
 * adds tooltips to the interface 
 * 
 * @version 1.0
 * @author flobo <bosselmann@gmail.com>
 * 
 */
var plugin_tooltips = {
	_init:function() {
		tipz = new Tips('',{  
	         className: 'tip',
	         fixed: true,  
	         hideDelay: 50,  
	         showDelay: 1000
	    });
		
		tipz.FX = new Fx.Tween(tipz.tip, {duration: 200, transition: Fx.Transitions.Sine.easeOut});
 		
		tipz.addEvents({  
		     'show': function(tip) {  
		      	tipz.FX.start('opacity', 0.85 );
		     },  
		     'hide': function(tip) {  
		         tipz.FX.start('opacity',0 );
		     }  
		 });
	},
	
	_drawTasklist:function(tasklist) {
 		 $$('#tasklist .c3').each(function(element,index) {  
	         element.store('tip:title', 'Task Priority');  
	         element.store('tip:text', 'Click to set the Priority of this task. (ctrl+shift+i)'); 
	     }); 
		 
		 //create the tooltips  
		 tipz.attach($$('.c3'));
	 }
}

