var plugin_shareprojects = {

	events:function(fnc,param) {
		if (fnc == 'tasklist.load') {
			var parent = this;
			var link = new Element('div', {
				id: "shareproject_link",
				html: 'share this project',
				events: {
					click: function(){
						parent.floater = new Modal({
							width: 300,
							windowtitle: 'Share Project',
							content: 'Please enter the email address of the person you want to share this project with:<br><br><input type="text" id="shareproject_with"><br> <div class="button" id="shareprojects_send" >send invitation</div><br><br><div id="send_result" style="clear:both"></div>'
						});
						
						$('shareprojects_send').addEvent('click', function(){
							$('send_result').innerHTML = 'sending request...';
							if (utils.String.isEmail($('shareproject_with').value)) {
								j.request('plugin_shareprojects:sendInvitation', {
									email: $('shareproject_with').value,
									project_id: project
								}, function(res){
									if (res) {
										$('send_result').innerHTML = res;
									}
									else {
										$('send_result').innerHTML = 'Invitation sent.';
										setTimeout(function(){
											parent.floater.close();
										}, 2000);
									}
								});
							}
							else {
								$('send_result').innerHTML = 'Please enter a valid email address.';
							}
						});
					}
				}
			}).inject($('tasklist'), 'top');
		}
		
		else if(fnc == 'topmenu.show') {
			/*
			if($defined(j.user.addProject)) {
				var details = user.addProject;
				var thisplugin = this;
				this.floater = new Modal({
					width:300,
					windowtitle:'Share Project',
					content:'Do you want to gain access to '+details.u_name+'\'s project "'+details.p_name+'"?<br><br><div class="button" id="shareprojects_accept" >accept</div> <div class="button"  id="shareprojects_decline" >decline</div>'
				});
				
				$('shareprojects_accept').addEvent('click', this.accept);
				$('shareprojects_decline').addEvent('click', this.decline);
			}
			*/
		}
	
		else if (fnc == 'summary.listProjects') {
			var plugin = this;
			
			// if shared projects are present add "shared projects" to collections list
			projects.each(function(e) {
				if($defined(e.shared)) {
					var thisproject = e.project_id; 
					var trashcan = $('sum_'+thisproject).getElement(".delproject").onclick = function() { plugin.delProject(thisproject); }
				}
			});
		}
	},
	
	delProject:function(project_id) {
		var floating = new Modal({
			windowtitle:'Delete Project',
			content:'This is a shared project. Do you want to remove it from your projects list?<br><br><br><div class="button" id="delProject_yes"> yes </div> <div class="button" id="delProject_no"> cancel </div><br><br>'
		});
		
		$('delProject_yes').onclick=function() {
			j.request('plugin_shareprojects:delProject',{'project_id':project_id},function(res) {
				if(res) { alert(res); } else {
					floating.close();
					projects.each(function(e,i) { if(e.project_id == project_id) { projects.splice(i,1); }});
					BC.broadcastMessage('_project_delete', project_id);
				}
			});
		}
		
		$('delProject_no').onclick=function() {
			floating.close();
		}
	},
	
	accept:function() {
		j.request('plugin_shareprojects:accept',{},function(res) { if(res) { alert(res); } else { 
			floater.close();
			j.request('getProjects','',function (response) {
				if(response != 'logout') {
					projects = JSON.decode(response);
					selectProject(user.addProject.project_id);
					delete user.addProject;
				} else {
					logout();
				}
			});
		}});
	},
	
	decline:function() {
		j.request('plugin_shareprojects:decline',{},function(res) { if(res) { alert(res); } else { floater.close(); }});
		delete user.addProject;
	}
}