 /**
 * Shows the time in the Timer Clock
 * @author flobo
 */
 
var Stopwatch = Class({
	name:'stopwatch',
	
	initialize:function(){
		// cache the dom elements
		this.Stopwatch_div = $('Stopwatch_div');
		this.Stopwatch_button = $('Stopwatch_button');
		this.Stopwatch_elapsed = $('Stopwatch_elapsed');
		
		// applying effects
		this.Stopwatch_div.set('tween',{duration:200});
		
		this.started = '';
		this.running = false;
		this.timerInt = false;
		this.projectname = '';
		this.FX = new Fx.Tween(this.Stopwatch_div,{duration:200});
    },
    
	
	/** 
     * interval function for timer
     */
	update:function () {
		var now = (new Date().getTime())/1000;
		var running = now - this.started;
	    	now  = new Date(2000,1,1,0,0,running);
		//var minutes = running / 60;
	   
		var h=now.getHours();
	    var m=now.getMinutes();
	    var s=now.getSeconds();
		var d=running/86400;
		var str_time = sprintf("%d:%02d:%02d", h , m , s);
		
	    this.Stopwatch_elapsed.innerHTML = str_time;
		
		if(project == user.u_working_project && $type($(user.u_working_on+"_t_elapsed"))) {
			$(user.u_working_on+"_t_elapsed").value = s2h(running);
			if(h2s($(user.u_working_on+"_t_currest").value) < running) {
				$(user.u_working_on+"_t_currest").value = s2h(running);
				$(user.u_working_on+"_t_remain").innerHTML = s2h(0);
				str_time += ' (!)';
			} else {
				$(user.u_working_on+"_t_remain").innerHTML = s2h(h2s($(user.u_working_on+"_t_currest").value) - running);
			}
		}
		
		// show time in status bar
		document.title = str_time+" - "+((this.projectname+" :: "+user.u_working_description));
	},
	
	setFavicon:function(type) {
		if (!nil(type)) { type = ''; }
		var ico1 = new Element('link',{id:'ico1',href:skinpath+'gfx/favicon'+type+'.png', rel:'icon'});
		var ico2 = ico1.clone();
			ico2.id = 'ico2';
			ico2.rel = 'shortcut icon';
	
		ico1.replaces($('ico1'));
		ico2.replaces($('ico2'));
	},
	
	
	start:function(from) {
		/* start from previous timer */
		if(nil(from)) {
			this.started = user.u_working_since;
			
		/* start new timer */
		} else {
			var elapsed = currentTask.t_elapsed;
			var currenttime = Math.floor(new Date().getTime() / 1000);		// unix timestamp for now
		
			user.u_working_since = currenttime - elapsed;
			user.u_working_on = currentTask.task_id;
			user.u_working_project = project;
			user.u_working_description  = currentTask.t_description;
			
			// send action to server
			j.request('Stopwatch_start',{'task_id':currentTask.task_id }, function(res) { if (res != '') {
				alert(res);
			}});
			$('row_'+user.u_working_on).addClass('counting');
		}
		
		
		this.setFavicon('_stop');
		
		projects.each(function(e) { if(e.project_id == user.u_working_project) { this.projectname = e.p_name; return; }}, this);
		this.started = user.u_working_since;
		this.update();
		var _root = this;
		this.timerInt = setInterval(function() { _root.update(); },1000);
		
		// show and change button on new counting task
		this.Stopwatch_button.onclick = function () { _root.stop(); };
		this.Stopwatch_elapsed.onclick = function () { task_select(user.u_working_on, user.u_working_project); };
		
		this.Stopwatch_div.setStyles({display:'block', opacity:0});
		this.FX.start('opacity', 0, 1);
	},
	
	
	stop:function(withoutselecting, startnew) {
		// stop timer
		var _this = this;
		this.FX.start('opacity',1,0).chain(function() { _this.Stopwatch_div.setStyles({display:'none'}); });
		this.Stopwatch_button.onclick = function() { this.start(); };
		clearInterval(this.timerInt);
		this.setFavicon('');
		
		// calculate elapsed time to minutes
		var elapsed = (Number(new Date().getTime())/1000) - user.u_working_since;
		
		// send values to server
		j.request('Stopwatch_stop','', function(res) { if (res != '') {
			alert(res);
		}});
		
		// if taskproject is currently selected
		if(user.u_working_project == project) {
			var task = $t(user.u_working_on);
			if (task) {
				tasklist[task.index].t_currest = elapsed;
				tasklist[task.index].t_elapsed = elapsed;
				tasklist[task.index].t_remain = (tasklist[task.index].t_currest - elapsed);
				
				$('row_' + user.u_working_on).removeClass('counting');
				
				// if tasks remaining time is 0 set current estimation to the same time
				if (tasklist[task.index].t_remain < 0) {
					tasklist[task.index].t_remain = 0;
					$(task.task_id + "_t_remain").innerHTML = m2h(0);
					
					tasklist[task.index].t_currest = elapsed;
					updateTask(task, 't_currest', elapsed, m2h);
				}
			}
		}
		
		if(currentTask.task_id != user.u_working_on && !nil(withoutselecting)) {
			task_select(user.u_working_on, user.u_working_project);
		}
		
		// reset local user object
		user.u_working_since = false;
		user.u_working_on = false;
		user.u_working_project =false;
		
		// reset browser title
		document.title = PROJECT_TITLE;
		clearInterval(this.timerInt);
		
		if(nil(startnew)) {
			this.start();
		}
	},
	
	/** stops current timer without any more action */
	biff:function() {
		delete user.u_working_on;
		delete user.u_working_since;
		delete user.u_working_project;
		delete user.u_working_description;
		
		this.Stopwatch_div.fade('out');
		this.Stopwatch_button.onclick = function() { this.start(); };
		this.setFavicon('');
		document.title = PROJECT_TITLE;
		clearInterval(this.timerInt);
	},
	
	// the function to be called when clicked on the start/stop button of a task
	click:function(e) {
		var taskid = e.id.substr(3);
		
		// if user is working on no task
		if(user.u_working_on == 0 || !nil(user.u_working_on) || user.u_working_on === false) {
			this.start();
		
		// if this task is currently beeing worked on - stop it
		} else if (user.u_working_on == taskid) {
			this.stop();
			
		// if user is working on another task
		} else {
			if(user.u_working_project == project) {
				$('row_'+taskid).removeClass('counting');
			}
			
			// stop dont select task and start new timer
			this.stop(true, true);
		}
	},
	
	events:function(fnc,param) {
		if(fnc == 'joel.start') {
			// user is logged in
			if(j.user.user_id) {
				// if user is working on a task start the timer
				console.log(j.user.u_working_on);
				if(j.user.u_working_on != '0') {
					this.start(j.user.u_working_since);
				}
			}
		}
		
		else if (fnc == 'joel.logout') {
			clearInterval(this.timerInt);
			this.Stopwatch_div.setStyles({display:'none'});
			this.Stopwatch_elapsed.innerHTML = '0:00:00';
			this.Stopwatch_button.onclick = function () { return false; };
			this.setFavicon('');
			this.running = false;
		}
		
		else if(fnc == 'task.delete' || fnc == 'project.delete') {
			if(j.user.u_working_on == param) {
				this.biff();
			}
		} else if (fnc == 'tasklist.draw') {
			if (currentRow) {
				currentRow.addClass('counting');
			}
		}
	}
});

