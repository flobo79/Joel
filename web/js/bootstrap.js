/**
*  	JOEL - painless task tracking
*  	http://code.google.com/p/joel
*  
*  	@author Florian Bosselmann <bosselmann@gmail.com>
*  
*/
//var	tasksselected = [];
/*
 * 
var	lastField = false;

var project = 'summary';					// initial project after login
var projects = false;						// projects object
var tasklist = {};							// tasklist object
var j = false;
var currentTask = false;
var currentProject = false;
var currentRow = false;						
var currentField = false;					
var lastTask = false;						// previously selected Task
var plugins = [];							// container for plugin classes
var clipboard = false;						// clipboard object
var user = false;							// container for user details
var templates = false;						// container for template html source
var keysdown = [];  						// container for pressed keys
var updateFieldInt = false;					// field update interval
var keyEvent = {};							// global object for last key event
var BC = false;

var api = 'api.php';						// URL zum API
var controlkey = Browser.Platform.mac ? 	// assign the apple key as control equivalent
	'meta' : 'control';
*/


// global namespace
j = false;	// 
DOM = window.DOM || {};

function bootstrap() {
	j = new Joel();
	j.bootstrap();
}
window.addEvent('domready', bootstrap);


document.addEvents({
	focus: function(){
		keysdown = [];
	},
	blur: function(){
		keysdown = [];
	}
});