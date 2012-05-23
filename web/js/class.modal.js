/**
 * Class Modal
 * 
 * This class provides functions to create and manage a floating modal window
 * 
 * @requires Mootools 1.2
 * @author flobo
 */
var Modal = Class({
	name:'modal',
	windowtitle:'',
	width:400,
	height:200,
	content: '<div class="loading">loading...</div>',
	div:false,
	steady:false,	// do not close after logout
	Broadcast:false,
	
	initialize:function(settings) {
		for(e in settings) {
			this[e] = settings[e];
		}
		
		this.bg = new Element('div',{
			id:'modal_bg',
			styles:{
				height:'100%',
				width:'100%',
				position:'fixed',
				backgroundColor:'#000',
				opacity:0,
				top:0,
				bottom:0,
				margin:0,
				zIndex:300
			}
		}).inject(document.body,'top');
		
		this.winFX = new Fx.Morph(this.bg, {duration: 200, transition:Fx.Transitions.Quint.easeOut}).start({opacity:0.2});
		this.bg.setStyles({opacity:0.2});
		
		var calctop = (Number(window.getHeight()) - Number(this.height)) / 3 * 1; 
		var postop = calctop < 10 ? 10 : calctop;
		
		this.div = new Element('div', {
			id:'floating_window',
			styles:{
				marginLeft:(this.width/2)*-1+"px",
				backgroundColor:'#FFF',
				border:'1px solid #CCC',
				width:this.width+"px",
				position:'fixed !important',
				marginTop:postop+'px',
				padding:'15px',
				top:0,
				height:this.height+'px',
				zIndex:305
			},
			'html':['<div id="floating_close">  </div>',
				'	<div id="window_content">',
				'	<div style="height:',Number(this.height)-60,'px;">',
				'	<div id="floating_windowtitle">',(this.windowtitle),'</div>',
				'	<div id="floating_content">',this.content,'</div></div></div>'].join('')
		}).inject(document.body,'bottom');
		$('floating_close').onclick = function() { j.BC.broadcastMessage('_close_modal'); }
		
		this.windowtitle = $('floating_windowtitle');
		this.content = $('floating_content');
		
		if(nil(RUZEE) && nil(RUZEE.ShadedBorder)) {
			var shadowedBorder = RUZEE.ShadedBorder.create({ corner:15, border:8, borderOpacity:0.3 }).render(this.div);
			this.div.style.border='none';
		}
		
		this.div.style.position='fixed';
		
		j.BC.addListener(this);
	},
	
	close:function() {
		j.BC.removeListener(this);
		var parent = this;
		this.div.dispose();
		this.winFX.start({
			'opacity': 0
		}).chain(function () { 
			delete parent; 
			if($('modal_bg')) {
				if(typeof parent.onclose == 'function') {
					parent.onclose();
				}
				$('modal_bg').dispose(); 
			}
		});
	},
	
	/* if attached to a Broadcaster listen to these events */
	events:function(fnc, param) {
		switch(fnc) {
			case 'joel.logout':
				this.close();
				break;
			case 'key.escape':
				this.close();
				break;
			case '_close_modal':
				this.close();
				break;
		} 	
	}
});