/**
 * Class Tasklist
 * 
 * Represents the Tasklist
 * 
 * @author flobo
 */
var Tasklist = new Class({
	list:[],
	selected:[],
	name:'tasklist',
	currentTask:false,
	lastTask:false,
	tasklist_html:false,
	
	initialize:function() {
		
	},
	
	
	/**
	 * Loads the Tasklist from the server and displays it
	 */
	load:function(id, selTask) {
		this.tasklist_html = $('tasklist_list');
		
		j.template.show('tasktable', $('contentbox'));
		$('tasklist_list').innerHTML = '<div id="loading_status" style="margin:5px;">Loading ...</div>';
		$('tasklist_top').style.display = 'block';

		
		var _this = this;
		//j.BC.broadcastMessage('tasklist.load');
		
		// loadTasklist
		j.request ('tasklist:get-list', {'project':project}, function (res) {
			_this.list = JSON.decode(res);
			
			var newlist = new Element('div',{id:'tasklist_list'});
			_this.list.each(function(e,i){
				var task = new Task(e);
				task.index = i;
				task.row = _this.newTaskRow(e);
				task.row.store('task', task);
				newlist.adopt(task.row);
				_this.list[i] = task;
			});
			
			// enable tabbing through fields 
			newlist.getElements('textarea,input').each(function(e){
				e.addEvents({
					blur:function(ev) { console.log(this); this.parentNode.retrieve('task').saveField(ev); },
					focus:function(ev) { _this.selectTask(this.parentNode.retrieve('task').task_id, ev); }
				});
			});
			
			// inject the new tasklist by replacing the old one
			this.tasklist_html = newlist.replaces(this.tasklist_html);
			
			if (typeof selTask != 'undefined') {
				//_this.selectTask(selTask);
				
			} else if(j.user.u_working_project == j.project.project_id && j.user.u_working_on) {
				//_this.selectTask(j.user.u_working_on);
			}
			

			//tasklist_updateSummary();
			var mySortables = new Sortables('tasklist_list', {
				onStart:function() {
					_this.oldorder = (mySortables.serialize(1,function(element, index) {
				    	return element.id.replace('row_','');
					}).join(','));
				},
				
				handle:'.sort',
				onSort:function () {
					(mySortables.serialize(1, function(e, i) {
						var id = e.id.substr(4);
						if(id == currentId) {
							tasklist.splice(currentTask.index,1);
							currentTask.index = i;
							tasklist.splice(i,0,currentTask);
							tasklist_updateSummary();
						}
				    	return id;
					}));
				},
				
				onComplete:function (foo) {
					var newIndex = 0;
					var newOrder = (mySortables.serialize(1, function(e, i) {
						var id = e.id.substr(4);
						if(id == currentId) {
							tasklist.splice(currentTask.index,1);
							currentTask.index = i;
							tasklist.splice(i, 0, currentTask);
							tasklist.updateSummary();
						}
				    	return id;
					}));
					
					if(newOrder != _this.oldorder) {	
						j.request('tasklist_updateOrder',{'order':newOrder.join(',')});
					}
					
					_this.list.each(function(e,i) { _this.list[i].index=i;});
				}
			});
			
			$('tasklist').setStyles({'opacity': 1, display:'block'});
			
			j.BC.broadcastMessage('tasklist.display');
		});
	},
	
	addTask:function() {
		var _this = this;
		j.r('task:create',{},function(data) {
			var newTask = new Task(data);
			newTask.row = _this.newTaskRow(data);
			newTask.row.store('task', newTask);
			
			_this.list.splice(_this.currentTask.index, 0, newTask);
			
			newTask.row.inject('row_' + _this.currentTask.task_id, 'after');
		});
	},

	/**
	 * helper function to find a task
	 * 
	 * @param key - id or key name of task value
	 * @param value - optional to find a key value if key is used
	 */
	find:function (key, value) {
		if(this.list.length > 0) {
			var obj = {'task':false};
			var _this = this;
			if(typeof value != 'undefined') {
				this.list.each(function(e,i) {
					if(e[key] == value) {
						this.task = e;
						return;
					}
				},obj);
			} else {
				this.list.each(function(e,i) {
					if(e.task_id == key) {
						this.task = e;
						return;
					}
				},obj);
			}
			return obj.task;
		}
		return false;
	},
	
	
	selectTask:function(id, event) {
		
		if(this.currentTask) {
			this.previousTask = this.currentTask;
		}
		
		this.currentTask = this.find(id);
		
		if(this.currentTask.index = this.list.length+1) {
			this.addTask();
		}
		
		var _this = this;
		
		if(typeof event != 'undefined') {
			// if control key is pressed add task to selected tasks
			if (event[controlkey]) {
				if(!this.selected.contains(this.currentTask)) {
					this.selected.push(this.currentTask);
					console.log('added task to selected list, new length: '+this.selected.length);
				}
			// with shift key pressed select all tasks between current and previous
			} else if(event.shift && this.previousTask && this.list.length > 0) {
				var select = false;
				var selfrom = false;
				var seltill = false;
				
				
				if(Number(this.currentTask.index) > Number(this.list[0].index)) {
					selfrom = this.list[0].task_id;
					seltill = this.currentTask.task_id;
				} else {
					selfrom = this.currentTask.task_id;
					seltill = this.list[0].task_id;
				}
				
				this.list.each(function(e,i) {
					if(e.task_id == selfrom) { select = true; }
					if(select) { _this.selected.push(e); }
					if(e.task_id == seltill) { select=false; return; }
				});
				
			} else {
				this.selected = [this.currentTask];
			}
		} else {
			this.selected = [this.currentTask];
		}
		
		this.list.each(function(task) {
			if(_this.selected.contains(task)) {
				task.row.addClass('selected');
			} else {
				task.row.removeClass('selected');
			}
		});
	},
	
	
	newTaskRow:function(rowdata) {
		
		rowdata._MODIFIERS = {
			s2h:s2h,
			drawPrio:drawPrio,
			htmlentities:htmlentities
		};
		rowdata.asNode = true;
		
		return new Element('div',{
			'id':'row_'+rowdata.task_id,
			'class':['t_row',(rowdata.t_feature !== '' ? ' block':''),
							(rowdata.t_description==='' ? ' empty' : ''),
							(rowdata.t_currest != 0 && rowdata.t_remain == 0 ? ' ready':''),
							(j.user.u_working_on == rowdata.task_id ? ' counting':'')].join(''),
			'html': j.template.list.tasklist_entry.process(rowdata),
			
			'events':{
				'mouseover': function () { this.addClass('hover'); },
				'mouseout': function () { this.removeClass('hover'); }
			}
		});
	},
	

	
	resize:function() {
		if ($('tasklist_list')) {
			setTimeout(function() {
				var dim = $('tasklist_list').getCoordinates();
				$('tasklist_list').style.height = String(Number(window.getHeight()) - Number(dim.top) - 20) + 'px';
			},100);
		}
	},
	
	
	events:function(ev,para) {
		if(ev == 'project.select') {
			this.load(para);
		}
	}
});


