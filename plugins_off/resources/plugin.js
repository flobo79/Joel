/**
 * Admin Panel
 * 
 */
var plugin_resources = {
	currentView:'viewProjects',
	events:function(fnc, param) {
		if(fnc === 'topmenu.show') { 
			$('topmenu').innerHTML += '<div> | </div><div onclick="plugin_resources.load();">Resouces</div>';
			
		
		}
	},
	
	load:function () {
		var _this = this;

		j.request('plugin_resources:load', '', function(res, b, c, d){
			//$('r_content').innerHTML = res;
			
			
		},false,false,true);
	},
	
	viewProjects:function() {
		this.currentView = 'viewProjects';
		j.getHTML('plugin_resources/load-view', {view:this.currentView}, function(res){
			
			$('r_content').innerHTML = res;
			
		},false,false,false);
	}
};