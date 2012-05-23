/**
 * Plugin Invite
 */

var plugin_invite = {
	events:function(fnc,param) {
		if(fnc == 'joel.init') {
			// if an invitation string has been sent
			if(key = $get('invite')) {
				var template = '';
				// load invitation details from clipboard
				j.request('plugin_invite:loadInvitation',{key:key},function (res) {
					if(res) {
						j.request('plugin_invite:loadTemplate',"",function (html) {
							var html = new Element("div",{ 
								id:'invite_form',
								html:html,
								styles: {width:'100%',height:'200px',padding:'40px'},
								events:{
									submit:function(ev) {
										new Event(ev).stop();
										var result = {go:true,packet:{}};
										
										$('invite_form_form').getElements('input[type=text],input[type=password]').each(function(el) {
											el.removeClass('error');
											this.packet[el.id] = el.value;
											if(el.value.length < 3) {
												el.addClass('error');
												this.go = false;
											}
										}, result);
										
										if($('signup_password').value != $('signup_password2').value) {
											$('signup_password2').addClass('error');
											result.go = false;
										}
										
										result.packet.cb_key = $get('invite');
										
										if(result.go) {
											$('submit_result').innerHTML = 'Sending request to server...';
											
											j.request('plugin_invite:createAccount',{packet:result.packet},function(res) {
											 if(res) {
											 	$('submit_result').innerHTML = res;
											 } else {
											 	
											 	$('invite_form_form').fade('out');
											 	setTimeout(function() {
											 		$('invite_form_form').hide();
											 		$('invite_form_success').setStyles({opacity:0, display:'block'}).fade('in');
												 	$('gotologin').onclick=function() {
												 		$('u_login').value = $('signup_username').value;
												 		$('u_password').value = $('signup_password').value;
												 		
												 		user.loginForm();
												 	};
											 	},500);
											 }
											});
								
										} else {
											$('submit_result').innerHTML = 'Please check all fields are filled out correctly';
										}
									}
								}
							}).replaces($('loginbox'));
						});
					}
				});
			}
		} 
	
		else if(fnc == 'topmenu.show') {
			if(j.user.user_id) {
				var link = new Element('div',{
					id:'invite_link',
					html:' | invite',
				    events: {
				        'click': function () { 
				        	this.floater = new Modal({
								width:300,
								windowtitle:'Invite someone to use "Joel"',
								content:'Please enter the email address of the person you want to use "Joel":<br><br><input type="text" id="invite_with"><br> <div class="button" id="invite_send" >send invitation</div><br><br><div id="send_result" style="clear:both"></div>'
							});
							var floater = this.floater;
							
							$('invite_send').addEvent('click', function () {
								if(utils.String.isEmail($('invite_with').value)) {
									$('send_result').innerHTML = 'sending invitation...';
									j.request('plugin_invite:sendInvitation',{email:$('invite_with').value,project_id:project},function (res) {
										if(res) {
											$('send_result').innerHTML = res;
										} else {
											$('send_result').innerHTML = 'Invitation sent.';
											setTimeout(function() { floater.close(); },2000);
										}
									});
								} else {
									$('send_result').innerHTML = 'please enter a valid email address';
								}
							});
				        }
				     }
				});
				
				link.inject($('username'), 'after');
			}
		}
	}
};