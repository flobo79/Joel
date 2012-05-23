var ContextMenoo = new Class({
	options:{},
	name:'contextmenu',
	initialize: function (op) {
		this.options = op;
		//$('cm_block').onclick = changeTask;
		//$('cm_new').onclick = newTaskShortcut;
		
		this.cont=$('contextmenue');
		this.cont.setStyles({'opacity':0,'display':'block'});
		var _cont = this.cont;
		this.cont.addEvents({
			//'mouseleave':function (e) { _cont.fade('out'); }
		});
		document.addEvents({
			'click':this.hide.bind(this)
		});
	},
	
	attach:function() {
		$$(this.options.selector).each(function(el){
			el.removeEvent('contextmenu');	// remove event first
			el.addEvent(window.opera?'click':'contextmenu',function(e){
				if(window.opera && !e.ctrlKey) { return; }
				this.show(e);
			}.bind(this));
		},this);
	},
	
	hide: function(){ this.cont.fade('out'); },
	
	show: function(e) {
		e=new Event(e).stop();
		this.options.onclickevent(e.target);
		this.cont.style.display='block';
		var oCont = this.cont.getCoordinates();
		var size = {'height':window.getHeight(), 'width':window.getWidth(), 'top': window.getScrollTop(),'cW':oCont.width, 'cH':oCont.height};
		
		this.cont.setStyles({
			left: ((e.page.x + size.cW + this.options.pageOffset) > size.width ? (size.width - size.cW - this.options.pageOffset +5) : e.page.x-5),
			top: ((e.page.y - size.top + size.cH) > size.height && (e.page.y - size.top) > size.cH ? (e.page.y - size.cH +5) : e.page.y -5)	
		});
		
		this.options.fade ? this.cont.fade('in') : this.cont.setStyles({opacity:1});
	},
	
	onClick:function(e,args){
		args();
	},
	
	events:function(f,p) {
		if (f == 'tasklist.draw') {
			this.attach();
		}
	}
});
