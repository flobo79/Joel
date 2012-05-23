/**
 * Collection
 * 
 * @param {Object} id
 */
var Collection = new Class({
	list:false,
	name:'collection',
	current:false,
	
	initialize:function(id) {

	},
	
	select:function(id) {
		if(nil(id)) {
			var _this = this;
			this.current = id;
			this.list.each(function(e, i) { if(e.collection_id == id) { _this.current = e; }});
		}
	},
	
	
	getAll:function() {
		var _this = this;
		j.request('collection:get-all',{},function(res) {
			_this.list =  JSON.decode(res);
			j.BC.broadcastMessage('collection.loaded');
		});
	},
	
	update:function(obj) {
		obj.collection_id = this.id;
		j.request('collection_update',obj,function(res){
			if(!res) {
				
			}
		});
		
		// update collectionlist
	},
	
	create:function() {
		var m = new Modal({
			windowtitle:'Create New CTimesheet Collection',
			content:templates.new_collection
		});
		
		$('newcollection_submit').addEvent('click',function(e){
			if($('new_c_name').value !== '') {
				
				$('summary').getElements('.content>div').each(function(e) { e.style.display='none'; });
				$('summary').getElement('.content .details').style.display='block';
				
				j.request('collection_create',{name:$('new_c_name').value},function(res) {
					var result = JSON.decode(res);
					if(result.result == 'failure') {
						$('response').innerHTML = result.reason;
					} else {
						collectionlist.list.push(result.data);
						this.data = result.data;
						this.id = this.data.collection_id;
						currentcollection = new collection(this.id);
						summary.listProjects();
						m.close();
					}
				});
			} else {
				$('response').innerHTML = 'Please enter a Name';
			}
		});
	},
	

	obliterate:function() {
		if($('collection_delete_confirm').checked) {
			j.request('collection_delete',{id:currentcollection},function(res) {
				
			});
		} else {
			alert('If you are sure you want to delete the Collection, tick the above checkbox first.');
		}
	},

	events:function(fnc, data) {
		
	}
});
