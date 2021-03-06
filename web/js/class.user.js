/**
 * User Class representing a Joel User
 * 
 */
var User = Class({
	user_id:false,
	u_type:false,
	u_name:'user',
	u_skin:'blue',
	u_locale: 'en_EN',
	u_working_on: false,
	u_working_since: false,
	u_bookmarks:[],
	
	initialize:function(base) {
		
	},
	
	adopt:function(data) {
		var required = ['user_id', 'u_name', 'u_email', 'u_type', 'u_bookmarks', 'u_working_on', 'u_working_since'];
		for(e in data) {
			if(required.contains(e)) {
				this[e] = data[e];
			}
		}
	},
	
	load:function(id) {
		var _this = this;
		
		j.request('user:load',{user_id:id},function (response) {	
			
			var result = JSON.decode(response);
			
			if(typeof result == 'object') {
				_this.adopt(result);
			}
			
			j.BC.broadcastMessage('user.loaded');
		});
	},
	
	edit_show:function() {
		var floater = new Modal({
			height:320,
			content:'<div class="loading">loading user data</div>',
			windowtitle:'Settings'
		});

		$('floating_content').innerHTML = templates.useredit.process(this);
	},
	
	save:function() {
		$('response').innerHTML = '';
		packet = {};
		
		// check if password is set
		if($('p_password_new').value != '') {
			if ($('p_password_new').value != $('p_password_retype').value) {
				$('response').innerHTML = 'The new password and the retyped password dont match.';
				return;
			} else {
				packet.password = hex_md5($('p_password_new').value+"Das weiss ich nicht");
			}
		}
		
		if($('p_login').value === '') {
			$('response').innerHTML = 'Please enter a username containing at least 6 characters and 2 numbers.';
			return;
		}
		if($('p_name').value === '') {
			$('response').innerHTML = 'Please enter your name.';
			return;
		}
		
		packet.name = $('p_name').value;
		packet.email = $('p_email').value;
		packet.login = $('p_login').value;
		
		var _this = this;
		j.request('user_update',packet,function(obj) {
			if(!obj) {
				_this.u_name = packet.name;
				_this.u_email = packet.email;
				_this.u_login = packet.login;
				
				$('response').innerHTML = 'saved.';
				BC.broadcastMessage('user.updated');
			}
		});
	},
	
	
	
	/**
	 * login function expects to be called on the login template and to find 
	 * the form named "login_form" containing fields "u_login" "u_password" and "loginbutton"
	 * as well as the container "loginresult" to display the login result.
	 * 
	 */
	 login:function () {
		$('loginbutton').disabled = true;
		$('u_login').disabled = true;
		$('u_password').disabled = true;
		$('loginresult').innerHTML = 'logging in';
	    
	    var _this = this;
	    
		j.request('auth:login', {
				'login':$('u_login').value,
				'password': (this.md5 ? hex_md5($('u_password').value+joel.salt) : $('u_password').value)
			},
			function(obj) {
				var response = JSON.decode(obj);
				if(response.result == "success") {
					$('contentbox').removeEvent(window.ie ? 'keydown' : 'keypress');
					$('loginresult').innerHTML = "success...";
					
					// skip the load process
					for(e in response.userdata) {
						_this[e] = response.userdata[e];
					}
					
					_this.loggedIn = true;
					j.BC.broadcastMessage('user.login');
					
				} else {
					$('loginbutton').disabled = false;
					$('u_login').disabled = false;
					$('u_password').disabled = false;
					$('u_password').value = '';
					$('loginresult').innerHTML = response.reason;
				}
			});
	 },
	 
	 
	/**
	 * logout function
	 * 
	 * This funktion resets all global objects and variables
	 */
	 logout:function() {
		this.floater.close();
		this.user_id = false;
		this.u_type = false;
		this.loggedIn = false;
		j.request('logout');
		BC.broadcastMessage('user.logout');
		this.loginForm();
	},
	
	
	/**
	 * brings up the logout floating window
	 */
	logout_show:function () {
		this.floater = new Modal({
			windowtitle:'Logout?',
			height:110,
			width:250,
			content:'<div class="button" onclick="user.logout();"> yes </div> <div class="button" onclick="user.floater.close();"> no </div>'
		});
	},
	

	events:function(type, param) {
		if(type == 'topmenu.show') {
			if(j.user.user_id != false) {
				new Element('div', {id:"username", events:{click:j.user.edit}, html:j.user.u_name}).inject($('joel'),'after');
				new Element('div', {id:"bu_logout", events:{click:j.user.logout_show}, html:'| logout'}).inject(param);
			} else {
				new Element('div', {html:'login'}).inject($('joel'), 'after');
			}
			//param.inject(html, 'inside');
		}
	}
});
