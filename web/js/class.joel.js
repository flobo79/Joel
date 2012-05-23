/**
 * @author flobo
 */

 var Joel = new Class({
	
	//tasksselected:[],
	//lastField:false,
	//project:'summary',				// initial project after login
	//projects:[],						// projects object
	//tasklist:{},						// tasklist object
	//currentTask:false,
	//currentProject:false,
	//currentRow:false,						
	//currentField:false,					
	//collections:{},						// container for collections
	//lastTask:false,						// previously selected Task
	//clipboard:false,					// clipboard object
	//user:false,							// container for user details
	//templates:false,					// container for template html source
	//updateFieldInt:false,				// field update interval
	
	name:'joel',
	keysdown:[],  						// container for pressed keys
	keyEvent:{},						// global object for last key event
	api:'/api/json/',	// URL zum API
	plugins:{},							// container for plugin classes		
	controlkey:Browser.Platform.mac ? 'meta' : 'control', 	// assign the apple key as control equivalent
	interval:false,						// interval for keeping session alive
	BC:false,
	components:{},							// colleciton of inheriting classes
	waitonstartup:[],					// list of components to be waited for on startup
	
	initialize:function() {
	
	},
	
	bootstrap:function() {
		var _this = this;
		this.components = {},
		this.BC = new Broadcaster();
		this.BC.debug = false;
		this.BC.addListener(this);
		this.user_id = false;
		
		controlkey = Browser.Platform.mac ? 'meta' : 'control'; 	// assign the apple key as control equivalent
		project = 0;						// initial project after login
		document.title = PROJECT_TITLE;
		
		
		//TODO: move this to a field class
		//clearTimeout(updateFieldInt);		// field update interval  to be part of task class
		
		// HERE IS WHERE THE APPLICATION STARTS 
		// if this user is logged in...
		
		// this into a plugin
		this.context = new ContextMenoo({
			selector: '#tasklist_list',
			className: 'contextmenue',
			
			fade: true,
			pageOffset:-10,
			onclickevent:function (el) {
				selectField(el);
				updateContextMenu();
				if(currentTask.t_feature !== '') {
					$('cm_block_text').innerHTML = 'change to task';
				} else {
					$('cm_block_text').innerHTML = 'change to heading';
				}
			}
		});
		
		this.components['context'] = this.context;
		this.BC.addListener(this.context);
		
		
		// add all plugins to Broadcaster
		//todo: put this into a controller
		pluginslist.each(function(p) {
			if(p) {
				var pluginname = 'plugin_'+p;
				var plugin = eval(pluginname);
				_this.BC.addListener(plugin);
				_this.plugins[p] = plugin;
			}
		});
		
		this.componentAdd('User, Tablist, Tasklist, Summary, Clipboard, Shortcut, Topmenu, Template, Stopwatch, Collection, Project');
		var _this = this;
		
		// components can push their loading call to waitonstartup
		var loader = new Loader(this.waitonstartup, function() { 
			j.request('joel:bootstrap',{client:'web'}, function (response) {
				response = JSON.decode(response);
				if(response.user) {
					_this.user.adopt(response.user);
					_this.startup();
					
				} else {
					j.loginForm();
					j.BC.broadcastMessage('joel.start');
				}
				
				// talk to server to avoid him getting sleepy
				this.interval = window.setInterval("this.intervalFunction", 120000);
			});
		}, this.BC);
		
		window.addEvent('resize', this.windowResize);
		this.windowResize();
		j.BC.broadcastMessage('joel.bootstrap');
	},
	
	/** 
	 * called after successful login or existing session
	 * 
	 */
	startup:function() {
		var loader = new Loader(['project.loaded','collection.loaded'],function() {
			j.BC.broadcastMessage('joel.start');
		}, j.BC);
		
		j.collection.getAll();
		j.project.getAll();
		//j.user.load(this.user_id);
	},
	
	/**
	* Main interface function to call methods from the server.
	* If you supply the method name, it will call a method from the Joel class
	* If you supply a method name in this format "classname:methodname" the method will
	* be called in the given class name.
	* 
	* @param {String} method method to be called
	* @param {Object} parameter parameter to be passed to method
	* @param {Function} onComplete function to be called after response from server
	* @param {String} update id of dom tree element to be updated with response from server
	* @param {Bool} async async set "true" to process ajax request ansynchronously
	* @param {Bool} evalReturn set "true" to evaluate javascript contained in response
	*/
	request:function(method, parameter, callback, update, async, evalReturn) {
		var classname = method.split(":");
		var call = this.api;
		
		if (typeof classname[1] != "undefined") {
			call = call+classname[0] +"/"+classname[1];
		} else {
			call = call+"joel/"+classname[0];
		}
	
		var query = {
			url:call,
			method: 'post',
			data:parameter,
			encoding:'utf-8'
		};
		
		if (nil(async)) { query.async = async; }
		if (nil(update) && $type(update) == 'element') { query.update = update; }
		if (nil(evalReturn)) { query.evalScripts = true; }
		
		var _parent = this;
		var regex = new RegExp("^error:*");
		query.onFailure = function (res) { BC.broadcastMessage('j.request.error', res); };
		query.onComplete = function (res, text) {
			if(res == 'logout') {
				user.logout();
			
			} else if (regex.exec(res)) {
				BC.broadcastMessage('j.request.error',res);
				
			} else {
				if($type(callback) == 'function') {
					callback(res,text);
				}
			}
		};
		return new Request(query).send();
	},
	
	
	r:function(method, parameter, callback, update, async, evalReturn) {
		this.request(method, parameter, callback, update, async, evalReturn);
	},
	
	getHTML:function(call, params, target, callback) {
		
		call = this.api+call;
		
		var query = {
			url:call,
			method: 'post',
			//data:params,
			//encoding:'utf-8',
			//onSuccess:callback
		};
		
		return new Request.HTML({url:call}).post(params);
	},
	
	/**
	 * interval to check connection and keep session alive
	 */
	intervalFunction:function() {
		this.request('init', {'sessid':this.sessid},function(res) {
			var result = JSON.decode(res);
			if(!result.loggedin){ this.logout(); }
		});
	},
	
	/** 
	 * this function adds a component to joels component registry??
	 * 
	 */
	componentAdd:function(name) {
		
		if(typeof name == 'object') {
			
			console.log('register component:'+name.name);
			var c = j[name.name] = new name();
			j.BC.addListener(c);
			
		} else if(typeof name == 'string') {
			name = name.trim();
			
			
			if(!this.componentExist(name)) {
				if(name.split(',').length > 1) {
					name.split(',').each(function(e) { this.componentAdd(e); }, this);
					return;
				}
				
				var component = eval(name) || false;
				
				if (!component) {
					l('classname ' + name + " does not exist");
					return false;
				}
				
				name = name.toLowerCase();
				console.log('register component:'+name);
				var c = j[name] = new component();
				j.BC.addListener(c);
			}
		}
	},
	
	componentRemove:function(name) {
		
	},
	
	componentList:function() {
		for(e in j.components) {
			l(e);
		}
	},
	
	componentApply:function(action) {
		
	},
	
	componentExist:function(name) {
		name = name.toLowerCase();
		for(w in this.components) {
			if(w == name) return this.components[w];
		}
		return false;
	},
	
	windowResize:function() {
		j.BC.broadcastMessage('window.resize',[window.getWidth(), window.getHeight()]);	
	},
	

	events:function(fnc, param) {
		if (fnc == 'user.logout') {
			this.start();
		}
		else if (fnc == 'user.login') {
			//Asset.javascript('scripts.php?plugins=1');
			this.getCollections();
			this.start();
		}
	},
	
	
	loginForm:function () {
		j.template.show('login', $('contentbox'), function() {
			$('u_login').focus();
			$('contentbox').getElement('#login_form').addEvent('submit', function (ev) {
				ev = new Event(ev).stop();
				j.user.login();
			});
		});
	},
 });