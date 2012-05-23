/**
 * Plugin Taskdetails
 * 
 * This plugin adds extra details to a task, such as files and additional data
 * 
 * 
 */
var plugin_taskdetails = {
	id:false,
	html:false,
	
	openDetails:function(elem) {
		var id;
		var elem = elem;
		
		if((id = elem.id.substr(4)) != this.id) {
			elem.ondblclick = function() {}
			this.id = id;
			this.task = $t(this.id);
			this.html = new Element('div',{ 'id':'task_detailsbox', styles:{'opacity':0}});
			this.html.innerHTML = templates.taskdetails_details.process(this.task);
			this.task.div_icons.fade('out');
			
			var html = this.html;
			var task = this.task;
			var _parent = this;
			
			shortcuts.collection.add('return', function(ele) { return false } , {target:this.task.row});
			
			this.FX = new Fx.Morph(elem, {duration: 300, transition:Fx.Transitions.Quint.easeInOut}).start({height:'100px'}).chain(function() {
				task.div_icons.hide();
				elem.addClass('detailed');
				html.inject(elem.getElement('.c2'), 'after');
				_parent.listFiles(task);

				new Fx.Morph(html, {duration: 500}).start({'opacity':1});
			});
			
		} else {
			this.closeDetails();
		}
	},
	
	closeDetails:function () {
		var _parent = this;
		this.task.div_icons.show().fade('in');
		this.task.row.removeClass('detailed');
		this.task.row.ondblclick = function () { _parent.openDetails(this) };
		this.html.dispose();
		this.id = false;
		this.FX.start({height:'18'});
	},
	
	listFiles:function(task) {
		if(task.files.length > 0) {
			var box = $('details_fileslist_'+task.task_id);
			var task = task;
			var _parent = this;
			
			box.innerHTML = '';
			
			task.files.each(function(file,i) {
				var filename = file.name.length > 26 ? 
				(file.name.substr(0,13)+"..."+file.name.substr(-10)) : file.name;
				box.innerHTML += '<div class="file"><div class="taskdetails_file '+file.ext+'"></div><div class="del" id="'+escape(file.name)+'" ></div>'+filename+'</div>';
			});
			
			
			// adds del file button actions
			box.getElements('.del').each(function (el,i) { el.onclick=function () { 
				var filename = this.id;
				var div_del = this;
				
				request('taskdetails:delFile',{taskID:task.task_id, filename:filename}, function(res) {
					if(res) {
						alert(res);
					} else {
						// remove file from tasks filelist
						task.files.each(function(e,i) {
							if(e.name == filename) {
								delete tasklist[task.index].files[i], task.files[i];
							}
						});
						
						_parent.listIcons(task);
						div_del.getParent().dispose();
					}
				});
			}});
		}
	},
	
	listIcons:function(task) {
		var html = {html:[]};
		if(task.files.length) {
			task.files.each(function(file) {
				this.html.push(['<div class="taskdetails_file ',file.ext,'" title="',file.name,'" onclick="document.location.href=\'',escape('plugins/taskdetails/files/'+task.task_id+'/'+file.name),'\';"> </div>'].join(''));
			}, html);
			
			task.div_icons.innerHTML = html.html.join('');
		}
	},
	
	addActions:function(list) {
		var el_icons = new Element('div',{'class':'taskdetails_icons'});
		var _parent = this;
		list.each(function(task,i) {
			var thiscontainer = el_icons.clone();
			task.row.ondblclick = function () { _parent.openDetails(this); };
			thiscontainer.inject(task.row.getElement('.tl_right'), 'top');
			tasklist[task.index].div_icons = thiscontainer;
			this.listIcons(task);
		},this);
	},
	
	
	events:function(f,p) {
		if(f === 'task.select') {
			if(this.id && p != this.id) {
				this.closeDetails();
			}
		} else if (f === 'tasklist.draw') {
			// add doubleclick functionality to each row
			this.addActions(tasklist);
		}
	},
	
	
	_newTask:function(task) {
		this.addActions([task]);
	},
	
	_uploadFinished:function (id) {
		var _parent = this;
		var task = _parent.task;
		request('taskdetails:getFiles',{task_id:this.id}, function(files) {
			files = JSON.decode(files);
			tasklist[task.index].files = files;
			_parent.listFiles(task);
			_parent.listIcons(tasklist[task.index]);
		});
	}
}
