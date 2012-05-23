<?php

/**
 * plugin admin
 * 
 * @package plugins
 */
class plugin_adminController extends Plugin {
	
	var $permission = 2;
	
	function plugin_adminController () {
		
	}
	
	function load($obj) {
		// load user
		$users = new User();
		$userlist = $users->getAll();
		
		include(dirname(__FILE__)."/window.php");
	}
}

?>
