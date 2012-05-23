/**
 * @author flobo
 */

 var Clipboard = new Class({
 	data:{},
 	initialize:function () {
		
	},
	
	add:function(name,data) {
		if(nil(data)) { this.data[name] = data; }
	},
	
	fetch:function(name,clear) {
		var data = false;
		if(nil(this.data[name])) {
			data = this.data[name];
			if(nil(clear)) { this.remove(name); }
		}
		return data;
	},
	remove:function (name) {
		if(!nil(this.data[name])) { return false; }
		delete this.data[name];
		return false;
	},
	clear:function() {
		this.data={};
	}
 });