/**
 * 
 * Class Template provides template functions
 * @author flobo
 */

 var Template = new Class({
	name:'template',
	filesep: '##!##',  // separator to devide files from string
	tmpsep: '#!#',	  // separator to devide tmp name from temp content
	list:{},
	
	initialize:function() {
		j.waitonstartup.push('template.loaded');
	},
	
	
	/**
	 * loads the templates from the server
	 */
	load:function() {
		var _this = this;
		j.request('client:get-templates','',function (obj, str) {
			var parts = obj.split(_this.filesep);
			parts.each(function(e) {
				var thistemp = e.split(_this.tmpsep);
				_this.list[thistemp[0]] = thistemp[1];
			});
			
			j.BC.broadcastMessage('template.loaded');
		 });
	},
	
	show:function(template, box, callback) {
		if(this.list[template]) {
			box.innerHTML = this.list[template];
			if(callback) callback();
		}
	},
	
	events:function(func, param) {
		if(func === 'joel.bootstrap') {
			this.load();
		}
	}
});