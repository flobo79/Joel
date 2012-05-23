/**
 * Adds Project Details features to the Tasklist
 *  - edit Project Title
 * 
 */
var plugin_projectdetails = {
	editTitle:function (event) {
		var _root = this;
		new Element('input',{
			type:'text',
			id:'project_details_title',
			value:event.target.innerHTML,
			events:{
				'dblclick':function() {},
				'keyup':function(event) {
				if(event.key == 'enter') {
					var value = event.target.value;
					projects[currentProject.index].p_name = value;
					currentProject.p_name = value;
					tablist.show();
					new Element('div',{
						id:'project_details_title',
						html:value,
						events:{
							'dblclick':function (e) { _root.editTitle(e); }
						}
					}).replaces($('project_details_title'));
					
					request('project_update',{'p_name':value},function (r) { if(r) { alert(r); }});
				}
			}
		}}).replaces($('project_details_title'));
	},
	
	events:function(fnc, param) {
		if(fnc == 'tasklist.load') {
			if($('tasklist')) {
				var _root = this;
				new Element('div',{
					id:'project_details',
					html:'<div id="project_details_title">'+currentProject.p_name+'</div>'
				}).inject('tasklist', 'top');
				$('project_details_title').addEvent('dblclick', function(e) { _root.editTitle(e); });
			}
		}
	}
}