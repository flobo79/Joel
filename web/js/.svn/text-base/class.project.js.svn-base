

var Project = new Class({
	current:'summary',
	list:false,
	name:'summary',
	
	initialize:function() {

	},
	
	getAll:function(collection) {
		var _this = this;
		if(nil(j.user.user_id)) {
			j.request('project:get-all','',function (res) {
				_this.list = JSON.decode(res);
				j.BC.broadcastMessage('project.loaded');
			});
			
		} else {
			user.logout();
		}
	},
	
	get:function(id) {
		var p = false;
		this.list.each(function(e) {
			if(e.project_id == id) {
				p = e;
			}
		});
		return p;
	},
	
	create:function(title) {
		if(title !== '') {
			$('create_project_response').innerHTML = 'Creating project ...';
			j.request('project_create',{title:title,collection:currentcollection.id},function (response) {
				response = JSON.decode(response);		
				if(response.result == "success") {
					BC.broadcastMessage('modal.close');
					projects.push(response.project);
					project_select(response.project.project_id);
					
				} else {
					$('create_project_response').innerHTML = response.reason;
				}
			});
		} else {
			$('create_project_response').innerHTML = 'Please enter a Project name';
		}
	},


	/**
	 * Creates a new project
	 * 
	 * This function raises a promt window, if a string is entered a new
	 * project with a name like the given string will be created.
	 * 
	 * @param key Key Code of the key to be checked
	 * @return bool true or false
	 */
	create_show:function() {
		var floater = new Modal({
			windowtitle:'Create new Project',
			content:templates.new_timesheet.process()
		});
		floater.div.addEvent('keyup', function(ev) { if(ev.key=='enter') { project_create($('newProject_title').value); }});
		$('newproject_submit').addEvent('click', function () { project_create($('newProject_title').value); });
		$('newProject_title').focus();
	},
	
	/**
	 * Deletes a Project including all Tasks. This function calls the Server
	 * Method "deleteProject" in Class "project" and passes the parameter "id" to it.
	 * 
	 * @param id - Projects ID
	 */
	delete_show:function(id) {
		var floating = new Modal({
			windowtitle:'Delete Project',
			content:'Do you really want to delete this project and all its related tasks? Please be aware that other user might still be working on it.<br><br><br><div class="button" id="delProject_yes"> yes </div> <div class="button" id="delProject_no"> cancel </div><br><br>'
		});
		
		$('delProject_yes').onclick=function() {
			j.request('project_delete',{'id':id},function(obj) {
		  		floating.close();
				projects.each(function(e,i) { if(e.project_id == id) { delete(projects[i]); }});
				BC.broadcastMessage('project.delete',id);
				if(id == currentProject) { currentProject = false; }
				return;
			});
		};
		
		$('delProject_no').onclick=function() {
			floating.close();
		};
	},
	

	/**
	 * Selects a project by submitting the project reference id or 
	 * the key Strings "new" oder "summary". Selecting a project calls the Server method
	 * "project_select" in Class "project" and passes the project id via parameter "project".
	 * After using this funktion the Parameter "project" is globally available.
	 * 
	 * If the second Parameter "taskID" is set, the secified task will be selected after loading
	 * the Project
	 * 
	 * @param projectID
	 * @param taskID - optional
	 * 
	 */
	select:function (projectID, selTask) {	
		if(projectID === undefined) {
			projectID = "summary";
		}
		
		// reset task related keys and actions
		tasksselected = [];
		keysdown = [];
		currentId = false;
		currentTask=false;
		currentRow = false;
		lastField = false;
		currentField = false;
		
		// set global project ID
		project = projectID;
		
		if(project === 'summary') {
			j.summary.show();
		
		} else if(project === 'new') {
			newProject();
			
		} else {
			// bookmark project
			this.id = projectID;
			
			j.project.list.each(function(e,i) {
				if(e['project_id'] == projectID) {
					this.data = e;
					this.index = i;
					j.project.list[i].bookmarked = 1;
				}
			});
			
			j.BC.broadcastMessage('project.select', projectID);
		}
	},

	
	events:function(func, param) {
		if(func === 'joel.bootstrap') {
			if(j.user.user_id) {
				this.getAll();
			}
		}
	}
});
