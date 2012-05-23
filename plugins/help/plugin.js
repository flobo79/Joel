/**
 * Help
 * 
 * adds an input form into the head of a tasklist to filter the tasklist
 * 
 * @author flobo <bosselmann@gmail.com>
 * 
 */
var plugin_help = {
	events:function(fnc, param) {
		if(fnc === 'topmenu.show') { 
			$('topmenu').innerHTML += '<div> | </div><div onclick="j.plugins.help.open();" id="help"> ? </div>';
		}
	},
	
	open:function () {	
			
    	var floater = new Modal({
			width:600,
			height:400,
			windowtitle:'Joel Help',
			content:''
		});
		var parent = this;
		j.request('plugin_help:load',{context:'start'}, function(response) { 
			$('floating_content').innerHTML = response;
			$$('#help_topics li').each(function(e) {
				e.onclick = function(){
					parent.showTopic(e);
				}
			});
		});
			  
		
	},
	
	showTopic:function(li) {
		$$('#help_topics li').each(function(e) { });
		$$('#help_contents>div').each(function(e,i) { e.style.display='none';} );
		$('help_'+li.get('title')).setStyles({display:'block',opacity:0}).fade('in');
	}
}