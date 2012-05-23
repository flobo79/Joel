/**
 * Bugreport Window
 * 
 * This plugins adds a bug report window to the timemachine
 * 
 * @author flobo <bosselmann@gmail.com>
 * 
 */
var plugin_bugreport = {
	
	events:function(fnc, param) {
		if(fnc === 'topmenu.show') { 
			$('topmenu').innerHTML += '<div> | </div><div onclick="j.plugins.bugreport.open();" style="background-image:url(plugins/bugreport/bug.gif); height:15px; width:15px; margin-top:6px;" id="bugreport"></div>';
		}
	},
	
	open:function () {	
		var win = new Modal({
			width:400,
			height:400,
			windowtitle:'Bugreport'
		});
		
		// gather client information
		var report = [
		"CodeName:", navigator.appCodeName,"<br>",
   		"MinorVersion:", navigator.appMinorVersion,"<br>",
   		"Name:", navigator.appName,"<br>",
   		"Version:", navigator.appVersion,"<br>",
  		"CookieEnabled:", navigator.cookieEnabled,"<br>",
   		"CPUClass:", navigator.cpuClass,"<br>",
   		"OnLine:", navigator.onLine,"<br>",
   		"Platform:", navigator.platform,"<br>",
  		"UA:", navigator.userAgent,"<br>",
  		"BrowserLanguage:", navigator.browserLanguage,"<br>",
  		"SystemLanguage:", navigator.systemLanguage,"<br>",
   		"UserLanguage:", navigator.userLanguage,"<br>"].join('');
		
		j.request('plugin_bugreport:loadWindow','',function(res) {
			$('floating_content').innerHTML = res;
			$('report_content_open').onclick=function () { $('report_content').show(); }
			$('report_content').innerHTML = report;
			
			
			$('br_submit').onclick=function() {
				if($('br_report').value != '') {
					$('br_status').innerHTML = 'sending ...';
					j.request('plugin_bugreport:submit',{
						type:$('br_type').value,
						message:$('br_report').value,
						report:report
					},
					function (res) {
						if(res) {
							$('br_status').innerHTML = res;
						} else {
							$('br_status').innerHTML = 'Thank you! ;-)';
							setTimeout(function() { win.close(); }, 1800);
						}
					});
				} else {
					$('br_status').innerHTML = 'You are not quite talky, please give us at least a few words.';
				}
			};
		});
	}, 
};