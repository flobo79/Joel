/**
 * @author flobo
 */

var Tablist = new Class({
	name:'tablist',
	
	initialize: function(){
		$('tablistbox').setStyle('opacity',0);
    },
	
	show:function() {
		var tablistHtml = '';
		if(!j.user_id) {
			project = 'login';
			tablistHtml = '<div class="tab" id="tab_login"><div class="label">login</div><div class="close" style="width:7px;"></div></div>';
		
		} else {
			
			var addclass = project == 0 ? ' current' : '';
			tablistHtml = '<div class="tab'+addclass+'" onclick="j.project.select(\'summary\')" id="tab_summary"><div class="label">Summary</div><div class="close" style="width:7px;"></div></div>';
			
			j.user.u_bookmarks.each(function(e) {
				addclass = e.project_id == project ? ' current' : '';
				if(e = j.project.get(e.project_id)) {
					tablistHtml += '<div class="tab'+addclass+'" id="tab_'+e.project_id+'"><div class="label" onclick="j.project.select(\''+e.project_id+'\')">'+e.p_name+'</div><div class="close" onclick="tablist.deleteBookmark('+e.project_id+')"> </div></div>';
				
					
				}
			});
			
			//tablistHtml += '<div class="tab" onclick="j.project.create_show()" id="tab_new"><div class="label">&nbsp;+</div><div class="close" style="width:7px;"></div></div>';
		}
		$('tablistbox').innerHTML = tablistHtml;
		$('tablistbox').fade('in');
	},
	
	deleteBookmark:function(projectid) {
		j.request('deleteBookmark',{'id':projectid});
		j.projects.list.each(function(e,i) {
			if(e.project_id == projectid) { 
				if(e.bookmarked) { $('tab_'+projectid).dispose(); }
				projects[i]['bookmarked'] = 0;
			}
		});
		
		if(projectid == project) { project_select('summary'); }
	},
	
	events:function(type, param) {
		if(type === 'summary.show' || type === 'joel.start' ||type === 'user.logout' || type === 'project.select') { this.show(); }
		else if(type == 'project.delete') { this.deleteBookmark(id); }
	}
});
