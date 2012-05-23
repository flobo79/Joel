var Task = new Class({
	name:'task',
	
	initialize:function(data) {
		for(e in data) {
			this[e] = data[e];
		}
	},
	
	insert_date:function() {
	
	},
	
	insert_time:function() {
		
	},
	
	
	/** 
	 * this method is called by pressing ctrl-+ and adds
	 * 5 extra minutes to the done field
	 */
	plus5:function() {
		if (nil(currentTask.task_id)) {
			$(currentTask.task_id+'_t_elapsed').value = s2h(h2s($(currentTask.task_id+'_t_elapsed').value)+300);
			updateField($(currentTask.task_id+'_t_elapsed'));
		}
	},
	
	/** 
	 * this method is called by pressing ctrl-- and removes
	 * 5 minutes from the done field
	 */
	minus5:function() {
		if (nil(currentTask.task_id) && currentTask.t_elapsed > 300) {
			$(currentTask.task_id+'_t_elapsed').value = s2h(h2s($(currentTask.task_id+'_t_elapsed').value)-300);
			updateField($(currentTask.task_id+'_t_elapsed'));
		}
	},
	
	
	
	setReady:function (e) {
		var taskid = e.id.substr(3);
		task_select(taskid);
		
		if(currentTask.t_elapsed === 0) {
			updateTask(currentTask, 't_elapsed', (currentTask.t_currest > 61 ? currentTask.t_currest : 61), s2h);
			updateField($(taskid+'_t_elapsed'));
		}
		
		$(taskid+'_t_currest').value = s2h(currentTask.t_elapsed);
		updateField($(taskid+'_t_currest'));
		
		tasklist_updateSummary();
	},
	

	events:function(ev, param) {
		
	},
	
	update:function(ev) {
		console.log('update '+this.id);
		
		
	},
	
	/**  SAVES A SINGLE FIELD */
	saveField:function(ev) {
		var fieldname = ev.target.id.substr(ev.target.id.indexOf('_')+1);

		// Do some smartness
		// call existing field-update functions
		if($type(this['updateField_'+fieldname]) == 'function') {
			this['updateField_'+fieldname](ev.target);
		}
		
		// if value has changed save it to server
		if(ev.target.value != this[fieldname]) {
			j.r('task:updateField',{task_id:this.task_id, value:ev.target.value, field:fieldname});
			this[fieldname] = ev.target.value;
		}
	},
	

	/**
	 * updates a field in a task and its corresponding html element
	 */
	updateField:function(fieldname, value) {
		this[fieldname] = value;
		this.row.getElement('#'+this.task_id+'_'+fieldname).value = value;
	},
	
	/**
	 * updates description field
	 */
	updateField_t_description:function(element){
		if(this.row.hasClass('empty') && element.value !== '') { this.row.removeClass('empty'); }
		if(!this.row.hasClass('empty') && element.value === '') { this.row.addClass('empty'); }
	},
	
	updateField_t_origest:function (element) {
		var value = h2s(element.value);
		
		if(value > this.t_currest) {
			this.updateField('t_currest', s2h(value));
		}
		this.updateField('t_origest', s2h(value));
		
		j.tasklist.updateSummary();
	},

	updateField_t_currest:function (element) {
		value = h2s(value);
		if(value < thistask.t_elapsed) { value = thistask.t_elapsed; }
		updateTask(thistask, 't_currest', value, s2h);
		
		// if no time remaining, mark row as ready
		(tasklist[thistask.index].t_remain === 0 && tasklist[thistask.index].t_currest !== 0) ? $('row_'+thistask.task_id).addClass('ready') : $('row_'+thistask.task_id).removeClass('ready');
		
		// calculate remaining time
		thistask.t_remain = thistask.t_currest - thistask.t_elapsed;
		updateTask(thistask,'t_remain',thistask.t_remain,s2h);
		tasklist_updateSummary();
		return value;
	},


	updateField_t_elapsed:function (thistask, field, value) {
		value = h2s(value);
		// if remaining is negative - increase the currest
		updateTask(thistask,'t_elapsed', value, s2h);
		
		if((tasklist[thistask.index].t_currest - value) < 0) {
			updateTask(thistask, 't_currest', value, s2h);
			$(thistask.task_id+'_t_currest').value = s2h(value);
			updateField($(thistask.task_id+'_t_currest'));
		}
		
		// if no time remaining, mark row as ready
		(tasklist[thistask.index].t_remain === 0 && tasklist[thistask.index].t_currest !== 0) ? $('row_'+thistask.task_id).addClass('ready') : $('row_'+thistask.task_id).removeClass('ready');
		
		updateTask(thistask,'t_remain',(thistask.t_currest - thistask.t_elapsed), s2h);
		tasklist_updateSummary();
		return value;
	}
});
