
var Topmenu = new Class({
	name:'topmenu',
	
	initialize:function() {
		this.div = new Element('div',{id:'topmenu',html:'', styles:{display:'block',opacity:0}});
		console.log(this.div);
		this.div.inject($(document.body), 'top');
	},
	
	display:function () {
		var html = new Element('div', {
			id:"joel",
			html:PROJECT_TITLE+" | "
		});
	
		
		html.inject(this.div);
		
		j.BC.broadcastMessage('topmenu.show', this.div);
		//$('title').onclick=function() { j.plugins.help.open(); }
		
		this.div.fade('in');
		
	},
	
	events:function(type, param) {
		if (type === 'user.updated' || type === 'user.login' || type == 'joel.start') {
			this.display();
		}
	}
});