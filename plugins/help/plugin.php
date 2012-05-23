<?php

/**
 * plugin help
 * 
 * @return
 * @package plugins
 */
class plugin_helpController extends Plugin {
	
	function plugin_help () {
		
	}
	
	function load($obj) {
		// obj to contain context
		echo file_get_contents(dirname(__FILE__).'/index.html');
	}
}

?>
