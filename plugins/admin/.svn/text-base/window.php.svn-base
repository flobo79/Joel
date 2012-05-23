<?php


?><style type="text/css">

#admin_col_left {
	float:left;
	width:170px;
}

#admin_col_right {
	float:left;
	
}

#admin_bottom {
	clear:both;
	width:100%;
	margin-top:15px;
}

#admin_modules {
	float:left;
	padding:0;
	width:140px;
}
.admin_new {
	background: #FFFFFF url(<?php echo $_SESSION['joel']->skinpath; ?>gfx/add.png) no-repeat scroll 0 0;
	line-height:16px;
	padding-left:20px;
}
#admin_modules li {
	color:#3E9DD9;
	cursor:pointer;
	list-style:none;
	margin:0;
	padding:5px 3px;
}
#admin_modules li.current {
	font-weight:bold;
}
#admin_userlist {
	padding:0;
}

#admin_userlist li {
	list-style:none;
	padding:3px 0;
}
.admin_panel {
	display:none;
}
.admin_display {
	display:block;
}
.admin_hidden {
	display:none;
}


</style>

<div id="admin_col_left">
	<ul id="admin_modules">
		<li class="current admin_user">User</li>
		<li class="admin_settings">Settings</li>
	</ul>	
</div>

<div id="admin_col_right">
	
	<div id="admin_user" class="admin_panel admin_display">
		<h3>User Manage</h3>
		
		<ul id="admin_userlist">
			<?php foreach($userlist as $user) {
				echo "<li>".$user['u_name']."</li>\n";
			}
			?>
			<li class="admin_new">create new</li>
		</ul>
		
	</div>
	
	<div id="admin_settings" class="admin_panel">
		<h3>Settings</h3>
		
	</div>
	
</div>

<div id="admin_bottom">
	<input type="button" class="button" id="br_submit" value="submit" /><br><br>
	<div id="br_status"></div>
</div>


<script type="text/javascript">
	var admin_userlist = JSON.decode('<?php echo json_encode($userlist); ?>');
	var class_admin = new Class({
		current:'admin_user',
		
		initialize:function() {
			var parent = this;
			
			// add the onclose method to the admin modal window
			plugins.plugin_admin.win.onclose = function() {
				delete admin;
			};
			
			$$('#admin_modules li').each(function(e) {
				e.onclick=function() {
					parent.show(e);
				};
			});
		},

		show:function(el) {
			if(el.get('name') != this.current) {
				this.current = el.get('name');
				$$('#admin_modules li.current')[0].removeClass('admin_current');
				el.addClass('current');
				
				$$('#admin_col_right>div').removeClass('admin_display');
				$(el.get('name')).addClass('admin_display');
			}
		},
		
		user:{
			current:false,
			select:function(id) {
				
			},
			find:function(id) {
				
			},
			save:function() {
				
			},
			remove:function() {
				
			}
		},
		
		submit_user:function() {
			
		},
		
		submit_user_result:function(obj) {
			var text = obj.responseText;
			var result = text.substr(0,text.indexOf(":"));
			var reason = text.substr(text.indexOf(":") + 1);
		
			if(result == 'error') {
				switch(reason) {
					case 'exist':
						$('submit_newuser_result').innerHTML = 'user with this email adress still exists';
						break;
					// if user requested password
					case 'sent':
						$('submit_newuser_result').innerHTML = 'password was sent to your email address';
						break;
						
					default:
						// reset field classes
						var fields = $('register_inputs').getElementsByTagName('input');
						fields = $A(fields);
						fields.each(function(e,myindex) {
							$(e.id).className='';
						});
		
						// mark missing or error fields red
						var fields = reason.substr(1).split("&");
						fields.each(function(e,myindex) {
							$(e).className+='redborder';
						});
						
						$('submit_newuser_result').innerHTML = 'please fill out the red bordered field(s)';
						
						break;
				}
				
			} else if (result == "success") {
				// reset field classes
				var fields = $('register_inputs').getElementsByTagName('input');
				fields = $A(fields);
				fields.each(function(e,myindex) {
					$(e).value='';
					$(e.id).className='';
				});
				
				$('submit_newuser_result').innerHTML = 'user successfully created, please check your e-mail inbox';
			}
		}
	});
	
</script>
