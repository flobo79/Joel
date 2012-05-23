/**
 * Class Loader 
 * 
 * to be aattached the the global Broacaster, you
 * can assign a list of events and a callback function
 * that is being executed as soon as all events from 
 * the event list have been broadcasted.
 * 
 * @param toLoad Array - list of event names
 * @param callback - method to execute after all events have been broadcasted
 * @example: var myLoader = new Loader(['login','userdata'], loadInterface);
 * 
 */
var Loader = new Class({
	toLoad:[],
	Broadcast:{},
	onComplete:function(){},
	name:'loader',

	initialize:function(toLoad, callback, Broadcaster) {
		this.Broadcast = Broadcaster;
		this.Broadcast.addListener(this);
		this.toLoad = toLoad;
		this.onComplete = callback;
	},
	
	events:function(type, param) {
		if(this.toLoad.contains(type)) {
			this.toLoad.erase(type);	
		}
		
		if(this.toLoad.length === 0) {
			this.Broadcast.removeListener(this);
			this.onComplete();
		}
	}
});
