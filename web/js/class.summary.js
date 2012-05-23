

/**
 * Summary Class to create a projects summary
 */
var Summary = new Class({
	currentcollection:false,
	name:'Summary',
	
	initialize:function() {

	},
	
	show:function() {
		// if not logged in - go to sleep
		if(!j.user.user_id) { return; }
		var _parent = this;

		$('contentbox').innerHTML = j.template.list.summary;
		$('summary').getElement('.canvas').setStyle('opacity',0);
		
		var box = $('summary');
		var box_collectionlist = box.getElement('.collectionlist');
		var collectionHTML = "<option >Select Timesheet Collection ...</option>\n";
		$A(j.collection.list).each(function(e,i) {
			var selected = typeof currentcollection != 'undefined' && currentcollection.id == e.collection_id ? ' selected' : '';
			collectionHTML += (['<option value="',e.collection_id,'"',selected,'>',e.c_name,'</option>'].join(''));
		});
		
		box_collectionlist.innerHTML = collectionHTML;
		box_collectionlist.addEvent('change',function(e){
			if(this.value == 'all') {
				_parent.listProjects('all');
			} else {
				j.collection.select(this.value);// = this.value;
				_parent.listProjects(this.value);
			}
		});
		
		
		
		var bu_newproject = $('bu_newproject');
		bu_newproject.set('morph', { duration: 100 });
		bu_newproject.addEvent('mouseover',function(e) { this.morph({width: 120 });});
		bu_newproject.addEvent('mouseout',function(e) { this.morph({width: 0 });});
		bu_newproject.onclick = j.project.create_show;
		
		var bu_new_collection = box.getElement('.new_collection');
		bu_new_collection.set('morph', { duration: 100 });
		bu_new_collection.addEvent('mouseover',function(e) { this.morph({width: 120 }); });
		bu_new_collection.addEvent('mouseout',function(e) { this.morph({width: 0 }); });
		bu_new_collection.addEvent('mousedown',function(e) {
			var collection = new collection();
			j.collection.create();
		});
		
		$('summary_list').set('morph', { duration: 200 });
		$('summary_list').setStyles({opacity:0.1});
		if (typeof currentcollection != 'undefined') { this.listProjects(); }
		
		// setup update Details Button
		$('summary').getElement('.content .details').getElement('.update').addEvent('click',function(e) { 
			var listener = {};
			listener.events = function(type,data) {
				if(type == 'collection.update') {
					BC.removeListener(this);
				}
			};
			BC.addListener(listener);
		
			currentcollection.update({
				c_name:$('c_name').value,
				c_info:$('c_info').value
			});
		});
		j.BC.broadcastMessage('summary.show');
		this.updateTabs();
	},
	
	
	updateTabs:function () {
		$('summary').getElements('.tabs div').each(function(e) {
			e.addEvent('click',function(e) {
				$('summary').getElements('.content>div').each(function(e) { e.style.display='none'; });
				$('summary').getElement('.content .'+e.target.className).style.display='block';
			});
		});
	},
	
	listProjects:function(opt) {
		var list = [];
		var all = typeof opt != 'undefined' && opt == 'all' ? true : false;
		var ListHTML = ['<li class="colhead">',
			'<div class="sumcol_1">Timesheet</div>',
			'<div class="sumcol_2">orig est.</div>',
			'<div class="sumcol_2">curr est.</div>',
			'<div class="sumcol_2">elapsed</div>',
			'<div class="sumcol_2">remaining</div>',
			'<div class="sumcol_2">progress </div>',
			'<div class="sumcol_2">EBS factor </div>',
		'</li>'].join('');
		
		list = j.project.list.filter(function(e) { return e.collection_id == opt; });
		
		list.each(function(e) {
			ListHTML += ['<li id="sum_'+(e.project_id)+'" >',
			'<div class="sumcol_1" style="cursor:pointer" onclick="j.project.select(\'',e.project_id,'\');">',e.p_name,'</div>',
			'<div class="sumcol_2">',s2h(e.origest),'</div>',
			'<div class="sumcol_2">',s2h(e.currest),'</div>',
			'<div class="sumcol_2">',s2h(e.elapsed),'</div>',
			'<div class="sumcol_2">',s2h(Number(e.remain)),'</div>',
			'<div class="sumcol_2">',(e.elapsed > 0 ? Math.round(100 / e.currest * e.elapsed) : 0),' %</div>',
			'<div class="sumcol_2">',(e.origest > 0 ? (Math.round((e.currest / e.origest)*10))/10 : '&nbsp'),'</div>',
			(j.user.u_type == 'admin' ? '<div class="delproject" onclick="project_delete_show('+e.project_id+')"> </div>' : ''),
			'</li>'].join('');
		});
		
		var FX = new Fx.Morph($('summary_list'),{duration:200});
		$('summary').getElement('.canvas').fade('in');
		
		if($('summary_list').getStyle('opacity') == 0.1) {
			$('summary_list').innerHTML = ListHTML; 
			$('summary_list').morph({ visibility:1, opacity:1 });
		} else {
			FX.start({'opacity':0})
			.chain(function() { $('summary_list').innerHTML = ListHTML; $('summary_list').morph({ visibility:1, opacity: 1 }); });
		}		
		
		if(all) {
			$('summary').getElement('.canvas .tabs .details').style.display = 'none';
		} else {
			// update cusomter details page
			$('c_name').value=j.collection.current.c_name;
			$('c_info').value=j.collection.current.c_info;
		}
		
		j.BC.broadcastMessage('summary.listProjects');
	},

	events:function(type, param) {
		if(type == 'joel.start') {
			this.show(); 
		}
		
		else if(type == 'project.select') {
			
		}
		
		else if(type == 'project.delete') {
			if(param == currentProject.project_id) {
				this.show();
			}
		}
	}
});