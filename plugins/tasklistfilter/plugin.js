/**
 * Tasklist filter
 * 
 * adds an input form to the head of a tasklist to filter the tasklist
 * 
 * @author flobo <bosselmann@gmail.com>
 * 
 */
var plugin_tasklistfilter = {
	box:new Element('div',{
		id:"tasklist_filter_box"
	}),
	
	filterinput:new Element('input',{
		type:"text",
		id:"tasklist_filter_input",
	    'events': {
	        'keyup': function() { plugins.plugin_tasklistfilter.filterlist(this.value) },
	        'blur' : function() { if(this.value == "") { this.style.display='none'; plugins.plugin_tasklistfilter.filterbutton.style.display='block'; }}
	     }
	}),
	
	filterbutton:new Element('div',{
		id:"tasklist_filter_button",
		html:'filter tasklist',
		events:{
			'click':function () {
				this.style.display='none';
				plugins.plugin_tasklistfilter.filterinput.style.display='block';
				plugins.plugin_tasklistfilter.filterinput.focus();
			}
		}
	}),
		
	filterlist:function (str) {
		tasklist.each(function(e,i) {
			$('row_'+e.task_id).style.display = (e.t_description.toLowerCase().indexOf(str.toLowerCase()) == -1) ? 'none' : 'block';
		});
	},
	
	events:function(fnc,param) {
		if(fnc == 'tasklist.load') {
			this.box.inject($('tasklist'), 'top');
			this.filterinput.inject(this.box);
			this.filterbutton.inject(this.box);
		}
	}
}